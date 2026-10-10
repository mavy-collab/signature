<?php

namespace App\Http\Controllers;

use App\Models\Signature;
use App\Models\SignatureAuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SignatureController extends Controller
{
    /**
     * Show the authenticated user's signature. A signature that turns out to be blank
     * (e.g. saved before validation caught empty canvases) doesn't count as a real,
     * final signature: it's treated as if none exists yet, so it can be redrawn.
     */
    public function show(Request $request): View
    {
        $signature = $request->user()->signature;
        $wasBlank = false;

        if ($signature && $signature->isBlank()) {
            $signature = null;
            $wasBlank = true;
        }

        return view('signature.show', [
            'signature' => $signature,
            'wasBlank' => $wasBlank,
            'auditLogs' => $request->user()->signatureAuditLogs()->latest()->limit(20)->get(),
        ]);
    }

    /**
     * Register the authenticated user's first signature. A blank signature left over
     * from before validation existed doesn't count as real, so it can still be replaced
     * here; but once a real signature is registered, replacing it requires going through
     * the password-confirmed edit() / update() flow instead.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $existing = Signature::where('user_id', $user->id)->first();

        if ($existing && ! $existing->isBlank()) {
            return back()->withErrors(['signature_data' => __('Une signature est déjà enregistrée. Utilisez « Modifier ma signature » pour la remplacer.')]);
        }

        [$imageContents, $extension] = $this->extractSignatureUpload($request);

        $saved = DB::transaction(function () use ($user, $imageContents, $extension) {
            $existing = Signature::where('user_id', $user->id)->first();

            if ($existing && ! $existing->isBlank()) {
                return null;
            }

            return $this->persistSignature($user->id, $imageContents, $extension, $existing ? 'updated' : 'created');
        });

        if (! $saved) {
            return back()->withErrors(['signature_data' => __('Une signature est déjà enregistrée. Utilisez « Modifier ma signature » pour la remplacer.')]);
        }

        return redirect()->route('signature.show')->with('status', 'signature-saved');
    }

    /**
     * Show the form to replace an existing, real signature. Gated by the
     * `password.confirm` middleware since this changes an already-established signature.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $signature = $request->user()->signature;

        if (! $signature || $signature->isBlank()) {
            return redirect()->route('signature.show');
        }

        return view('signature.edit', ['signature' => $signature]);
    }

    /**
     * Replace the authenticated user's existing signature. Documents already signed keep
     * their own independent snapshot of the signature as it was at signing time, so
     * replacing the live signature doesn't retroactively change anything already signed.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $existing = Signature::where('user_id', $user->id)->first();

        if (! $existing || $existing->isBlank()) {
            return redirect()->route('signature.show');
        }

        [$imageContents, $extension] = $this->extractSignatureUpload($request);

        $this->persistSignature($user->id, $imageContents, $extension, 'updated');

        return redirect()->route('signature.show')->with('status', 'signature-saved');
    }

    /**
     * Validate the incoming request (drawn PNG or uploaded image) and return its raw
     * bytes and file extension. Throws a validation error if the image is blank.
     *
     * @return array{0: string, 1: string}
     */
    private function extractSignatureUpload(Request $request): array
    {
        $request->validate([
            'signature_data' => ['nullable', 'required_without:signature_file', 'string', 'starts_with:data:image/png;base64,'],
            'signature_file' => ['nullable', 'required_without:signature_data', 'file', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
        ]);

        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $imageContents = file_get_contents($file->getRealPath());
            $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension()) ?: 'png';
            $extension = $extension === 'jpeg' ? 'jpg' : $extension;

            if ($extension === 'png' && Signature::pngIsBlank($imageContents)) {
                throw ValidationException::withMessages(['signature_file' => __('Cette image ne contient aucune signature visible.')]);
            }

            return [$imageContents, $extension];
        }

        $base64 = substr($request->string('signature_data'), strlen('data:image/png;base64,'));
        $imageContents = base64_decode($base64, true);

        if ($imageContents === false) {
            throw ValidationException::withMessages(['signature_data' => __('Signature invalide.')]);
        }

        if (Signature::pngIsBlank($imageContents)) {
            throw ValidationException::withMessages(['signature_data' => __('Veuillez dessiner votre signature avant de l\'enregistrer.')]);
        }

        return [$imageContents, 'png'];
    }

    /**
     * Write the signature file to storage and record it, deleting any previous file.
     */
    private function persistSignature(int $userId, string $imageContents, string $extension, string $action): Signature
    {
        $existing = Signature::where('user_id', $userId)->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->image_path);
        }

        $path = 'signatures/'.$userId.'-'.now()->format('YmdHis').'-'.substr(bin2hex(random_bytes(4)), 0, 8).'.'.$extension;
        Storage::disk('public')->put($path, $imageContents);

        $signature = Signature::updateOrCreate(
            ['user_id' => $userId],
            ['image_path' => $path],
        );

        SignatureAuditLog::create([
            'user_id' => $userId,
            'signature_id' => $signature->id,
            'action' => $action,
        ]);

        return $signature;
    }
}
