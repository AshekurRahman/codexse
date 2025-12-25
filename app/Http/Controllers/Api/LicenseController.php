<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Services\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Validate a license key
     *
     * POST /api/license/validate
     * Body: { "license_key": "XXXX-XXXX-XXXX-XXXX", "product_id": 1 (optional) }
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|size:19',
            'product_id' => 'nullable|integer|exists:products,id',
        ]);

        $result = $this->licenseService->validate(
            $request->input('license_key'),
            $request->input('product_id')
        );

        return response()->json($result, $result['valid'] ? 200 : 400);
    }

    /**
     * Activate a license
     *
     * POST /api/license/activate
     * Body: { "license_key": "XXXX-XXXX-XXXX-XXXX", "domain": "example.com", "machine_id": "xxx" }
     */
    public function activate(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|size:19',
            'domain' => 'nullable|string|max:255',
            'machine_id' => 'nullable|string|max:255',
        ]);

        $license = License::where('license_key', $request->input('license_key'))->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'error' => 'License key not found',
            ], 404);
        }

        $result = $this->licenseService->activate($license, [
            'domain' => $request->input('domain'),
            'machine_id' => $request->input('machine_id'),
            'ip_address' => $request->ip(),
        ]);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Deactivate a license activation
     *
     * POST /api/license/deactivate
     * Body: { "license_key": "XXXX-XXXX-XXXX-XXXX", "activation_id": 1 }
     */
    public function deactivate(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|size:19',
            'activation_id' => 'required|integer',
        ]);

        $license = License::where('license_key', $request->input('license_key'))->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'error' => 'License key not found',
            ], 404);
        }

        $activation = $license->activations()->find($request->input('activation_id'));

        if (!$activation) {
            return response()->json([
                'success' => false,
                'error' => 'Activation not found',
            ], 404);
        }

        $this->licenseService->deactivate($activation);

        return response()->json([
            'success' => true,
            'message' => 'License deactivated successfully',
        ]);
    }

    /**
     * Get license details (for authenticated users)
     *
     * GET /api/license/{license_key}
     */
    public function show(string $licenseKey): JsonResponse
    {
        $license = License::where('license_key', $licenseKey)
            ->with(['product', 'activations'])
            ->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'error' => 'License not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'license' => [
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
                    'list' => $license->activations->map(fn($a) => [
                        'id' => $a->id,
                        'domain' => $a->domain,
                        'ip_address' => $a->ip_address,
                        'is_active' => $a->is_active,
                        'created_at' => $a->created_at->toIso8601String(),
                    ]),
                ],
                'expires_at' => $license->expires_at?->toIso8601String(),
                'activated_at' => $license->activated_at?->toIso8601String(),
                'created_at' => $license->created_at->toIso8601String(),
            ],
        ]);
    }
}
