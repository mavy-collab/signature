<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    private const PNG_DATA_URL = 'data:image/png;base64,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';

    private function makePdf(): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << >> /Contents 4 0 R >>',
            4 => "<< /Length 5 >>\nstream\nBT ET\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= "{$num} 0 obj\n{$body}\nendobj\n";
        }

        $xrefStart = strlen($pdf);
        $pdf .= "xref\n0 5\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n{$xrefStart}\n%%EOF";

        return $pdf;
    }

    public function test_guest_cannot_view_history(): void
    {
        $this->get('/historique')->assertRedirect('/login');
    }

    public function test_history_merges_signature_and_document_events_in_order(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)->post('/signature', ['signature_data' => self::PNG_DATA_URL]);

        $file = UploadedFile::fake()->createWithContent('doc.pdf', $this->makePdf());
        $this->actingAs($user)->post('/documents', ['file' => $file]);
        $document = Document::where('user_id', $user->id)->latest()->first();

        $this->actingAs($user)->post(route('documents.sign', $document), [
            'page_number' => 1,
            'pos_x' => 0.6,
            'pos_y' => 0.8,
            'box_width' => 0.25,
            'box_height' => 0.1,
        ]);

        $response = $this->actingAs($user)->get('/historique');

        $response->assertOk();
        $response->assertSeeInOrder([
            'Document signé',
            'Document déposé',
            'Signature créée',
        ]);
    }

    public function test_signed_document_history_link_points_to_vault(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user)->post('/signature', ['signature_data' => self::PNG_DATA_URL]);

        $file = UploadedFile::fake()->createWithContent('doc.pdf', $this->makePdf());
        $this->actingAs($user)->post('/documents', ['file' => $file]);
        $document = Document::where('user_id', $user->id)->latest()->first();

        $this->actingAs($user)->post(route('documents.sign', $document), [
            'page_number' => 1,
            'pos_x' => 0.6,
            'pos_y' => 0.8,
            'box_width' => 0.25,
            'box_height' => 0.1,
        ]);

        $response = $this->actingAs($user)->get('/historique');

        $response->assertSee(route('vault.show', $document), false);
        $response->assertDontSee(route('documents.show', $document), false);
    }

    public function test_history_is_isolated_per_user(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($owner)->post('/signature', ['signature_data' => self::PNG_DATA_URL]);

        $response = $this->actingAs($other)->get('/historique');

        $response->assertOk();
        $response->assertSee(__('Aucune activité pour le moment.'));
    }
}
