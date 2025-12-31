<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'attachment',
        'message_type',
        'metadata',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isText(): bool
    {
        return $this->message_type === 'text' || $this->message_type === null;
    }

    public function isDelivery(): bool
    {
        return $this->message_type === 'delivery';
    }

    public function isStatusUpdate(): bool
    {
        return $this->message_type === 'status_update';
    }

    public function isSystem(): bool
    {
        return $this->message_type === 'system';
    }

    public const MESSAGE_TYPES = [
        'text' => 'Text Message',
        'delivery' => 'Delivery Submission',
        'status_update' => 'Status Update',
        'system' => 'System Message',
    ];

    public static function getMessageTypes(): array
    {
        return self::MESSAGE_TYPES;
    }
}
