<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignatureAuditLog extends Model
{
    const UPDATED_AT = null;

    /**
     * Microsecond precision keeps chronological ordering correct even when
     * two audit events are logged within the same second.
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'user_id',
        'signature_id',
        'action',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signature(): BelongsTo
    {
        return $this->belongsTo(Signature::class);
    }
}
