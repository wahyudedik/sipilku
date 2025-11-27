<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display store reviews management page.
     */
    public function index(Request $request): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        $query = StoreReview::where('store_id', $store->uuid)
            ->with('user');

        // Filter by approval status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => StoreReview::where('store_id', $store->uuid)->count(),
            'approved' => StoreReview::where('store_id', $store->uuid)->where('is_approved', true)->count(),
            'pending' => StoreReview::where('store_id', $store->uuid)->where('is_approved', false)->count(),
            'average_rating' => StoreReview::where('store_id', $store->uuid)
                ->where('is_approved', true)
                ->avg('rating') ?? 0,
        ];

        return view('store.reviews.index', compact('reviews', 'store', 'stats'));
    }

    /**
     * Show review details.
     */
    public function show(StoreReview $review): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store || $review->store_id !== $store->uuid) {
            abort(403, 'Unauthorized');
        }

        $review->load('user');

        return view('store.reviews.show', compact('review', 'store'));
    }
}

