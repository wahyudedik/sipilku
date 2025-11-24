<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of approved services.
     */
    public function index(Request $request): View
    {
        $query = Service::where('status', 'approved')
            ->with(['user', 'category'])
            ->latest();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by price range (minimum package price)
        // Note: This is a simplified filter - for production, consider adding a min_price column
        if ($request->has('min_price') && $request->min_price) {
            $query->whereRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(package_prices, "$[0].price")) AS DECIMAL(15,2)) >= ?', [$request->min_price]);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->whereRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(package_prices, "$[0].price")) AS DECIMAL(15,2)) <= ?', [$request->max_price]);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        match($sort) {
            'price_low' => $query->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(package_prices, "$[0].price")) AS DECIMAL(15,2)) ASC'),
            'price_high' => $query->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(package_prices, "$[0].price")) AS DECIMAL(15,2)) DESC'),
            'rating' => $query->orderBy('rating', 'desc'),
            'orders' => $query->orderBy('completed_orders', 'desc'),
            default => $query->latest(),
        };

        $services = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)
            ->whereIn('type', ['service', 'both'])
            ->orderBy('name')
            ->get();

        return view('services.index', compact('services', 'categories'));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): View
    {
        // Only show approved services
        if ($service->status !== 'approved') {
            abort(404);
        }

        $service->load(['user', 'category', 'reviews' => function($query) {
            $query->where('is_approved', true)->with('user');
        }]);

        // Get related services (same category, exclude current)
        $relatedServices = Service::where('status', 'approved')
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->with('category')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('services.show', compact('service', 'relatedServices'));
    }
}
