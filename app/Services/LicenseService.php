<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class LicenseService
{
    // Characters to use in license keys (excluding confusing ones: 0, O, 1, I, L)
    protected const CHARS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /**
     * Generate a unique license key in XXXX-XXXX-XXXX-XXXX format
     */
    public function generate(): string
    {
        do {
            $key = $this->generateKey();
        } while (License::where('license_key', $key)->exists());

        return $key;
    }

    /**
     * Generate a single key without uniqueness check
     */
    protected function generateKey(): string
    {
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segment = '';
            for ($j = 0; $j < 4; $j++) {
                $segment .= self::CHARS[random_int(0, strlen(self::CHARS) - 1)];
            }
            $segments[] = $segment;
        }

        return implode('-', $segments);
    }

    /**
     * Create a license for an order item
     */
    public function createForOrderItem(OrderItem $orderItem, array $options = []): License
    {
        return License::create([
            'order_item_id' => $orderItem->id,
            'user_id' => $orderItem->order->user_id,
            'product_id' => $orderItem->product_id,
            'license_key' => $this->generate(),
            'license_type' => $orderItem->license_type ?? 'regular',
            'status' => 'active',
            'max_activations' => $options['max_activations'] ?? $this->getMaxActivationsForType($orderItem->license_type),
            'expires_at' => $options['expires_at'] ?? null,
        ]);
    }

    /**
     * Get default max activations based on license type
     */
    protected function getMaxActivationsForType(string $licenseType): int
    {
        return match ($licenseType) {
            'extended' => 5,
            'unlimited' => 0, // 0 means unlimited
            default => 1, // regular
        };
    }

    /**
     * Validate a license key
     */
    public function validate(string $licenseKey, ?int $productId = null): array
    {
        $license = License::where('license_key', $licenseKey)
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->with(['product', 'user'])
            ->first();

        if (!$license) {
            return [
                'valid' => false,
                'error' => 'License key not found',
            ];
        }

        if ($license->status === 'revoked') {
            return [
                'valid' => false,
                'error' => 'License has been revoked',
                'license' => $this->formatLicenseResponse($license),
            ];
        }

        if ($license->status === 'suspended') {
            return [
                'valid' => false,
                'error' => 'License is suspended',
                'license' => $this->formatLicenseResponse($license),
            ];
        }

        if ($license->isExpired()) {
            return [
                'valid' => false,
                'error' => 'License has expired',
                'license' => $this->formatLicenseResponse($license),
            ];
        }

        return [
            'valid' => true,
            'license' => $this->formatLicenseResponse($license),
        ];
    }

    /**
     * Activate a license
     */
    public function activate(License $license, array $metadata = []): array
    {
        if (!$license->isActive()) {
            return [
                'success' => false,
                'error' => 'License is not active',
            ];
        }

        if (!$license->canActivate()) {
            return [
                'success' => false,
                'error' => 'Maximum activations reached',
                'activations_used' => $license->activations_count,
                'activations_max' => $license->max_activations,
            ];
        }

        // Check if this domain/machine is already activated
        $existingActivation = $license->activations()
            ->active()
            ->when(isset($metadata['domain']), fn($q) => $q->where('domain', $metadata['domain']))
            ->when(isset($metadata['machine_id']), fn($q) => $q->where('machine_id', $metadata['machine_id']))
            ->first();

        if ($existingActivation) {
            return [
                'success' => true,
                'message' => 'Already activated',
                'activation_id' => $existingActivation->id,
                'activations_remaining' => $license->activationsRemaining(),
            ];
        }

        // Create new activation
        $activation = LicenseActivation::create([
            'license_id' => $license->id,
            'domain' => $metadata['domain'] ?? null,
            'ip_address' => $metadata['ip_address'] ?? request()->ip(),
            'machine_id' => $metadata['machine_id'] ?? null,
        ]);

        // Increment activation count
        $license->increment('activations_count');

        // Set activated_at if first activation
        if ($license->activated_at === null) {
            $license->update(['activated_at' => now()]);
        }

        return [
            'success' => true,
            'activation_id' => $activation->id,
            'activations_remaining' => $license->fresh()->activationsRemaining(),
        ];
    }

    /**
     * Deactivate a license activation
     */
    public function deactivate(LicenseActivation $activation): bool
    {
        $activation->deactivate();
        return true;
    }

    /**
     * Suspend a license
     */
    public function suspend(License $license, ?string $reason = null): bool
    {
        $license->update([
            'status' => 'suspended',
            'notes' => $reason ? ($license->notes . "\n[Suspended] " . $reason) : $license->notes,
        ]);

        return true;
    }

    /**
     * Revoke a license
     */
    public function revoke(License $license, ?string $reason = null): bool
    {
        $license->update([
            'status' => 'revoked',
            'notes' => $reason ? ($license->notes . "\n[Revoked] " . $reason) : $license->notes,
        ]);

        // Deactivate all activations
        $license->activations()->active()->update([
            'is_active' => false,
            'deactivated_at' => now(),
        ]);

        return true;
    }

    /**
     * Reactivate a suspended license
     */
    public function reactivate(License $license): bool
    {
        if ($license->status !== 'suspended') {
            return false;
        }

        $license->update([
            'status' => 'active',
            'notes' => $license->notes . "\n[Reactivated] " . now()->toDateTimeString(),
        ]);

        return true;
    }

    /**
     * Regenerate a license key
     */
    public function regenerateKey(License $license): string
    {
        $newKey = $this->generate();

        $license->update([
            'license_key' => $newKey,
            'notes' => $license->notes . "\n[Key Regenerated] " . now()->toDateTimeString(),
        ]);

        return $newKey;
    }

    /**
     * Format license for API response
     */
    protected function formatLicenseResponse(License $license): array
    {
        return [
            'license_key' => $license->license_key,
            'license_type' => $license->license_type,
            'status' => $license->status,
            'product' => [
                'id' => $license->product_id,
                'name' => $license->product->name ?? null,
            ],
            'activations' => [
                'used' => $license->activations_count,
                'max' => $license->max_activations,
                'remaining' => $license->activationsRemaining(),
            ],
            'expires_at' => $license->expires_at?->toIso8601String(),
            'activated_at' => $license->activated_at?->toIso8601String(),
        ];
    }
}
