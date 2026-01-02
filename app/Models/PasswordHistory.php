<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class PasswordHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a password was used before.
     */
    public static function wasUsedBefore(int $userId, string $password, int $checkCount = 5): bool
    {
        $recentPasswords = self::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take($checkCount)
            ->pluck('password_hash');

        foreach ($recentPasswords as $hash) {
            if (Hash::check($password, $hash)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store password in history.
     */
    public static function store(int $userId, string $hashedPassword): self
    {
        return self::create([
            'user_id' => $userId,
            'password_hash' => $hashedPassword,
        ]);
    }

    /**
     * Clean old password history entries.
     */
    public static function cleanOldEntries(int $userId, int $keepCount = 10): int
    {
        $idsToKeep = self::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take($keepCount)
            ->pluck('id');

        return self::where('user_id', $userId)
            ->whereNotIn('id', $idsToKeep)
            ->delete();
    }
}
