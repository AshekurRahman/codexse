<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'tier',
        'description',
        'price',
        'delivery_days',
        'revisions',
        'deliverables',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deliverables' => 'array',
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getDeliveryTextAttribute(): string
    {
        if ($this->delivery_days == 1) {
            return '1 day delivery';
        }
        return $this->delivery_days . ' days delivery';
    }

    public function getRevisionsTextAttribute(): string
    {
        if ($this->revisions == 0) {
            return 'Unlimited revisions';
        }
        if ($this->revisions == 1) {
            return '1 revision';
        }
        return $this->revisions . ' revisions';
    }

    public const TIERS = [
        'basic' => 'Basic',
        'standard' => 'Standard',
        'premium' => 'Premium',
    ];

    public static function getTiers(): array
    {
        return self::TIERS;
    }
}
