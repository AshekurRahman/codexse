<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class AiChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'guest_name',
        'guest_email',
        'status',
        'subject',
        'context',
        'message_count',
        'total_tokens_used',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'last_message_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->session_id)) {
                $session->session_id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(AiChatMessage::class)->latestOfMany();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForUserOrSession($query, ?int $userId, ?string $sessionId)
    {
        return $query->where(function ($q) use ($userId, $sessionId) {
            if ($userId) {
                $q->where('user_id', $userId);
            } else {
                $q->where('session_id', $sessionId)->whereNull('user_id');
            }
        });
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->guest_name ?? 'Guest';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    public function incrementMessageCount(int $tokens = 0): void
    {
        $this->increment('message_count');
        if ($tokens > 0) {
            $this->increment('total_tokens_used', $tokens);
        }
        $this->update(['last_message_at' => now()]);
    }
}
