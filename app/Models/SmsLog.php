<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'type',
        'message',
        'status',
        'provider',
        'provider_message_id',
        'provider_response',
        'error_message',
        'cost',
    ];

    protected function casts(): array
    {
        return [
            'provider_response' => 'array',
            'cost' => 'decimal:4',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'sent' => 'Sent',
        'delivered' => 'Delivered',
        'failed' => 'Failed',
    ];

    public const TYPES = [
        'verification' => 'Verification',
        'order_confirmation' => 'Order Confirmation',
        'order_shipped' => 'Order Shipped',
        'order_delivered' => 'Order Delivered',
        'order_cancelled' => 'Order Cancelled',
        'payout_completed' => 'Payout Completed',
        'service_order_new' => 'New Service Order',
        'service_order_completed' => 'Service Completed',
        'message_received' => 'Message Received',
        'video_call_reminder' => 'Video Call Reminder',
        'marketing' => 'Marketing',
    ];

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsSent(?string $providerId = null): void
    {
        $this->update([
            'status' => 'sent',
            'provider_message_id' => $providerId,
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update(['status' => 'delivered']);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
