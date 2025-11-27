<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\FactoryReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FactoryReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display factory reviews management page.
     */
    public function index(Request $request, Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $status = $request->get('status', 'all');
        $query = FactoryReview::where('factory_id', $factory->uuid)
            ->with('user');

        if ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'pending') {
            $query->where('is_approved', false);
        }

        $reviews = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total' => FactoryReview::where('factory_id', $factory->uuid)->count(),
            'approved' => FactoryReview::where('factory_id', $factory->uuid)->where('is_approved', true)->count(),
            'pending' => FactoryReview::where('factory_id', $factory->uuid)->where('is_approved', false)->count(),
            'average_rating' => FactoryReview::where('factory_id', $factory->uuid)
                ->where('is_approved', true)
                ->avg('rating') ?? 0,
        ];

        return view('factories.reviews.index', compact('factory', 'reviews', 'stats', 'status'));
    }
}

