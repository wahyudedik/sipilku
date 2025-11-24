<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of approved products.
     */
    public function index(Request $request): View
    {
        $query = Product::where('status', 'approved')
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

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where(function($q) use ($request) {
                $q->whereRaw('COALESCE(discount_price, price) >= ?', [$request->min_price]);
            });
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where(function($q) use ($request) {
                $q->whereRaw('COALESCE(discount_price, price) <= ?', [$request->max_price]);
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        match($sort) {
            'price_low' => $query->orderByRaw('COALESCE(discount_price, price) ASC'),
            'price_high' => $query->orderByRaw('COALESCE(discount_price, price) DESC'),
            'rating' => $query->orderBy('rating', 'desc'),
            'sales' => $query->orderBy('sales_count', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)
            ->whereIn('type', ['product', 'both'])
            ->orderBy('name')
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        // Only show approved products
        if ($product->status !== 'approved') {
            abort(404);
        }

        $product->load(['user', 'category', 'reviews' => function($query) {
            $query->where('is_approved', true)->with('user');
        }]);

        // Get related products (same category, exclude current)
        $relatedProducts = Product::where('status', 'approved')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Increment view count (optional, bisa ditambahkan field view_count)
        // $product->increment('view_count');

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
