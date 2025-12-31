<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SellerApplicationController extends Controller
{
    /**
     * Display the become a seller landing page.
     */
    public function index(): View
    {
        // Get commission rates from settings
        $defaultCommission = (int) Setting::get('default_commission_rate', 20);
        $newSellerCommission = (int) Setting::get('new_seller_commission_rate', 25);
        $establishedSellerCommission = (int) Setting::get('established_seller_commission_rate', 20);
        $topSellerCommission = (int) Setting::get('top_seller_commission_rate', 15);
        $useLevelRates = Setting::get('use_seller_level_rates', false);

        // Calculate revenue percentages (100 - commission)
        $commissionRates = [
            'default' => $defaultCommission,
            'new_seller' => $newSellerCommission,
            'established_seller' => $establishedSellerCommission,
            'top_seller' => $topSellerCommission,
            'use_level_rates' => $useLevelRates,
            'default_revenue' => 100 - $defaultCommission,
            'new_seller_revenue' => 100 - $newSellerCommission,
            'established_seller_revenue' => 100 - $establishedSellerCommission,
            'top_seller_revenue' => 100 - $topSellerCommission,
        ];

        return view('pages.become-seller', compact('commissionRates'));
    }

    /**
     * Display the seller application form.
     */
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        // If already an approved seller, redirect to dashboard
        if ($user->seller && $user->seller->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        // If application is pending, show pending page
        if ($user->seller && $user->seller->status === 'pending') {
            return redirect()->route('seller.pending');
        }

        return view('pages.seller.apply', [
            'existingApplication' => $user->seller,
        ]);
    }

    /**
     * Store a new seller application.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Check if user already has an approved seller account
        if ($user->seller && $user->seller->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255|unique:sellers,store_name' . ($user->seller ? ',' . $user->seller->id : ''),
            'description' => 'required|string|min:50|max:1000',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'portfolio_url' => 'nullable|url|max:255',
            'product_types' => 'required|array|min:1',
            'product_types.*' => 'string|in:ui-kits,templates,icons,illustrations,themes,code,fonts,mockups,other',
            'experience' => 'required|string|in:beginner,intermediate,expert',
            'terms' => 'required|accepted',
        ], [
            'store_name.unique' => 'This store name is already taken. Please choose another.',
            'description.min' => 'Please provide at least 50 characters describing your store and products.',
            'terms.accepted' => 'You must agree to the Seller Terms and Conditions.',
        ]);

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('sellers/logos', 'public');
        }

        // Create or update seller application
        $sellerData = [
            'store_name' => $validated['store_name'],
            'description' => $validated['description'],
            'website' => $validated['website'] ?? null,
            'logo' => $logoPath,
            'status' => 'pending',
        ];

        if ($user->seller) {
            // Update existing rejected application
            if ($user->seller->logo && $logoPath) {
                Storage::disk('public')->delete($user->seller->logo);
            }
            $user->seller->update($sellerData);
        } else {
            // Create new application (commission rate is determined by admin settings)
            $user->seller()->create($sellerData);
        }

        return redirect()->route('seller.pending')->with('success', 'Your seller application has been submitted successfully!');
    }

    /**
     * Display the application pending page.
     */
    public function pending(): View|RedirectResponse
    {
        $user = auth()->user();

        if (!$user->seller) {
            return redirect()->route('seller.apply');
        }

        if ($user->seller->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        return view('pages.seller.pending', [
            'seller' => $user->seller,
        ]);
    }
}
