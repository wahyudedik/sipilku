<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoreReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Display reviews for a store.
     */
    public function index(Store $store): View
    {
        $reviews = StoreReview::where('store_id', $store->uuid)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(10);

        // Calculate average rating
        $averageRating = StoreReview::where('store_id', $store->uuid)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        // Rating breakdown
        $ratingBreakdown = [
            5 => StoreReview::where('store_id', $store->uuid)->where('is_approved', true)->where('rating', 5)->count(),
            4 => StoreReview::where('store_id', $store->uuid)->where('is_approved', true)->where('rating', 4)->count(),
            3 => StoreReview::where('store_id', $store->uuid)->where('is_approved', true)->where('rating', 3)->count(),
            2 => StoreReview::where('store_id', $store->uuid)->where('is_approved', true)->where('rating', 2)->count(),
            1 => StoreReview::where('store_id', $store->uuid)->where('is_approved', true)->where('rating', 1)->count(),
        ];

        return view('store-reviews.index', compact('store', 'reviews', 'averageRating', 'ratingBreakdown'));
    }

    /**
     * Show the form for creating a new review.
     */
    public function create(Store $store): View
    {
        // Check if user already reviewed this store
        $existingReview = StoreReview::where('store_id', $store->uuid)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()->route('store-reviews.edit', [$store, $existingReview])
                ->with('info', 'Anda sudah memberikan review untuk toko ini. Anda dapat mengedit review Anda.');
        }

        return view('store-reviews.create', compact('store'));
    }

    /**
     * Store a newly created review.
     */
    public function store(Request $request, Store $store): RedirectResponse
    {
        // Check if user already reviewed this store
        $existingReview = StoreReview::where('store_id', $store->uuid)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'Anda sudah memberikan review untuk toko ini.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'ratings_breakdown' => ['nullable', 'array'],
            'ratings_breakdown.product_quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.service' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.price' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $review = StoreReview::create([
            'store_id' => $store->uuid,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'ratings_breakdown' => $validated['ratings_breakdown'] ?? null,
            'is_verified_purchase' => false, // Can be set based on order history
            'is_approved' => true, // Auto-approve, can be changed to require moderation
        ]);

        // Update store rating
        $this->updateStoreRating($store);

        return redirect()->route('stores.show', $store)
            ->with('success', 'Review berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Store $store, StoreReview $review): View
    {
        if ($review->user_id !== Auth::id() || $review->store_id !== $store->uuid) {
            abort(403, 'Unauthorized action.');
        }

        return view('store-reviews.edit', compact('store', 'review'));
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Store $store, StoreReview $review): RedirectResponse
    {
        if ($review->user_id !== Auth::id() || $review->store_id !== $store->uuid) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'ratings_breakdown' => ['nullable', 'array'],
            'ratings_breakdown.product_quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.service' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.price' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $review->update($validated);

        // Update store rating
        $this->updateStoreRating($store);

        return redirect()->route('stores.show', $store)
            ->with('success', 'Review berhasil diperbarui.');
    }

    /**
     * Mark review as helpful.
     */
    public function markHelpful(Store $store, StoreReview $review): RedirectResponse
    {
        if ($review->store_id !== $store->uuid) {
            abort(404);
        }

        // Check if user already voted
        $existingVote = \App\Models\StoreReviewHelpfulVote::where('store_review_id', $review->uuid)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingVote) {
            return redirect()->back()
                ->with('info', 'Anda sudah menandai review ini sebagai membantu.');
        }

        // Create vote
        \App\Models\StoreReviewHelpfulVote::create([
            'store_review_id' => $review->uuid,
            'user_id' => Auth::id(),
        ]);

        // Update helpful count
        $review->increment('helpful_count');

        return redirect()->back()
            ->with('success', 'Review ditandai sebagai membantu.');
    }

    /**
     * Update store rating based on approved reviews.
     */
    private function updateStoreRating(Store $store): void
    {
        $averageRating = StoreReview::where('store_id', $store->uuid)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        $totalReviews = StoreReview::where('store_id', $store->uuid)
            ->where('is_approved', true)
            ->count();

        $store->update([
            'rating' => round($averageRating),
            'total_reviews' => $totalReviews,
        ]);
    }
}

