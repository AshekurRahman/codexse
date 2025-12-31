<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_chat_id',
        'user_id',
        'sender_type',
        'message',
        'attachments',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'is_read' => 'boolean',
        ];
    }

    public const SENDER_TYPES = [
        'visitor' => 'Visitor',
        'agent' => 'Agent',
        'system' => 'System',
    ];

    public function liveChat(): BelongsTo
    {
        return $this->belongsTo(LiveChat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFromVisitor(): bool
    {
        return $this->sender_type === 'visitor';
    }

    public function isFromAgent(): bool
    {
        return $this->sender_type === 'agent';
    }

    public function isSystem(): bool
    {
        return $this->sender_type === 'system';
    }

    public function getSenderName(): string
    {
        if ($this->isSystem()) {
            return 'System';
        }

        if ($this->user) {
            return $this->user->name;
        }

        if ($this->isFromVisitor()) {
            return $this->liveChat->getVisitorDisplayName();
        }

        return 'Support Agent';
    }
}
