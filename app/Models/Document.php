<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'path',
        'signed_path',
        'content_hash',
        'mime_type',
        'page_count',
        'pages',
        'status',
        'uploaded_signed',
    ];

    /**
     * The final, immutable file for a signed document: the freshly stamped PDF for one
     * signed in-app, or the uploaded file itself when it arrived already signed.
     * Never regenerated after signing, so its bytes (and content_hash) never change.
     */
    public function finalPath(): ?string
    {
        if (! $this->isSigned()) {
            return null;
        }

        return $this->signed_path ?? $this->path;
    }

    protected function casts(): array
    {
        return [
            'pages' => 'array',
            'uploaded_signed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signature(): HasOne
    {
        return $this->hasOne(DocumentSignature::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(DocumentAuditLog::class);
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }
}
