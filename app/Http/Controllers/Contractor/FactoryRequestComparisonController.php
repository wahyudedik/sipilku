<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\FactoryRequest;
use App\Services\FactoryComparisonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FactoryRequestComparisonController extends Controller
{
    protected FactoryComparisonService $comparisonService;

    public function __construct(FactoryComparisonService $comparisonService)
    {
        $this->middleware(['auth', 'verified']);
        $this->comparisonService = $comparisonService;
    }

    /**
     * Compare factory quotes from multiple factories.
     */
    public function compare(Request $request, ?string $requestGroupId = null): View
    {
        // Get request group ID from query or parameter
        $groupId = $requestGroupId ?? $request->get('request_group_id');

        if (!$groupId) {
            abort(404, 'Request group ID is required.');
        }

        // Get all factory requests in this group
        $factoryRequests = FactoryRequest::where('request_group_id', $groupId)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['quoted', 'accepted'])
            ->with([
                'factory',
                'factory.factoryType',
                'factory.locations',
                'projectLocation'
            ])
            ->get();

        if ($factoryRequests->isEmpty()) {
            abort(404, 'No quoted requests found for comparison.');
        }

        // Get project location for distance calculation
        $projectLocation = $factoryRequests->first()->projectLocation;
        $latitude = $projectLocation && $projectLocation->hasCoordinates() ? $projectLocation->latitude : null;
        $longitude = $projectLocation && $projectLocation->hasCoordinates() ? $projectLocation->longitude : null;

        // Prepare comparison data
        $comparisons = $factoryRequests->map(function($factoryRequest) use ($latitude, $longitude) {
            $factory = $factoryRequest->factory;
            
            // Calculate distance if coordinates available
            $distance = null;
            if ($latitude && $longitude) {
                $nearestLocation = $factory->locations
                    ->where('is_active', true)
                    ->filter(function($location) {
                        return $location->hasCoordinates();
                    })
                    ->map(function($location) use ($latitude, $longitude) {
                        $dist = \App\Helpers\GeolocationHelper::calculateDistance(
                            $latitude,
                            $longitude,
                            $location->latitude,
                            $location->longitude
                        );
                        return [
                            'location' => $location,
                            'distance' => $dist,
                        ];
                    })
                    ->sortBy('distance')
                    ->first();

                $distance = $nearestLocation ? $nearestLocation['distance'] : null;
            }

            // Calculate quality score
            $qualityScore = $this->calculateQualityScore($factory);

            // Get cost breakdown
            $costBreakdown = $factoryRequest->cost_breakdown;

            return [
                'factory_request' => $factoryRequest,
                'factory' => $factory,
                'distance' => $distance,
                'quality_score' => $qualityScore,
                'rating' => $factory->rating ?? 0,
                'total_reviews' => $factory->total_reviews ?? 0,
                'certification_count' => is_array($factory->certifications) ? count($factory->certifications) : 0,
                'cost_breakdown' => $costBreakdown,
                'total_cost' => $factoryRequest->total_cost ?? 0,
            ];
        })
        ->sortBy('total_cost')
        ->values();

        return view('contractor.factory-requests.compare', compact('comparisons', 'requestGroupId', 'projectLocation'));
    }

    /**
     * Calculate quality score for a factory.
     */
    private function calculateQualityScore($factory): float
    {
        $score = 0;

        // Rating component (0-50 points)
        $rating = $factory->rating ?? 0;
        $score += ($rating / 5) * 50;

        // Certifications component (0-30 points)
        $certifications = $factory->certifications ?? [];
        if (is_array($certifications) && count($certifications) > 0) {
            $score += min(30, count($certifications) * 10);
        }

        // Reviews component (0-20 points)
        $reviewsCount = $factory->total_reviews ?? 0;
        if ($reviewsCount > 0) {
            $score += min(20, ($reviewsCount / 50) * 20);
        }

        return round($score, 2);
    }
}

