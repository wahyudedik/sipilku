<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreRegistrationRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Models\Store;
use App\Models\StoreLocation;
use App\Models\StoreView;
use App\Services\StoreRecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth as AuthFacade;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['show', 'index']);
    }

    /**
     * Display a listing of stores (public).
     */
    public function index(Request $request): View
    {
        $query = Store::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['user', 'primaryLocation', 'products' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by location (city/province)
        if ($request->filled('city')) {
            $query->whereHas('locations', function ($q) use ($request) {
                $q->where('city', 'like', "%{$request->city}%");
            });
        }

        if ($request->filled('province')) {
            $query->whereHas('locations', function ($q) use ($request) {
                $q->where('province', 'like', "%{$request->province}%");
            });
        }

        // Filter by rating
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'rating' => $query->orderBy('rating', 'desc')->orderBy('total_reviews', 'desc'),
            'reviews' => $query->orderBy('total_reviews', 'desc'),
            'products' => $query->orderBy('products_count', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->latest(),
        };

        $stores = $query->paginate(12)->withQueryString();

        // Get unique cities and provinces for filter
        $cities = StoreLocation::whereHas('store', function ($q) {
            $q->where('status', 'approved')
                ->where('is_active', true)
                ->where('is_verified', true);
        })
            ->where('is_active', true)
            ->distinct()
            ->pluck('city')
            ->filter()
            ->sort()
            ->values();

        $provinces = StoreLocation::whereHas('store', function ($q) {
            $q->where('status', 'approved')
                ->where('is_active', true)
                ->where('is_verified', true);
        })
            ->where('is_active', true)
            ->distinct()
            ->pluck('province')
            ->filter()
            ->sort()
            ->values();

        return view('stores.index', compact('stores', 'cities', 'provinces'));
    }

    /**
     * Show the form for creating a new store.
     */
    public function create(): View|RedirectResponse
    {
        // Check if user already has a store
        if (Auth::user()->stores()->exists()) {
            return redirect()->route('stores.my-store')
                ->with('info', 'Anda sudah memiliki toko. Anda dapat mengedit toko yang ada.');
        }

        return view('stores.create');
    }

    /**
     * Store a newly created store.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        // Check if user already has a store
        if (Auth::user()->stores()->exists()) {
            return redirect()->route('stores.my-store')
                ->with('error', 'Anda sudah memiliki toko.');
        }

        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['name']);
        $data['status'] = 'pending';
        $data['is_verified'] = false;
        $data['is_active'] = true;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('stores/banners', 'public');
        }

        // Handle documents upload
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $documents[] = $document->store('stores/documents', 'public');
            }
            $data['documents'] = $documents;
        }

        // Create store
        $store = Store::create($data);

        // Create primary location
        if ($request->has('location')) {
            $locationData = $request->location;
            $locationData['store_id'] = $store->id;
            $locationData['is_primary'] = true;
            $locationData['is_active'] = true;
            StoreLocation::create($locationData);
        }

        return redirect()->route('stores.my-store')
            ->with('success', 'Toko berhasil didaftarkan. Menunggu persetujuan admin.');
    }

    /**
     * Display the specified store (public).
     */
    public function show(Request $request, Store $store): View
    {
        // Only show approved and active stores
        if ($store->status !== 'approved' || !$store->is_active || !$store->is_verified) {
            abort(404);
        }

        // Track store view
        $this->trackStoreView($store, $request);

        // Load products with filters
        $productsQuery = $store->products()->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $productsQuery->where('store_category_id', $request->category);
        }

        // Search products
        if ($request->filled('product_search')) {
            $productsQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->product_search}%")
                    ->orWhere('description', 'like', "%{$request->product_search}%");
            });
        }

        // Sort products
        $productSort = $request->get('product_sort', 'latest');
        match ($productSort) {
            'price_low' => $productsQuery->orderByRaw('COALESCE(discount_price, price) ASC'),
            'price_high' => $productsQuery->orderByRaw('COALESCE(discount_price, price) DESC'),
            'name' => $productsQuery->orderBy('name', 'asc'),
            default => $productsQuery->latest(),
        };

        $products = $productsQuery->with('category')->paginate(12)->withQueryString();

        // Load reviews with pagination
        $reviews = $store->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10, ['*'], 'reviews_page')
            ->withQueryString();

        // Calculate rating breakdown
        $ratingBreakdown = [
            5 => $store->approvedReviews()->where('rating', 5)->count(),
            4 => $store->approvedReviews()->where('rating', 4)->count(),
            3 => $store->approvedReviews()->where('rating', 3)->count(),
            2 => $store->approvedReviews()->where('rating', 2)->count(),
            1 => $store->approvedReviews()->where('rating', 1)->count(),
        ];

        $store->load([
            'user',
            'locations' => function ($q) {
                $q->where('is_active', true);
            },
            'products' => function ($q) {
                $q->where('is_active', true)->limit(6);
            }
        ]);

        // Get categories for filter
        $categories = \App\Models\StoreCategory::whereHas('products', function ($q) use ($store) {
            $q->where('store_id', $store->id)->where('is_active', true);
        })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get location-based recommendations
        $recommendationService = new StoreRecommendationService();
        $recommendations = collect();

        // Get primary location for recommendations
        $primaryLocation = $store->primaryLocation()->where('is_active', true)->first();
        if ($primaryLocation && $primaryLocation->hasCoordinates()) {
            $recommendations = $recommendationService->getSimilarStoreRecommendations($store, 5);
        }

        return view('stores.show', compact('store', 'products', 'reviews', 'ratingBreakdown', 'categories', 'recommendations'));
    }

    /**
     * Record a store view (one per session).
     */
    protected function trackStoreView(Store $store, Request $request): void
    {
        try {
            if (!$store) return;

            $sessionKey = "store_viewed_{$store->uuid}";
            if (!session()->has($sessionKey)) {
                StoreView::create([
                    'store_id' => $store->uuid,
                    'user_id' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'referrer' => $request->headers->get('referer'),
                    'viewed_at' => now(),
                ]);
                session()->put($sessionKey, true);
            }
        } catch (\Throwable $e) {
            // don't fail the request if analytics fails
        }
    }

    /**
     * Display user's store dashboard.
     */
    public function myStore(): View|RedirectResponse
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            return redirect()->route('stores.create')
                ->with('info', 'Anda belum memiliki toko. Silakan daftarkan toko Anda.');
        }

        $store->load(['locations', 'products', 'materialRequests']);

        return view('stores.my-store', compact('store'));
    }

    /**
     * Show the form for editing the specified store.
     */
    public function edit(Store $store): View
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $store->load('locations');
        return view('stores.edit', compact('store'));
    }

    /**
     * Update the specified store.
     */
    public function update(UpdateStoreRequest $request, Store $store): RedirectResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner
            if ($store->banner) {
                Storage::disk('public')->delete($store->banner);
            }
            $data['banner'] = $request->file('banner')->store('stores/banners', 'public');
        }

        // Handle documents upload
        if ($request->hasFile('documents')) {
            // Delete old documents
            if ($store->documents) {
                foreach ($store->documents as $doc) {
                    Storage::disk('public')->delete($doc);
                }
            }
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $documents[] = $document->store('stores/documents', 'public');
            }
            $data['documents'] = $documents;
        }

        // Update slug if name changed
        if ($data['name'] !== $store->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        $store->update($data);

        return redirect()->route('stores.my-store')
            ->with('success', 'Toko berhasil diperbarui.');
    }

    /**
     * Remove the specified store.
     */
    public function destroy(Store $store): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete files
        if ($store->logo) {
            Storage::disk('public')->delete($store->logo);
        }
        if ($store->banner) {
            Storage::disk('public')->delete($store->banner);
        }
        if ($store->documents) {
            foreach ($store->documents as $doc) {
                Storage::disk('public')->delete($doc);
            }
        }

        $store->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Toko berhasil dihapus.');
    }
}
