<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_order_id',
        'message_id',
        'notes',
        'files',
        'status',
        'revision_notes',
        'delivered_at',
        'responded_at',
    ];

    protected $casts = [
        'files' => 'array',
        'delivered_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRevisionRequested(): bool
    {
        return $this->status === 'revision_requested';
    }

    public const STATUSES = [
        'pending' => 'Pending Review',
        'accepted' => 'Accepted',
        'revision_requested' => 'Revision Requested',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
