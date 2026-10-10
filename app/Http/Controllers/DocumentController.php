<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAuditLog;
use App\Models\DocumentSignature;
use App\Models\Signature;
use App\Models\User;
use App\Services\SignedPdfBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfReader\PdfReader;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * List the authenticated user's documents.
     */
    public function index(Request $request): View
    {
        return view('documents.index', [
            'documents' => $request->user()->documents()->latest()->get(),
            'hasSignature' => Signature::isUsableFor($request->user()->id),
        ]);
    }

    /**
     * Show the upload form.
     */
    public function create(): View
    {
        return view('documents.create');
    }

    /**
     * Store a newly uploaded PDF document and read its page dimensions. If the uploader
     * marks it as already signed (e.g. physically signed and scanned, or signed with
     * another tool), it's filed directly into the vault instead of going through the
     * in-app placement/signing step — there's no in-app signature to place since the
     * signature is already part of the document's visual content.
     *
     * The file's hash is always checked against every already-signed document's
     * content_hash, regardless of the "already signed" checkbox: this is what stops a
     * signed PDF that was downloaded from OtoSign from being re-uploaded and signed a
     * second time — its bytes provably already match a sealed, immutable document.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $user = $request->user();
        $uploaded = $request->file('file');
        $path = $uploaded->store('documents/'.$user->id, 'local');
        $contentHash = hash('sha256', Storage::disk('local')->get($path));

        $markedAlreadySigned = $request->boolean('already_signed');
        $matchesExistingSignedDocument = Document::where('content_hash', $contentHash)->exists();
        $alreadySigned = $markedAlreadySigned || $matchesExistingSignedDocument;

        [$pageCount, $pages] = $this->readPdfPages(Storage::disk('local')->path($path));

        $document = Document::create([
            'user_id' => $user->id,
            'original_name' => $uploaded->getClientOriginalName(),
            'path' => $path,
            'content_hash' => $alreadySigned ? $contentHash : null,
            'mime_type' => 'application/pdf',
            'page_count' => $pageCount,
            'pages' => $pages,
            'status' => $alreadySigned ? 'signed' : 'pending',
            'uploaded_signed' => $alreadySigned,
        ]);

        DocumentAuditLog::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'action' => $alreadySigned ? 'uploaded_signed' : 'uploaded',
        ]);

        if ($matchesExistingSignedDocument && ! $markedAlreadySigned) {
            return redirect()->route('vault.show', $document)
                ->with('status', 'document-matches-existing-signed');
        }

        return redirect()->route('documents.show', $document);
    }

    /**
     * Show a document: its dimensions, a preview, and the proposed signature zone.
     * Signed documents live in the password-protected vault instead.
     */
    public function show(Request $request, Document $document): View|RedirectResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);

        if ($document->isSigned()) {
            return redirect()->route('vault.show', $document);
        }

        $document->load(['auditLogs' => fn ($query) => $query->latest()->limit(20)]);

        return view('documents.show', [
            'document' => $document,
            'hasSignature' => Signature::isUsableFor($request->user()->id),
            'suggestion' => $this->suggestedPlacement($document),
        ]);
    }

    /**
     * Stream the raw PDF file to its owner only, while the document is still pending.
     * Once signed, the file can only be reached through the password-protected vault.
     */
    public function file(Request $request, Document $document): StreamedResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        abort_if($document->isSigned(), 403);

        return Storage::disk('local')->response($document->path, $document->original_name, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Apply the authenticated user's signature to the document. A document can only be signed once.
     */
    public function sign(Request $request, Document $document): RedirectResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);

        if ($document->isSigned()) {
            return back()->withErrors(['document' => __('Ce document a déjà été signé et ne peut plus être modifié.')]);
        }

        $validated = $request->validate([
            'page_number' => ['required', 'integer', 'min:1', 'max:'.$document->page_count],
            'pos_x' => ['required', 'numeric', 'between:0,1'],
            'pos_y' => ['required', 'numeric', 'between:0,1'],
            'box_width' => ['required', 'numeric', 'between:0.01,1'],
            'box_height' => ['required', 'numeric', 'between:0.01,1'],
            'include_caption' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();
        $signature = Signature::where('user_id', $user->id)->first();

        if (! $signature || $signature->isBlank()) {
            return redirect()->route('signature.show')
                ->withErrors(['signature' => __('Vous devez d\'abord enregistrer votre signature.')]);
        }

        $result = $this->applySignature(
            $document,
            $user,
            $signature,
            $validated['page_number'],
            $validated['pos_x'],
            $validated['pos_y'],
            $validated['box_width'],
            $validated['box_height'],
            $request->boolean('include_caption', true),
        );

        if (! $result) {
            return back()->withErrors(['document' => __('Ce document a déjà été signé et ne peut plus être modifié.')]);
        }

        return redirect()->route('documents.show', $document)->with('status', 'document-signed');
    }

    /**
     * Sign several pending documents at once, applying the platform's suggested signature
     * zone (last page, bottom-right) to each without a manual adjustment step — the
     * fast path for signing a batch. Documents can still be opened individually
     * afterwards if a particular one needs a different placement (before it's signed).
     */
    public function signMany(Request $request): RedirectResponse
    {
        $request->validate([
            'document_ids' => ['required', 'array', 'min:1'],
            'document_ids.*' => ['integer'],
        ]);

        $user = $request->user();
        $signature = Signature::where('user_id', $user->id)->first();

        if (! $signature || $signature->isBlank()) {
            return redirect()->route('signature.show')
                ->withErrors(['signature' => __('Vous devez d\'abord enregistrer votre signature.')]);
        }

        $documents = Document::where('user_id', $user->id)
            ->whereIn('id', $request->input('document_ids'))
            ->where('status', 'pending')
            ->get();

        $signedCount = 0;

        foreach ($documents as $document) {
            $placement = $this->suggestedPlacement($document);

            $result = $this->applySignature(
                $document,
                $user,
                $signature,
                $placement['page'],
                $placement['x'],
                $placement['y'],
                $placement['width'],
                $placement['height'],
                true,
            );

            if ($result) {
                $signedCount++;
            }
        }

        return redirect()->route('documents.index')->with('bulkSignedCount', $signedCount);
    }

    /**
     * The platform's suggested signature zone for a document: the last page, bottom-right,
     * sized to a sensible physical footprint regardless of the page's own dimensions.
     */
    private function suggestedPlacement(Document $document): array
    {
        $lastPage = collect($document->pages)->last();

        return [
            'page' => $document->page_count,
            'x' => 0.62,
            'y' => 0.80,
            'width' => min(0.30, 220 / max($lastPage['width'], 1)),
            'height' => min(0.12, 90 / max($lastPage['height'], 1)),
        ];
    }

    /**
     * Snapshot the current signature image, record it as the document's signature, and
     * build the final stamped PDF once and for all — its bytes (and content hash) are
     * fixed from this point on and never regenerated, so the signed document can no
     * longer be altered even by the platform itself. Returns null (without side effects)
     * if the document was already signed by the time this runs, to stay safe under
     * concurrent requests.
     */
    private function applySignature(
        Document $document,
        User $user,
        Signature $signature,
        int $pageNumber,
        float $posX,
        float $posY,
        float $boxWidth,
        float $boxHeight,
        bool $includeCaption,
    ): ?DocumentSignature {
        return DB::transaction(function () use ($document, $user, $signature, $pageNumber, $posX, $posY, $boxWidth, $boxHeight, $includeCaption) {
            $document->refresh();

            if ($document->isSigned()) {
                return null;
            }

            $imageContents = Storage::disk('public')->get($signature->image_path);
            $extension = pathinfo($signature->image_path, PATHINFO_EXTENSION) ?: 'png';
            $snapshotPath = 'document-signatures/'.$document->id.'.'.$extension;
            Storage::disk('local')->put($snapshotPath, $imageContents);

            $documentSignature = DocumentSignature::create([
                'document_id' => $document->id,
                'user_id' => $user->id,
                'signature_id' => $signature->id,
                'image_path' => $snapshotPath,
                'page_number' => $pageNumber,
                'pos_x' => $posX,
                'pos_y' => $posY,
                'box_width' => $boxWidth,
                'box_height' => $boxHeight,
                'include_caption' => $includeCaption,
                'signed_at' => now(),
            ]);

            $signedPdf = SignedPdfBuilder::build($document, $documentSignature);
            $signedPath = 'signed-documents/'.$document->id.'.pdf';
            Storage::disk('local')->put($signedPath, $signedPdf);

            $document->update([
                'status' => 'signed',
                'signed_path' => $signedPath,
                'content_hash' => hash('sha256', $signedPdf),
            ]);

            DocumentAuditLog::create([
                'document_id' => $document->id,
                'user_id' => $user->id,
                'action' => 'signed',
            ]);

            return $documentSignature;
        });
    }

    /**
     * Read the page count and per-page dimensions (in points) of a PDF file.
     *
     * @return array{0: int, 1: array<int, array{page: int, width: float, height: float}>}
     */
    private function readPdfPages(string $absolutePath): array
    {
        $reader = new PdfReader(new PdfParser(StreamReader::createByFile($absolutePath)));
        $pageCount = $reader->getPageCount();

        $pages = [];
        for ($i = 1; $i <= $pageCount; $i++) {
            [$width, $height] = $reader->getPage($i)->getWidthAndHeight();
            $pages[] = ['page' => $i, 'width' => round($width, 2), 'height' => round($height, 2)];
        }

        return [$pageCount, $pages];
    }
}
