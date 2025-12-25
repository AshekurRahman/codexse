<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements HasMedia
{
    use HasFactory, HasSlug, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'sale_price',
        'thumbnail',
        'preview_images',
        'video_url',
        'file_path',
        'file_size',
        'file_type',
        'preview_url',
        'demo_url',
        'version',
        'changelog',
        'software_compatibility',
        'license_types',
        'status',
        'rejection_reason',
        'is_featured',
        'is_trending',
        'views_count',
        'downloads_count',
        'sales_count',
        'average_rating',
        'reviews_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'preview_images' => 'array',
            'software_compatibility' => 'array',
            'license_types' => 'array',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'average_rating' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile();

        $this->addMediaCollection('previews');

        $this->addMediaCollection('files')
            ->singleFile();
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(ProductBundle::class, 'product_bundle_product', 'product_id', 'product_bundle_id');
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        return asset('images/placeholder-product.png');
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function hasVideo(): bool
    {
        return !empty($this->video_url);
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $this->video_url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return $this->video_url;
    }

    public function getVideoThumbnailAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // YouTube thumbnail
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $this->video_url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
        }

        return null;
    }

    public function getGalleryItemsAttribute(): array
    {
        $items = [];

        // Add thumbnail as first item
        if ($this->thumbnail) {
            $items[] = [
                'type' => 'image',
                'url' => $this->thumbnail_url,
                'thumbnail' => $this->thumbnail_url,
            ];
        }

        // Add video if exists
        if ($this->hasVideo()) {
            $items[] = [
                'type' => 'video',
                'url' => $this->video_embed_url,
                'thumbnail' => $this->video_thumbnail ?? $this->thumbnail_url,
            ];
        }

        // Add preview images
        if ($this->preview_images && is_array($this->preview_images)) {
            foreach ($this->preview_images as $image) {
                $imageUrl = str_starts_with($image, 'http') ? $image : asset('storage/' . $image);
                $items[] = [
                    'type' => 'image',
                    'url' => $imageUrl,
                    'thumbnail' => $imageUrl,
                ];
            }
        }

        return $items;
    }
}
