<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $services = auth()->user()->services()
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('seller.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)
            ->whereIn('type', ['service', 'both'])
            ->orderBy('name')
            ->get();

        return view('seller.services.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            $data['preview_image'] = $request->file('preview_image')->store('services/previews', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('services/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        // Handle portfolio upload
        if ($request->hasFile('portfolio')) {
            $portfolioItems = [];
            foreach ($request->file('portfolio') as $index => $portfolioFile) {
                if ($portfolioFile->isValid()) {
                    $portfolioItems[] = [
                        'title' => $request->input("portfolio.{$index}.title", 'Portfolio Item'),
                        'description' => $request->input("portfolio.{$index}.description"),
                        'image' => $portfolioFile->store('services/portfolio', 'public'),
                    ];
                }
            }
            $data['portfolio'] = $portfolioItems;
        }

        // Process package prices
        if (isset($data['package_prices'])) {
            $packages = [];
            foreach ($data['package_prices'] as $package) {
                $packages[] = [
                    'name' => $package['name'],
                    'price' => $package['price'],
                    'description' => $package['description'] ?? null,
                ];
            }
            $data['package_prices'] = $packages;
        }

        // Set default status
        $data['status'] = 'pending';
        $data['user_id'] = auth()->id();

        $service = Service::create($data);

        return redirect()->route('seller.services.index')
            ->with('success', 'Jasa berhasil dibuat dan menunggu persetujuan admin.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): View
    {
        $this->authorize('view', $service);

        $service->load(['category', 'reviews.user']);

        return view('seller.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        $categories = Category::where('is_active', true)
            ->whereIn('type', ['service', 'both'])
            ->orderBy('name')
            ->get();

        return view('seller.services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $this->authorize('update', $service);

        $data = $request->validated();

        // Generate slug if changed
        if ($data['title'] !== $service->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            // Delete old image
            if ($service->preview_image) {
                Storage::disk('public')->delete($service->preview_image);
            }
            $data['preview_image'] = $request->file('preview_image')->store('services/previews', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images
            if ($service->gallery_images) {
                foreach ($service->gallery_images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('services/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        // Handle portfolio upload
        if ($request->hasFile('portfolio')) {
            // Delete old portfolio images
            if ($service->portfolio) {
                foreach ($service->portfolio as $oldPortfolio) {
                    if (isset($oldPortfolio['image'])) {
                        Storage::disk('public')->delete($oldPortfolio['image']);
                    }
                }
            }
            $portfolioItems = [];
            foreach ($request->file('portfolio') as $index => $portfolioFile) {
                if ($portfolioFile->isValid()) {
                    $portfolioItems[] = [
                        'title' => $request->input("portfolio.{$index}.title", 'Portfolio Item'),
                        'description' => $request->input("portfolio.{$index}.description"),
                        'image' => $portfolioFile->store('services/portfolio', 'public'),
                    ];
                }
            }
            $data['portfolio'] = $portfolioItems;
        }

        // Process package prices
        if (isset($data['package_prices'])) {
            $packages = [];
            foreach ($data['package_prices'] as $package) {
                $packages[] = [
                    'name' => $package['name'],
                    'price' => $package['price'],
                    'description' => $package['description'] ?? null,
                ];
            }
            $data['package_prices'] = $packages;
        }

        // Reset status to pending if previously rejected
        if ($service->status === 'rejected') {
            $data['status'] = 'pending';
            $data['rejection_reason'] = null;
        }

        $service->update($data);

        return redirect()->route('seller.services.index')
            ->with('success', 'Jasa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $this->authorize('delete', $service);

        // Delete images
        if ($service->preview_image) {
            Storage::disk('public')->delete($service->preview_image);
        }
        if ($service->gallery_images) {
            foreach ($service->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        if ($service->portfolio) {
            foreach ($service->portfolio as $portfolio) {
                if (isset($portfolio['image'])) {
                    Storage::disk('public')->delete($portfolio['image']);
                }
            }
        }

        $service->delete();

        return redirect()->route('seller.services.index')
            ->with('success', 'Jasa berhasil dihapus.');
    }
}
