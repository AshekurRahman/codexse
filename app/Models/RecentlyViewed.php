<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentlyViewed extends Model
{
    use HasFactory;

    protected $table = 'recently_viewed';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function recordView(User $user, Product $product): void
    {
        self::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
            ],
            [
                'updated_at' => now(),
            ]
        );
    }

    public static function getRecentForUser(User $user, int $limit = 10)
    {
        return self::where('user_id', $user->id)
            ->with('product')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->pluck('product');
    }
}
