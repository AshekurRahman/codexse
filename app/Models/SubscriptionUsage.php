<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionUsage extends Model
{
    use HasFactory;

    protected $table = 'subscription_usage';

    protected $fillable = [
        'subscription_id',
        'feature',
        'quantity',
        'recorded_at',
        'metadata',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    // Scopes
    public function scopeForFeature($query, string $feature)
    {
        return $query->where('feature', $feature);
    }

    public function scopeInPeriod($query, $start, $end)
    {
        return $query->whereBetween('recorded_at', [$start, $end]);
    }
}
