<?php

namespace App\Models;

use App\Services\LicenseService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'product_name',
        'license_type',
        'price',
        'discount',
        'seller_amount',
        'platform_fee',
        'license_key',
        'download_count',
        'download_limit',
        'last_downloaded_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'seller_amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'last_downloaded_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            // Generate license key using the service for consistent format
            if (empty($item->license_key)) {
                $item->license_key = app(LicenseService::class)->generate();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function license(): HasOne
    {
        return $this->hasOne(License::class);
    }

    public function canDownload(): bool
    {
        if ($this->download_limit === 0) {
            return true; // Unlimited downloads
        }

        return $this->download_count < $this->download_limit;
    }
}
