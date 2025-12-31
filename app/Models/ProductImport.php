<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'total_rows',
        'processed_rows',
        'success_rows',
        'failed_rows',
        'status',
        'options',
        'errors',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'errors' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->total_rows === 0) return 0;
        return round(($this->processed_rows / $this->total_rows) * 100, 2);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing', 'started_at' => now()]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed', 'completed_at' => now()]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $errors = $this->errors ?? [];
        if ($reason) $errors['fatal'] = $reason;
        $this->update(['status' => 'failed', 'errors' => $errors, 'completed_at' => now()]);
    }
}
