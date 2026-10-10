<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Public page: anyone holding a signed PDF (the platform's own user, or a third
     * party they sent it to) can drop the file here to confirm it's byte-for-byte
     * identical to what was signed and sealed — proof it hasn't been altered since.
     */
    public function show(): View
    {
        return view('verification.show', ['result' => null]);
    }

    public function verify(Request $request): View
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $hash = hash('sha256', $request->file('file')->get());

        $document = Document::where('content_hash', $hash)->with(['user', 'signature'])->first();

        return view('verification.show', [
            'result' => $document ? [
                'match' => true,
                'document' => $document,
            ] : [
                'match' => false,
            ],
        ]);
    }
}
