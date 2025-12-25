<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'thumbnail',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'bundle_products');
    }

    public function getSavingsAttribute(): float
    {
        return $this->original_price - $this->price;
    }

    public function getSavingsPercentAttribute(): float
    {
        if ($this->original_price <= 0) {
            return 0;
        }
        return round(($this->savings / $this->original_price) * 100);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
