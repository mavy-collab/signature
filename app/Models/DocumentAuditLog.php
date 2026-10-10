<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAuditLog extends Model
{
    const UPDATED_AT = null;

    /**
     * Microsecond precision keeps chronological ordering correct even when
     * two audit events are logged within the same second.
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'document_id',
        'user_id',
        'action',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
