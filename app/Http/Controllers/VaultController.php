<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VaultController extends Controller
{
    /**
     * List the authenticated user's signed documents kept in the vault.
     */
    public function index(Request $request): View
    {
        $documents = Document::where('user_id', $request->user()->id)
            ->where('status', 'signed')
            ->latest()
            ->get();

        return view('vault.index', ['documents' => $documents]);
    }

    /**
     * Show a signed document from the vault.
     */
    public function show(Request $request, Document $document): View
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        abort_unless($document->isSigned(), 404);

        $document->load(['signature', 'auditLogs' => fn ($query) => $query->latest()->limit(20)]);

        DocumentAuditLog::create([
            'document_id' => $document->id,
            'user_id' => $request->user()->id,
            'action' => 'vault_viewed',
        ]);

        return view('vault.show', ['document' => $document]);
    }

    /**
     * Stream a signed document's final file from the vault (inline preview) — the same
     * immutable bytes produced once at signing time, never regenerated.
     */
    public function file(Request $request, Document $document): StreamedResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        abort_unless($document->isSigned(), 404);

        return Storage::disk('local')->response($document->finalPath(), $document->original_name, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Download the final signed PDF: the exact same immutable file every time, produced
     * once at signing time (or, for an upload that arrived already signed, the uploaded
     * file itself) and never regenerated — so its content can never drift from what was
     * originally signed and hashed.
     */
    public function download(Request $request, Document $document): Response|StreamedResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        abort_unless($document->isSigned(), 404);

        DocumentAuditLog::create([
            'document_id' => $document->id,
            'user_id' => $request->user()->id,
            'action' => 'downloaded',
        ]);

        $downloadName = pathinfo($document->original_name, PATHINFO_FILENAME).'-signe.pdf';

        return Storage::disk('local')->download($document->finalPath(), $downloadName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
