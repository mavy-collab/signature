<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentSignature;
use App\Services\Pdf\SealingPdfEngine;
use Illuminate\Support\Facades\Storage;

class SignedPdfBuilder
{
    /**
     * Rebuild the original document page by page, stamping the signature image onto the
     * page it was placed on along with a small signing caption directly beneath it, then
     * seal the result with OtoSign's platform certificate (a real PKCS#7/PAdES signature
     * that PDF readers recognize natively — complementary to the SHA-256 fingerprint used
     * by /verifier, not a replacement for it). This runs exactly once, at signing time —
     * the result is persisted as the document's final file and never regenerated, so its
     * bytes (and content hash) stay fixed for good.
     */
    public static function build(Document $document, DocumentSignature $placement): string
    {
        $pdf = new SealingPdfEngine();
        $pdf->setSourceFile(Storage::disk('local')->path($document->path));

        // Signatures are sometimes placed close to the bottom margin, and the caption is
        // drawn just below the signature box — near enough to the edge, on some pages, to
        // fall inside the engine's default page-break margin. Left enabled, that silently
        // inserts a blank extra page for the caption instead of drawing it in place.
        $pdf->SetAutoPageBreak(false);

        $signer = $document->user;
        $caption = $placement->include_caption
            ? __('Signé le :date par :name', [
                'date' => $placement->signed_at->translatedFormat('d F Y').' '.__('à').' '.$placement->signed_at->format('H:i'),
                'name' => $signer->account_type === 'company' && $signer->company_name
                    ? $signer->company_name
                    : $signer->name,
            ])
            : null;

        for ($pageNumber = 1; $pageNumber <= $document->page_count; $pageNumber++) {
            $templateId = $pdf->importPage($pageNumber);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            if ($placement->page_number === $pageNumber) {
                $imageX = $placement->pos_x * $size['width'];
                $imageY = $placement->pos_y * $size['height'];
                $imageWidth = $placement->box_width * $size['width'];
                $imageHeight = $placement->box_height * $size['height'];

                $pdf->Image(
                    Storage::disk('local')->path($placement->image_path),
                    $imageX,
                    $imageY,
                    $imageWidth,
                    $imageHeight,
                );

                if ($caption !== null) {
                    $pdf->SetFont('helvetica', 'I', 8);
                    $pdf->SetTextColor(87, 83, 78);
                    $pdf->SetXY($imageX, $imageY + $imageHeight + 1.5);
                    $pdf->Cell($imageWidth, 4, $caption, 0, 0, 'C');
                }
            }
        }

        $pdf->setSignature(
            'file://'.config('services.otosign_pki.cert_path'),
            'file://'.config('services.otosign_pki.key_path'),
            config('services.otosign_pki.passphrase') ?: '',
            '',
            2,
            [
                'Name' => config('app.name'),
                'Reason' => __('Scellement électronique OtoSign'),
                'Location' => 'France',
                'ContactInfo' => url('/verifier'),
            ]
        );

        return $pdf->Output('', 'S');
    }
}
