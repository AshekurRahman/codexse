<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\ServiceRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->seller->services()
            ->with(['category', 'packages' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $services = $query->latest()->paginate(10);

        return view('seller.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('seller.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'required|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'tags' => 'nullable|string|max:500',

            // Packages
            'packages' => 'required|array|min:1',
            'packages.*.name' => 'required|string|max:100',
            'packages.*.tier' => 'required|in:basic,standard,premium',
            'packages.*.price' => 'required|numeric|min:5',
            'packages.*.description' => 'required|string|max:500',
            'packages.*.delivery_days' => 'required|integer|min:1|max:365',
            'packages.*.revisions' => 'nullable|integer|min:0|max:99',
            'packages.*.deliverables' => 'nullable|array',

            // Requirements
            'requirements' => 'nullable|array',
            'requirements.*.question' => 'required|string|max:500',
            'requirements.*.type' => 'required|in:text,textarea,select,multiple_select,file',
            'requirements.*.options' => 'nullable|array',
            'requirements.*.is_required' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $seller = auth()->user()->seller;

            // Handle thumbnail upload
            $thumbnailPath = $request->file('thumbnail')->store('services/thumbnails', 'public');

            // Handle gallery images
            $galleryImages = [];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $galleryImages[] = $image->store('services/gallery', 'public');
                }
            }

            // Create service
            $service = $seller->services()->create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'short_description' => $validated['short_description'] ?? null,
                'category_id' => $validated['category_id'],
                'thumbnail' => $thumbnailPath,
                'gallery_images' => $galleryImages,
                'video_url' => $validated['video_url'] ?? null,
                'tags' => $validated['tags'] ?? null,
                'status' => 'pending',
            ]);

            // Create packages
            foreach ($validated['packages'] as $index => $packageData) {
                $service->packages()->create([
                    'name' => $packageData['name'],
                    'tier' => $packageData['tier'],
                    'price' => $packageData['price'],
                    'description' => $packageData['description'],
                    'delivery_days' => $packageData['delivery_days'],
                    'revisions' => $packageData['revisions'] ?? 0,
                    'deliverables' => $packageData['deliverables'] ?? [],
                    'is_active' => true,
                ]);
            }

            // Create requirements
            if (!empty($validated['requirements'])) {
                foreach ($validated['requirements'] as $index => $reqData) {
                    $service->requirements()->create([
                        'question' => $reqData['question'],
                        'type' => $reqData['type'],
                        'options' => $reqData['options'] ?? null,
                        'is_required' => $reqData['is_required'] ?? false,
                        'sort_order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('seller.services.index')
                ->with('success', 'Service created successfully and is pending review.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create service. Please try again.')
                ->withInput();
        }
    }

    public function show(Service $service)
    {
        $this->authorizeService($service);

        $service->load(['packages', 'requirements', 'orders' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('seller.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $this->authorizeService($service);

        $service->load(['packages', 'requirements']);
        $categories = Category::orderBy('name')->get();

        return view('seller.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeService($service);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'tags' => 'nullable|string|max:500',

            'packages' => 'required|array|min:1',
            'packages.*.id' => 'nullable|exists:service_packages,id',
            'packages.*.name' => 'required|string|max:100',
            'packages.*.tier' => 'required|in:basic,standard,premium',
            'packages.*.price' => 'required|numeric|min:5',
            'packages.*.description' => 'required|string|max:500',
            'packages.*.delivery_days' => 'required|integer|min:1|max:365',
            'packages.*.revisions' => 'nullable|integer|min:0|max:99',
            'packages.*.deliverables' => 'nullable|array',
            'packages.*.is_active' => 'boolean',

            'requirements' => 'nullable|array',
            'requirements.*.id' => 'nullable|exists:service_requirements,id',
            'requirements.*.question' => 'required|string|max:500',
            'requirements.*.type' => 'required|in:text,textarea,select,multiple_select,file',
            'requirements.*.options' => 'nullable|array',
            'requirements.*.is_required' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Handle thumbnail upload
            $updateData = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'short_description' => $validated['short_description'] ?? null,
                'category_id' => $validated['category_id'],
                'video_url' => $validated['video_url'] ?? null,
                'tags' => $validated['tags'] ?? null,
            ];

            if ($request->hasFile('thumbnail')) {
                if ($service->thumbnail) {
                    Storage::disk('public')->delete($service->thumbnail);
                }
                $updateData['thumbnail'] = $request->file('thumbnail')->store('services/thumbnails', 'public');
            }

            // Handle gallery images
            if ($request->hasFile('gallery_images')) {
                $galleryImages = $service->gallery_images ?? [];
                foreach ($request->file('gallery_images') as $image) {
                    $galleryImages[] = $image->store('services/gallery', 'public');
                }
                $updateData['gallery_images'] = $galleryImages;
            }

            $service->update($updateData);

            // Update packages
            $existingPackageIds = [];
            foreach ($validated['packages'] as $packageData) {
                if (!empty($packageData['id'])) {
                    $package = $service->packages()->find($packageData['id']);
                    if ($package) {
                        $package->update([
                            'name' => $packageData['name'],
                            'tier' => $packageData['tier'],
                            'price' => $packageData['price'],
                            'description' => $packageData['description'],
                            'delivery_days' => $packageData['delivery_days'],
                            'revisions' => $packageData['revisions'] ?? 0,
                            'deliverables' => $packageData['deliverables'] ?? [],
                            'is_active' => $packageData['is_active'] ?? true,
                        ]);
                        $existingPackageIds[] = $package->id;
                    }
                } else {
                    $newPackage = $service->packages()->create([
                        'name' => $packageData['name'],
                        'tier' => $packageData['tier'],
                        'price' => $packageData['price'],
                        'description' => $packageData['description'],
                        'delivery_days' => $packageData['delivery_days'],
                        'revisions' => $packageData['revisions'] ?? 0,
                        'deliverables' => $packageData['deliverables'] ?? [],
                        'is_active' => $packageData['is_active'] ?? true,
                    ]);
                    $existingPackageIds[] = $newPackage->id;
                }
            }
            // Remove deleted packages
            $service->packages()->whereNotIn('id', $existingPackageIds)->delete();

            // Update requirements
            $existingReqIds = [];
            if (!empty($validated['requirements'])) {
                foreach ($validated['requirements'] as $index => $reqData) {
                    if (!empty($reqData['id'])) {
                        $req = $service->requirements()->find($reqData['id']);
                        if ($req) {
                            $req->update([
                                'question' => $reqData['question'],
                                'type' => $reqData['type'],
                                'options' => $reqData['options'] ?? null,
                                'is_required' => $reqData['is_required'] ?? false,
                                'sort_order' => $index,
                            ]);
                            $existingReqIds[] = $req->id;
                        }
                    } else {
                        $newReq = $service->requirements()->create([
                            'question' => $reqData['question'],
                            'type' => $reqData['type'],
                            'options' => $reqData['options'] ?? null,
                            'is_required' => $reqData['is_required'] ?? false,
                            'sort_order' => $index,
                        ]);
                        $existingReqIds[] = $newReq->id;
                    }
                }
            }
            $service->requirements()->whereNotIn('id', $existingReqIds)->delete();

            DB::commit();

            return redirect()->route('seller.services.index')
                ->with('success', 'Service updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update service. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Service $service)
    {
        $this->authorizeService($service);

        // Don't allow deletion if there are active orders
        if ($service->orders()->active()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete service with active orders.');
        }

        $service->delete();

        return redirect()->route('seller.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    public function toggleStatus(Service $service)
    {
        $this->authorizeService($service);

        if ($service->status === 'published') {
            $service->update(['status' => 'paused']);
            $message = 'Service paused.';
        } elseif ($service->status === 'paused') {
            $service->update(['status' => 'published']);
            $message = 'Service published.';
        } else {
            return redirect()->back()->with('error', 'Cannot change status of pending or rejected services.');
        }

        return redirect()->back()->with('success', $message);
    }

    protected function authorizeService(Service $service): void
    {
        if ($service->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }
    }
}
