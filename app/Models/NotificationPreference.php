<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'push_enabled',
        'notify_orders',
        'notify_messages',
        'notify_sales',
        'notify_reviews',
        'notify_promotions',
    ];

    protected function casts(): array
    {
        return [
            'push_enabled' => 'boolean',
            'notify_orders' => 'boolean',
            'notify_messages' => 'boolean',
            'notify_sales' => 'boolean',
            'notify_reviews' => 'boolean',
            'notify_promotions' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getOrCreate(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'push_enabled' => true,
                'notify_orders' => true,
                'notify_messages' => true,
                'notify_sales' => true,
                'notify_reviews' => true,
                'notify_promotions' => false,
            ]
        );
    }

    public function shouldNotify(string $type): bool
    {
        if (!$this->push_enabled) {
            return false;
        }

        return match ($type) {
            'order' => $this->notify_orders,
            'message' => $this->notify_messages,
            'sale' => $this->notify_sales,
            'review' => $this->notify_reviews,
            'promotion' => $this->notify_promotions,
            default => false,
        };
    }
}
