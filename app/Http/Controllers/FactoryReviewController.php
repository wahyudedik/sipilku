<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use App\Models\FactoryReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FactoryReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Display reviews for a factory.
     */
    public function index(Factory $factory): View
    {
        $reviews = FactoryReview::where('factory_id', $factory->uuid)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(10);

        // Calculate average ratings
        $approvedReviews = FactoryReview::where('factory_id', $factory->uuid)
            ->where('is_approved', true)
            ->get();

        $averageRating = $approvedReviews->avg('rating') ?? 0;

        // Calculate category averages
        $categoryRatings = [
            'product_quality' => $this->calculateCategoryAverage($approvedReviews, 'product_quality'),
            'delivery_quality' => $this->calculateCategoryAverage($approvedReviews, 'delivery_quality'),
            'service_quality' => $this->calculateCategoryAverage($approvedReviews, 'service_quality'),
            'price' => $this->calculateCategoryAverage($approvedReviews, 'price'),
        ];

        // Rating breakdown
        $ratingBreakdown = [
            5 => $approvedReviews->where('rating', 5)->count(),
            4 => $approvedReviews->where('rating', 4)->count(),
            3 => $approvedReviews->where('rating', 3)->count(),
            2 => $approvedReviews->where('rating', 2)->count(),
            1 => $approvedReviews->where('rating', 1)->count(),
        ];

        return view('factory-reviews.index', compact(
            'factory',
            'reviews',
            'averageRating',
            'categoryRatings',
            'ratingBreakdown'
        ));
    }

    /**
     * Show the form for creating a new review.
     */
    public function create(Factory $factory): View
    {
        // Check if user already reviewed this factory
        $existingReview = FactoryReview::where('factory_id', $factory->uuid)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()->route('factory-reviews.edit', [$factory, $existingReview])
                ->with('info', 'Anda sudah memberikan review untuk pabrik ini. Anda dapat mengedit review Anda.');
        }

        return view('factory-reviews.create', compact('factory'));
    }

    /**
     * Store a newly created review.
     */
    public function store(Request $request, Factory $factory): RedirectResponse
    {
        // Check if user already reviewed this factory
        $existingReview = FactoryReview::where('factory_id', $factory->uuid)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'Anda sudah memberikan review untuk pabrik ini.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'ratings_breakdown' => ['nullable', 'array'],
            'ratings_breakdown.product_quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.delivery_quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.service_quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.price' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $review = FactoryReview::create([
            'factory_id' => $factory->uuid,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'ratings_breakdown' => $validated['ratings_breakdown'] ?? null,
            'is_verified_purchase' => false, // Can be set based on order history
            'is_approved' => true, // Auto-approve, can be changed to require moderation
        ]);

        // Update factory rating
        $this->updateFactoryRating($factory);

        return redirect()->route('factories.show', $factory)
            ->with('success', 'Review berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Factory $factory, FactoryReview $review): View
    {
        if ($review->user_id !== Auth::id() || $review->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        return view('factory-reviews.edit', compact('factory', 'review'));
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Factory $factory, FactoryReview $review): RedirectResponse
    {
        if ($review->user_id !== Auth::id() || $review->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'ratings_breakdown' => ['nullable', 'array'],
            'ratings_breakdown.product_quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.delivery_quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.service_quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ratings_breakdown.price' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $review->update($validated);

        // Update factory rating
        $this->updateFactoryRating($factory);

        return redirect()->route('factories.show', $factory)
            ->with('success', 'Review berhasil diperbarui.');
    }

    /**
     * Mark review as helpful.
     */
    public function markHelpful(Factory $factory, FactoryReview $review): RedirectResponse
    {
        if ($review->factory_id !== $factory->uuid) {
            abort(404);
        }

        // Check if user already voted
        $existingVote = \App\Models\FactoryReviewHelpfulVote::where('factory_review_id', $review->uuid)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingVote) {
            return redirect()->back()
                ->with('info', 'Anda sudah menandai review ini sebagai membantu.');
        }

        // Create vote
        \App\Models\FactoryReviewHelpfulVote::create([
            'factory_review_id' => $review->uuid,
            'user_id' => Auth::id(),
        ]);

        // Update helpful count
        $review->increment('helpful_count');

        return redirect()->back()
            ->with('success', 'Review ditandai sebagai membantu.');
    }

    /**
     * Calculate average rating for a specific category.
     */
    private function calculateCategoryAverage($reviews, string $category): float
    {
        $ratings = $reviews->map(function($review) use ($category) {
            return $review->ratings_breakdown[$category] ?? null;
        })->filter()->values();

        if ($ratings->isEmpty()) {
            return 0;
        }

        return round($ratings->avg(), 2);
    }

    /**
     * Update factory rating based on approved reviews.
     */
    private function updateFactoryRating(Factory $factory): void
    {
        $averageRating = FactoryReview::where('factory_id', $factory->uuid)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        $totalReviews = FactoryReview::where('factory_id', $factory->uuid)
            ->where('is_approved', true)
            ->count();

        $factory->update([
            'rating' => round($averageRating),
            'total_reviews' => $totalReviews,
        ]);
    }
}

