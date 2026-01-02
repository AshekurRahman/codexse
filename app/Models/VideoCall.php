<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VideoCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'host_id',
        'participant_id',
        'conversation_id',
        'service_order_id',
        'status',
        'type',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration',
        'provider',
        'provider_data',
        'notes',
        'recording_url',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'provider_data' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($call) {
            if (empty($call->room_id)) {
                $call->room_id = 'room_' . Str::random(16);
            }
        });
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'scheduled' => 'Scheduled',
        'active' => 'Active',
        'ended' => 'Ended',
        'missed' => 'Missed',
        'cancelled' => 'Cancelled',
    ];

    public const TYPES = [
        'video' => 'Video Call',
        'audio' => 'Audio Call',
    ];

    public const PROVIDERS = [
        'agora' => 'Agora',
        'twilio' => 'Twilio',
        'daily' => 'Daily.co',
        'jitsi' => 'Jitsi Meet',
    ];

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function start(): void
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function end(): void
    {
        $duration = $this->started_at ? now()->diffInSeconds($this->started_at) : 0;

        $this->update([
            'status' => 'ended',
            'ended_at' => now(),
            'duration' => $duration,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAsMissed(): void
    {
        $this->update(['status' => 'missed']);
    }

    public function getFormattedDurationAttribute(): string
    {
        $duration = (int) abs($this->duration ?? 0);

        if ($duration === 0) {
            return '0:00';
        }

        $hours = (int) floor($duration / 3600);
        $minutes = (int) floor(($duration % 3600) / 60);
        $seconds = (int) ($duration % 60);

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getJoinUrlAttribute(): string
    {
        $providerData = $this->provider_data ?? [];

        // Return provider-specific URL if available
        if (isset($providerData['join_url'])) {
            return $providerData['join_url'];
        }

        // Generate URL based on provider
        return match ($this->provider) {
            'jitsi' => 'https://' . ($providerData['domain'] ?? 'meet.jit.si') . '/' . ($providerData['room_name'] ?? $this->room_id),
            default => url('/video-call/' . $this->room_id),
        };
    }

    public function canJoin(User $user): bool
    {
        return $user->id === $this->host_id || $user->id === $this->participant_id;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('host_id', $user->id)
              ->orWhere('participant_id', $user->id);
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['pending', 'scheduled'])
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '>=', now());
            });
    }
}
