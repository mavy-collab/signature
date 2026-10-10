<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSignature extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'signature_id',
        'image_path',
        'page_number',
        'pos_x',
        'pos_y',
        'box_width',
        'box_height',
        'include_caption',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'pos_x' => 'float',
            'pos_y' => 'float',
            'box_width' => 'float',
            'box_height' => 'float',
            'include_caption' => 'boolean',
            'signed_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signature(): BelongsTo
    {
        return $this->belongsTo(Signature::class);
    }

    /**
     * The snapshot image's MIME type, inferred from its stored file extension.
     */
    public function mimeType(): string
    {
        return match (strtolower(pathinfo($this->image_path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/png',
        };
    }
}
