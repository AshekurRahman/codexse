<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'user_id',
        'product_id',
        'license_key',
        'license_type',
        'status',
        'activations_count',
        'max_activations',
        'activated_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'activations_count' => 'integer',
        'max_activations' => 'integer',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function activations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }
        return $this->expires_at->isPast();
    }

    public function canActivate(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->max_activations === 0) {
            return true; // Unlimited activations
        }

        return $this->activations_count < $this->max_activations;
    }

    public function activationsRemaining(): int
    {
        if ($this->max_activations === 0) {
            return -1; // Unlimited
        }

        return max(0, $this->max_activations - $this->activations_count);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'suspended' => 'warning',
            'expired' => 'gray',
            'revoked' => 'danger',
            default => 'gray',
        };
    }
}
