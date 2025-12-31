<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'verification_type',
        'document_type',
        'document_number',
        'document_front',
        'document_back',
        'selfie_with_document',
        'full_name',
        'date_of_birth',
        'country',
        'address',
        'status',
        'rejection_reason',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'reviewed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getDocumentFrontUrlAttribute(): ?string
    {
        return $this->document_front ? asset('storage/' . $this->document_front) : null;
    }

    public function getDocumentBackUrlAttribute(): ?string
    {
        return $this->document_back ? asset('storage/' . $this->document_back) : null;
    }

    public function getSelfieUrlAttribute(): ?string
    {
        return $this->selfie_with_document ? asset('storage/' . $this->selfie_with_document) : null;
    }

    public function approve(User $admin, ?string $notes = null, ?\DateTime $expiresAt = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
            'expires_at' => $expiresAt,
        ]);

        // Update seller verification status
        $this->seller->update([
            'is_verified' => true,
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);

        // Add verification badge
        $badges = $this->seller->verification_badges ?? [];
        $badges[$this->verification_type] = [
            'verified_at' => now()->toISOString(),
            'expires_at' => $expiresAt?->toISOString(),
        ];
        $this->seller->update(['verification_badges' => $badges]);
    }

    public function reject(User $admin, string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);

        // Update seller verification status
        $this->seller->update([
            'verification_status' => 'rejected',
        ]);
    }

    public function markUnderReview(): void
    {
        $this->update(['status' => 'under_review']);
    }

    public static function getDocumentTypeOptions(): array
    {
        return [
            'passport' => 'Passport',
            'national_id' => 'National ID Card',
            'drivers_license' => 'Driver\'s License',
            'business_license' => 'Business License',
            'utility_bill' => 'Utility Bill',
            'bank_statement' => 'Bank Statement',
        ];
    }

    public static function getVerificationTypeOptions(): array
    {
        return [
            'identity' => 'Identity Verification',
            'business' => 'Business Verification',
            'address' => 'Address Verification',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }
}
