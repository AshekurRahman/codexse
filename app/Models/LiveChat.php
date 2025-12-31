<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agent_id',
        'session_id',
        'visitor_name',
        'visitor_email',
        'subject',
        'status',
        'department',
        'rating',
        'feedback',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'waiting' => 'Waiting',
        'active' => 'Active',
        'closed' => 'Closed',
    ];

    public const DEPARTMENTS = [
        'general' => 'General Support',
        'sales' => 'Sales',
        'technical' => 'Technical Support',
        'billing' => 'Billing',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(LiveChatMessage::class)->latestOfMany();
    }

    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function assignAgent(User $agent): void
    {
        $this->update([
            'agent_id' => $agent->id,
            'status' => 'active',
            'started_at' => now(),
        ]);

        $this->messages()->create([
            'sender_type' => 'system',
            'message' => "{$agent->name} has joined the chat.",
        ]);
    }

    public function close(): void
    {
        $this->update([
            'status' => 'closed',
            'ended_at' => now(),
        ]);

        $this->messages()->create([
            'sender_type' => 'system',
            'message' => 'This chat has been closed.',
        ]);
    }

    public function getVisitorDisplayName(): string
    {
        if ($this->user) {
            return $this->user->name;
        }

        return $this->visitor_name ?? 'Visitor';
    }

    public function unreadMessagesForAgent(): int
    {
        return $this->messages()
            ->where('sender_type', 'visitor')
            ->where('is_read', false)
            ->count();
    }

    public function unreadMessagesForVisitor(): int
    {
        return $this->messages()
            ->where('sender_type', 'agent')
            ->where('is_read', false)
            ->count();
    }

    public function markAsReadForAgent(): void
    {
        $this->messages()
            ->where('sender_type', 'visitor')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function markAsReadForVisitor(): void
    {
        $this->messages()
            ->where('sender_type', 'agent')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getDuration(): ?string
    {
        if (!$this->started_at) {
            return null;
        }

        $end = $this->ended_at ?? now();
        $diff = $this->started_at->diff($end);

        if ($diff->h > 0) {
            return $diff->format('%h hr %i min');
        }

        return $diff->format('%i min');
    }
}
