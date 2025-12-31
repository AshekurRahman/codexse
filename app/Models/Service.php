<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'gallery_images',
        'status',
        'rejection_reason',
        'is_featured',
        'accepts_custom_orders',
        'views_count',
        'orders_count',
        'average_rating',
        'reviews_count',
        'published_at',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'is_featured' => 'boolean',
        'accepts_custom_orders' => 'boolean',
        'average_rating' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }

            // Ensure unique slug
            $originalSlug = $service->slug;
            $count = 1;
            while (static::where('slug', $service->slug)->exists()) {
                $service->slug = $originalSlug . '-' . $count++;
            }
        });
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(ServicePackage::class)->orderBy('sort_order');
    }

    public function activePackages(): HasMany
    {
        return $this->packages()->where('is_active', true);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ServiceRequirement::class)->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(CustomQuoteRequest::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessors
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('images/placeholder-service.svg');
    }

    public function getStartingPriceAttribute(): ?float
    {
        $basicPackage = $this->packages()->where('tier', 'basic')->first();
        return $basicPackage?->price;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Status checks
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // Constants
    public const STATUSES = [
        'draft' => 'Draft',
        'pending' => 'Pending Review',
        'published' => 'Published',
        'rejected' => 'Rejected',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
