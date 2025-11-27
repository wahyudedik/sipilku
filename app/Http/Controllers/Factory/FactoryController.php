<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Factory\FactoryRegistrationRequest;
use App\Http\Requests\Factory\UpdateFactoryRequest;
use App\Models\Factory;
use App\Models\FactoryLocation;
use App\Models\FactoryType;
use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\FactoryView;

class FactoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['show', 'index']);
    }

    /**
     * Display a listing of factories (public).
     */
    public function index(Request $request): View
    {
        $query = Factory::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['user', 'factoryType', 'primaryLocation', 'products' => function ($q) {
                $q->where('is_available', true);
            }])
            ->withCount(['products' => function ($q) {
                $q->where('is_available', true);
            }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by factory type
        if ($request->filled('factory_type')) {
            $query->whereHas('factoryType', function ($q) use ($request) {
                $q->where('slug', $request->factory_type);
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
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

        $factories = $query->paginate(12)->withQueryString();

        // Get factory types for filter
        $factoryTypes = FactoryType::where('is_active', true)->get();

        // Get unique cities and provinces for filter
        $cities = FactoryLocation::whereHas('factory', function ($q) {
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

        $provinces = FactoryLocation::whereHas('factory', function ($q) {
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

        return view('factories.index', compact('factories', 'factoryTypes', 'cities', 'provinces'));
    }

    /**
     * Show the form for creating a new factory.
     */
    public function create(): View
    {
        $factoryTypes = FactoryType::where('is_active', true)->get();
        $umkms = Umkm::where('is_active', true)->get();

        return view('factories.create', compact('factoryTypes', 'umkms'));
    }

    /**
     * Store a newly created factory.
     */
    public function store(FactoryRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['name']);
        $data['status'] = 'pending';
        $data['is_verified'] = false;
        $data['is_active'] = true;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('factories/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('factories/banners', 'public');
        }

        // Handle documents upload
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $documents[] = $document->store('factories/documents', 'public');
            }
            $data['documents'] = $documents;
        }

        // Handle certifications upload
        if ($request->hasFile('certifications')) {
            $certifications = [];
            foreach ($request->file('certifications') as $certification) {
                $certifications[] = $certification->store('factories/certifications', 'public');
            }
            $data['certifications'] = $certifications;
        }

        // Create factory
        $factory = Factory::create($data);

        // Create primary location
        if ($request->has('location')) {
            $locationData = $request->location;
            $locationData['factory_id'] = $factory->uuid;
            $locationData['is_primary'] = true;
            $locationData['is_active'] = true;
            FactoryLocation::create($locationData);
        }

        return redirect()->route('factories.my-factory')
            ->with('success', 'Pabrik berhasil didaftarkan. Menunggu persetujuan admin.');
    }

    /**
     * Display the specified factory (public).
     */
    public function show(Request $request, Factory $factory): View
    {
        // Only show approved and active factories
        if ($factory->status !== 'approved' || !$factory->is_active || !$factory->is_verified) {
            abort(404);
        }

        // Track factory view
        $this->trackFactoryView($factory, $request);

        // Load products with filters
        $productsQuery = $factory->products()->where('is_available', true);

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

        $products = $productsQuery->paginate(12)->withQueryString();

        // Load reviews with pagination
        $reviews = $factory->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10, ['*'], 'reviews_page')
            ->withQueryString();

        // Calculate rating breakdown
        $approvedReviews = $factory->approvedReviews()->get();
        $ratingBreakdown = [
            5 => $approvedReviews->where('rating', 5)->count(),
            4 => $approvedReviews->where('rating', 4)->count(),
            3 => $approvedReviews->where('rating', 3)->count(),
            2 => $approvedReviews->where('rating', 2)->count(),
            1 => $approvedReviews->where('rating', 1)->count(),
        ];

        // Calculate category averages
        $categoryRatings = [
            'product_quality' => $this->calculateCategoryAverage($approvedReviews, 'product_quality'),
            'delivery_quality' => $this->calculateCategoryAverage($approvedReviews, 'delivery_quality'),
            'service_quality' => $this->calculateCategoryAverage($approvedReviews, 'service_quality'),
            'price' => $this->calculateCategoryAverage($approvedReviews, 'price'),
        ];

        $factory->load([
            'user',
            'factoryType',
            'umkm',
            'locations' => function ($q) {
                $q->where('is_active', true);
            },
            'products' => function ($q) {
                $q->where('is_available', true)->limit(6);
            }
        ]);

        return view('factories.show', compact('factory', 'products', 'reviews', 'ratingBreakdown', 'categoryRatings'));
    }

    /**
     * Record a factory view (single per session).
     */
    protected function trackFactoryView(Factory $factory, Request $request): void
    {
        try {
            if (!$factory) {
                return;
            }

            $sessionKey = "factory_viewed_{$factory->uuid}";
            if (!session()->has($sessionKey)) {
                FactoryView::create([
                    'factory_id' => $factory->uuid,
                    'user_id' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'referrer' => $request->headers->get('referer'),
                    'viewed_at' => now(),
                ]);
                session()->put($sessionKey, true);
            }
        } catch (\Throwable $e) {
            // silent
        }
    }

    /**
     * Calculate average rating for a specific category.
     */
    private function calculateCategoryAverage($reviews, string $category): float
    {
        $ratings = $reviews->map(function ($review) use ($category) {
            return $review->ratings_breakdown[$category] ?? null;
        })->filter()->values();

        if ($ratings->isEmpty()) {
            return 0;
        }

        return round($ratings->avg(), 2);
    }

    /**
     * Display user's factory dashboard.
     */
    public function myFactory(): RedirectResponse
    {
        $factory = Auth::user()->factories()->first();

        if (!$factory) {
            return redirect()->route('factories.create')
                ->with('info', 'Anda belum memiliki pabrik. Silakan daftarkan pabrik Anda.');
        }

        return redirect()->route('factories.dashboard', $factory);
    }

    /**
     * Show the form for editing the specified factory.
     */
    public function edit(Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $factory->load('locations');
        $factoryTypes = FactoryType::where('is_active', true)->get();
        $umkms = Umkm::where('is_active', true)->get();

        return view('factories.edit', compact('factory', 'factoryTypes', 'umkms'));
    }

    /**
     * Update the specified factory.
     */
    public function update(UpdateFactoryRequest $request, Factory $factory): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($factory->logo) {
                Storage::disk('public')->delete($factory->logo);
            }
            $data['logo'] = $request->file('logo')->store('factories/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner
            if ($factory->banner) {
                Storage::disk('public')->delete($factory->banner);
            }
            $data['banner'] = $request->file('banner')->store('factories/banners', 'public');
        }

        // Handle documents upload
        if ($request->hasFile('documents')) {
            // Delete old documents
            if ($factory->documents) {
                foreach ($factory->documents as $doc) {
                    Storage::disk('public')->delete($doc);
                }
            }
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $documents[] = $document->store('factories/documents', 'public');
            }
            $data['documents'] = $documents;
        }

        // Handle certifications upload
        if ($request->hasFile('certifications')) {
            // Delete old certifications
            if ($factory->certifications) {
                foreach ($factory->certifications as $cert) {
                    Storage::disk('public')->delete($cert);
                }
            }
            $certifications = [];
            foreach ($request->file('certifications') as $certification) {
                $certifications[] = $certification->store('factories/certifications', 'public');
            }
            $data['certifications'] = $certifications;
        }

        // Update slug if name changed
        if ($data['name'] !== $factory->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        $factory->update($data);

        return redirect()->route('factories.my-factory')
            ->with('success', 'Pabrik berhasil diperbarui.');
    }

    /**
     * Remove the specified factory.
     */
    public function destroy(Factory $factory): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete files
        if ($factory->logo) {
            Storage::disk('public')->delete($factory->logo);
        }
        if ($factory->banner) {
            Storage::disk('public')->delete($factory->banner);
        }
        if ($factory->documents) {
            foreach ($factory->documents as $doc) {
                Storage::disk('public')->delete($doc);
            }
        }
        if ($factory->certifications) {
            foreach ($factory->certifications as $cert) {
                Storage::disk('public')->delete($cert);
            }
        }

        $factory->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Pabrik berhasil dihapus.');
    }
}
