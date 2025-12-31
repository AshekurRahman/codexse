<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasFactory, Notifiable, Billable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'avatar',
        'bio',
        'website',
        'google_id',
        'facebook_id',
        'github_id',
        'twitter_id',
        'social_avatar',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'referral_code',
        'referred_by',
        'referral_balance',
        'total_referrals',
        'successful_referrals',
        'wishlist_share_token',
        'wishlist_public',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'referral_balance' => 'decimal:2',
            'wishlist_public' => 'boolean',
        ];
    }

    /**
     * Check if two-factor authentication is enabled.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && $this->two_factor_confirmed_at !== null;
    }

    /**
     * Get decrypted two-factor secret.
     */
    public function getTwoFactorSecret(): ?string
    {
        if (!$this->two_factor_secret) {
            return null;
        }

        try {
            return decrypt($this->two_factor_secret);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get recovery codes as array.
     */
    public function getRecoveryCodes(): array
    {
        if (!$this->two_factor_recovery_codes) {
            return [];
        }

        try {
            return json_decode(decrypt($this->two_factor_recovery_codes), true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Generate new recovery codes.
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(md5(random_bytes(32)), 0, 10));
        }

        $this->two_factor_recovery_codes = encrypt(json_encode($codes));
        $this->save();

        return $codes;
    }

    /**
     * Use a recovery code.
     */
    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->getRecoveryCodes();
        $code = strtoupper(trim($code));

        if (($key = array_search($code, $codes)) !== false) {
            unset($codes[$key]);
            $this->two_factor_recovery_codes = encrypt(json_encode(array_values($codes)));
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the seller profile for the user.
     */
    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class);
    }

    /**
     * Get the orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the user's wishlist items.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Generate or get wishlist share token.
     */
    public function getWishlistShareToken(): string
    {
        if (!$this->wishlist_share_token) {
            $this->wishlist_share_token = bin2hex(random_bytes(16));
            $this->save();
        }

        return $this->wishlist_share_token;
    }

    /**
     * Get the wishlist share URL.
     */
    public function getWishlistShareUrlAttribute(): string
    {
        return route('wishlist.shared', $this->getWishlistShareToken());
    }

    /**
     * Regenerate wishlist share token.
     */
    public function regenerateWishlistShareToken(): string
    {
        $this->wishlist_share_token = bin2hex(random_bytes(16));
        $this->save();

        return $this->wishlist_share_token;
    }

    /**
     * Get the user's downloads.
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Get the user's licenses.
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    /**
     * Get the user's affiliate profile.
     */
    public function affiliate(): HasOne
    {
        return $this->hasOne(Affiliate::class);
    }

    /**
     * Get the sellers the user follows.
     */
    public function followedSellers(): HasMany
    {
        return $this->hasMany(SellerFollow::class);
    }

    /**
     * Check if user is following a seller.
     */
    public function isFollowing(Seller $seller): bool
    {
        return $this->followedSellers()->where('seller_id', $seller->id)->exists();
    }

    /**
     * Get the user's support tickets.
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Get the user's conversations.
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    /**
     * Get the user's AI chat sessions.
     */
    public function aiChatSessions(): HasMany
    {
        return $this->hasMany(AiChatSession::class);
    }

    /**
     * Get the user's job postings (as client).
     */
    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'client_id');
    }

    /**
     * Get the user's service orders (as buyer).
     */
    public function serviceOrdersAsBuyer(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'buyer_id');
    }

    /**
     * Get the user's job contracts (as client).
     */
    public function jobContractsAsClient(): HasMany
    {
        return $this->hasMany(JobContract::class, 'client_id');
    }

    /**
     * Get escrow transactions where user is payer.
     */
    public function escrowTransactionsAsPayer(): HasMany
    {
        return $this->hasMany(EscrowTransaction::class, 'payer_id');
    }

    /**
     * Get escrow transactions where user is payee.
     */
    public function escrowTransactionsAsPayee(): HasMany
    {
        return $this->hasMany(EscrowTransaction::class, 'payee_id');
    }

    /**
     * Get disputes initiated by the user.
     */
    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'initiated_by');
    }

    /**
     * Check if user is a seller.
     */
    public function isSeller(): bool
    {
        return $this->seller()->exists() && $this->seller->status === 'approved';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        if ($this->social_avatar) {
            return $this->social_avatar;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff';
    }

    public function hasSocialLogin(): bool
    {
        return $this->google_id || $this->facebook_id || $this->github_id || $this->twitter_id;
    }

    // ==================== Referral Methods ====================

    /**
     * Boot method to generate referral code on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = static::generateUniqueReferralCode();
            }
        });
    }

    /**
     * Generate a unique referral code.
     */
    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Get the user who referred this user.
     */
    public function referrer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Get all users referred by this user.
     */
    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Get all referral rewards for this user.
     */
    public function referralRewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class);
    }

    /**
     * Get the referral link.
     */
    public function getReferralLinkAttribute(): string
    {
        return route('register', ['ref' => $this->referral_code]);
    }

    /**
     * Process referral signup reward.
     */
    public function processReferralSignupReward(): void
    {
        if (!$this->referred_by || !ReferralSetting::isEnabled()) {
            return;
        }

        $referrer = $this->referrer;
        if (!$referrer) {
            return;
        }

        // Reward for referrer
        $referrerReward = ReferralSetting::getSignupRewardForReferrer();
        if ($referrerReward > 0) {
            $reward = ReferralReward::create([
                'user_id' => $referrer->id,
                'referred_user_id' => $this->id,
                'type' => 'signup',
                'amount' => $referrerReward,
                'description' => "Referral bonus for inviting {$this->name}",
                'status' => 'credited',
                'credited_at' => now(),
            ]);

            $referrer->increment('referral_balance', $referrerReward);
            $referrer->increment('total_referrals');
            $referrer->increment('successful_referrals');
        }

        // Reward for referred user (welcome bonus)
        $referredReward = ReferralSetting::getSignupRewardForReferred();
        if ($referredReward > 0) {
            ReferralReward::create([
                'user_id' => $this->id,
                'referred_user_id' => $referrer->id,
                'type' => 'signup',
                'amount' => $referredReward,
                'description' => "Welcome bonus for joining via referral",
                'status' => 'credited',
                'credited_at' => now(),
            ]);

            $this->increment('referral_balance', $referredReward);
        }
    }

    /**
     * Process referral purchase commission.
     */
    public function processReferralPurchaseCommission(Order $order): void
    {
        if (!$this->referred_by || !ReferralSetting::isEnabled()) {
            return;
        }

        $referrer = $this->referrer;
        if (!$referrer) {
            return;
        }

        $commissionPercent = ReferralSetting::getPurchaseCommissionPercent();
        $commission = ($order->total * $commissionPercent) / 100;

        if ($commission > 0) {
            ReferralReward::create([
                'user_id' => $referrer->id,
                'referred_user_id' => $this->id,
                'order_id' => $order->id,
                'type' => 'purchase',
                'amount' => $commission,
                'description' => "Commission from {$this->name}'s purchase",
                'status' => 'credited',
                'credited_at' => now(),
            ]);

            $referrer->increment('referral_balance', $commission);
        }
    }

    /**
     * Get total earnings from referrals.
     */
    public function getTotalReferralEarningsAttribute(): float
    {
        return $this->referralRewards()->credited()->sum('amount');
    }
}
