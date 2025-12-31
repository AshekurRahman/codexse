<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'preview_text',
        'content',
        'email_template_id',
        'status',
        'sending_status',
        'created_by',
        'total_recipients',
        'sent_count',
        'failed_count',
        'opened_count',
        'clicked_count',
        'daily_limit',
        'sending_duration_days',
        'daily_increment',
        'current_day',
        'today_sent_count',
        'last_send_date',
        'campaign_start_date',
        'campaign_end_date',
        'scheduled_at',
        'sent_at',
        'completed_at',
        'paused_at',
        'stopped_at',
        'sending_log',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'completed_at' => 'datetime',
            'paused_at' => 'datetime',
            'stopped_at' => 'datetime',
            'last_send_date' => 'date',
            'campaign_start_date' => 'date',
            'campaign_end_date' => 'date',
            'sending_log' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EmailCampaignLog::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeRunning($query)
    {
        return $query->where('sending_status', 'running');
    }

    public function scopePaused($query)
    {
        return $query->where('sending_status', 'paused');
    }

    // Status checks
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    // Sending status checks
    public function isRunning(): bool
    {
        return $this->sending_status === 'running';
    }

    public function isPaused(): bool
    {
        return $this->sending_status === 'paused';
    }

    public function isStopped(): bool
    {
        return $this->sending_status === 'stopped';
    }

    public function isCompleted(): bool
    {
        return $this->sending_status === 'completed';
    }

    public function isIdle(): bool
    {
        return $this->sending_status === 'idle';
    }

    // Calculated attributes
    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0;
        }
        return round(($this->opened_count / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0;
        }
        return round(($this->clicked_count / $this->sent_count) * 100, 2);
    }

    public function getDeliveryRateAttribute(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    public function getTodayLimitAttribute(): int
    {
        return $this->daily_limit + ($this->daily_increment * $this->current_day);
    }

    public function getRemainingTodayAttribute(): int
    {
        return max(0, $this->today_limit - $this->today_sent_count);
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->campaign_end_date) {
            return 0;
        }
        return max(0, now()->diffInDays($this->campaign_end_date, false));
    }

    // Actions
    public function start(): void
    {
        $this->update([
            'status' => 'sending',
            'sending_status' => 'running',
            'campaign_start_date' => $this->campaign_start_date ?? now()->toDateString(),
            'campaign_end_date' => $this->campaign_end_date ?? now()->addDays($this->sending_duration_days - 1)->toDateString(),
            'sent_at' => $this->sent_at ?? now(),
            'total_recipients' => NewsletterSubscriber::active()->count(),
        ]);

        $this->addLog('Campaign started');
    }

    public function pause(): void
    {
        $this->update([
            'sending_status' => 'paused',
            'paused_at' => now(),
        ]);

        $this->addLog('Campaign paused');
    }

    public function resume(): void
    {
        $this->update([
            'sending_status' => 'running',
            'paused_at' => null,
        ]);

        $this->addLog('Campaign resumed');
    }

    public function stop(): void
    {
        $this->update([
            'status' => 'failed',
            'sending_status' => 'stopped',
            'stopped_at' => now(),
        ]);

        $this->addLog('Campaign stopped');
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'sent',
            'sending_status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->addLog('Campaign completed');
    }

    public function resetDailyCount(): void
    {
        $this->update([
            'today_sent_count' => 0,
            'last_send_date' => now()->toDateString(),
            'current_day' => $this->current_day + 1,
        ]);

        $this->addLog("Day {$this->current_day} started. Daily limit: {$this->today_limit}");
    }

    public function incrementSentCount(): void
    {
        $this->increment('sent_count');
        $this->increment('today_sent_count');
    }

    public function incrementFailedCount(): void
    {
        $this->increment('failed_count');
    }

    public function canSendToday(): bool
    {
        if (!$this->isRunning()) {
            return false;
        }

        // Check if daily limit reached
        if ($this->today_sent_count >= $this->today_limit) {
            return false;
        }

        // Check if campaign duration exceeded
        if ($this->campaign_end_date && now()->startOfDay()->gt($this->campaign_end_date)) {
            return false;
        }

        return true;
    }

    public function needsDailyReset(): bool
    {
        if (!$this->last_send_date) {
            return true;
        }

        return !$this->last_send_date->isToday();
    }

    public function addLog(string $message): void
    {
        $logs = $this->sending_log ?? [];
        $logs[] = [
            'timestamp' => now()->toIso8601String(),
            'message' => $message,
        ];

        // Keep only last 100 logs
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
        }

        $this->update(['sending_log' => $logs]);
    }

    public function getRenderedContent(NewsletterSubscriber $subscriber): string
    {
        $variables = [
            'app_name' => config('app.name'),
            'year' => date('Y'),
            'subscriber_email' => $subscriber->email,
            'unsubscribe_url' => route('newsletter.unsubscribe', $subscriber->token),
            'subject' => $this->subject,
            'preview_text' => $this->preview_text ?? '',
            'cta_url' => config('app.url'),
            'cta_text' => 'Visit Website',
            'content' => $this->content,
        ];

        if ($this->template) {
            $html = $this->template->renderContent($variables);
        } else {
            $html = view('emails.campaign', [
                'campaign' => $this,
                'subscriber' => $subscriber,
                'content' => $this->content,
                'previewText' => $this->preview_text,
                'unsubscribeUrl' => $variables['unsubscribe_url'],
            ])->render();
        }

        return $html;
    }
}
