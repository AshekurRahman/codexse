<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'product_id',
        'conversationable_type',
        'conversationable_id',
        'type',
        'subject',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function conversationable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isServiceOrder(): bool
    {
        return $this->type === 'service_order';
    }

    public function isJobPosting(): bool
    {
        return $this->type === 'job_posting';
    }

    public function isJobContract(): bool
    {
        return $this->type === 'job_contract';
    }

    public const TYPES = [
        'general' => 'General',
        'service_order' => 'Service Order',
        'job_posting' => 'Job Posting',
        'job_contract' => 'Job Contract',
    ];

    public static function getTypes(): array
    {
        return self::TYPES;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadMessagesFor(User $user): int
    {
        return $this->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->count();
    }

    public function markAsReadFor(User $user): void
    {
        $this->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['read_at' => now()]);
    }
}
