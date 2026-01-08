<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\ServicePackage;
use App\Rules\SecureFileUpload;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Browse all published services.
     */
    public function index(Request $request)
    {
        $query = Service::published()
            ->with(['seller', 'category', 'packages' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            }]);

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->whereHas('packages', function ($q) use ($request) {
                $q->where('is_active', true)->where('price', '>=', $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->whereHas('packages', function ($q) use ($request) {
                $q->where('is_active', true)->where('price', '<=', $request->max_price);
            });
        }

        // Delivery time filter
        if ($request->filled('delivery_time')) {
            $query->whereHas('packages', function ($q) use ($request) {
                $q->where('is_active', true)->where('delivery_days', '<=', $request->delivery_time);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderByRaw('(SELECT MIN(price) FROM service_packages WHERE service_packages.service_id = services.id AND is_active = 1) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('(SELECT MIN(price) FROM service_packages WHERE service_packages.service_id = services.id AND is_active = 1) DESC');
                break;
            case 'rating':
                $query->orderByDesc('rating_average');
                break;
            case 'popular':
                $query->orderByDesc('orders_count');
                break;
            default:
                $query->latest();
        }

        $services = $query->paginate(12)->withQueryString();
        $categories = Category::whereHas('services', function ($q) {
            $q->published();
        })->get();

        return view('pages.services.index', compact('services', 'categories'));
    }

    /**
     * Show a single service.
     */
    public function show(Service $service)
    {
        if ($service->status !== 'published') {
            abort(404);
        }

        $service->load([
            'seller',
            'category',
            'packages' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            },
            'requirements' => function ($q) {
                $q->orderBy('sort_order');
            },
            'activeSubscriptionPlans'
        ]);

        // Get related services
        $relatedServices = Service::published()
            ->where('id', '!=', $service->id)
            ->where('category_id', $service->category_id)
            ->with(['seller', 'packages' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            }])
            ->limit(4)
            ->get();

        return view('pages.services.show', compact('service', 'relatedServices'));
    }

    /**
     * Show the order form for a package.
     */
    public function order(Service $service, ServicePackage $package)
    {
        if ($service->status !== 'published' || !$package->is_active) {
            abort(404);
        }

        // Don't allow sellers to order their own services
        if (auth()->user()->seller && auth()->user()->seller->id === $service->seller_id) {
            return redirect()->route('services.show', $service)
                ->with('error', 'You cannot order your own service.');
        }

        $service->load('requirements');

        return view('pages.services.order', compact('service', 'package'));
    }

    /**
     * Process a service order.
     */
    public function processOrder(Request $request, Service $service, ServicePackage $package, EscrowService $escrowService)
    {
        if ($service->status !== 'published' || !$package->is_active) {
            abort(404);
        }

        // Validate requirements
        $rules = [];
        $requirementsData = [];

        foreach ($service->requirements as $requirement) {
            $key = "requirements.{$requirement->id}";

            if ($requirement->is_required) {
                $rules[$key] = 'required';
            } else {
                $rules[$key] = 'nullable';
            }

            if ($requirement->type === 'file') {
                $rules[$key] = [$rules[$key], 'file', SecureFileUpload::attachment(10)];
            }
        }

        $validated = $request->validate($rules);

        // Process requirements data
        foreach ($service->requirements as $requirement) {
            $value = $request->input("requirements.{$requirement->id}");

            if ($requirement->type === 'file' && $request->hasFile("requirements.{$requirement->id}")) {
                $file = $request->file("requirements.{$requirement->id}");
                $path = $file->store('service-requirements/' . $service->id, 'public');
                $value = $path;
            }

            $requirementsData[$requirement->id] = [
                'question' => $requirement->question,
                'answer' => $value,
                'type' => $requirement->type,
            ];
        }

        try {
            DB::beginTransaction();

            $fees = $escrowService->calculateFees($package->price, 'service');

            // Create the service order
            $order = ServiceOrder::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $service->seller_id,
                'service_id' => $service->id,
                'service_package_id' => $package->id,
                'title' => $service->name . ' - ' . $package->name,
                'description' => $package->description,
                'price' => $package->price,
                'platform_fee' => $fees['platform_fee'],
                'seller_amount' => $fees['seller_amount'],
                'delivery_days' => $package->delivery_days,
                'revisions_allowed' => $package->revisions,
                'status' => 'pending_payment',
                'requirements_data' => $requirementsData,
            ]);

            DB::commit();

            // Redirect to escrow checkout
            return redirect()->route('escrow.checkout.service-order', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create order. Please try again.')
                ->withInput();
        }
    }
}
