<?php

namespace App\Http\Controllers\Contractor;

use App\Helpers\GeolocationHelper;
use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\FactoryRequest;
use App\Models\FactoryType;
use App\Models\MaterialRequest;
use App\Models\Order;
use App\Models\ProjectLocation;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Store;
use App\Services\FactoryRecommendationService;
use App\Services\StoreRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display contractor dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $factoryTypeFilter = $request->get('factory_type');

        // Active service orders
        $activeServiceOrders = Order::where('user_id', $user->id)
            ->where('type', 'service')
            ->whereIn('status', ['pending', 'processing'])
            ->with(['orderable', 'orderable.category', 'quoteRequest'])
            ->latest()
            ->limit(10)
            ->get();

        // Material requests & quotes (from stores)
        $materialRequests = MaterialRequest::where('user_id', $user->id)
            ->with(['store', 'store.locations', 'projectLocation'])
            ->latest()
            ->limit(10)
            ->get();

        // Factory product requests & quotes (all factory types)
        $factoryRequestsQuery = FactoryRequest::where('user_id', $user->id)
            ->with(['factory', 'factory.factoryType', 'factory.locations', 'projectLocation']);

        if ($factoryTypeFilter) {
            $factoryRequestsQuery->whereHas('factory', function($q) use ($factoryTypeFilter) {
                $q->where('factory_type_id', $factoryTypeFilter);
            });
        }

        $factoryRequests = $factoryRequestsQuery->latest()->limit(10)->get();

        // Project locations
        $projectLocations = ProjectLocation::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        // Recommended stores nearby (based on active project locations)
        $recommendedStores = $this->getRecommendedStores($user, $projectLocations);

        // Recommended factories nearby (all types - based on active projects)
        $recommendedFactories = $this->getRecommendedFactories($user, $projectLocations, $factoryTypeFilter);

        // Service earnings
        $serviceEarnings = $this->calculateServiceEarnings($user);

        // Store integration statistics
        $storeStats = $this->getStoreStatistics($user);

        // Factory integration statistics (all factory types)
        $factoryStats = $this->getFactoryStatistics($user, $factoryTypeFilter);

        // Factory types for filter
        $factoryTypes = FactoryType::where('is_active', true)->get();

        return view('contractor.dashboard', compact(
            'activeServiceOrders',
            'materialRequests',
            'factoryRequests',
            'projectLocations',
            'recommendedStores',
            'recommendedFactories',
            'serviceEarnings',
            'storeStats',
            'factoryStats',
            'factoryTypes',
            'factoryTypeFilter'
        ));
    }

    /**
     * Display material procurement page (from stores)
     */
    public function materialProcurement(Request $request): View
    {
        $user = Auth::user();
        $projectLocationId = $request->get('project_location');

        $projectLocations = ProjectLocation::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        $stores = Store::where('is_active', true)
            ->where('is_verified', true)
            ->where('status', 'approved')
            ->with(['locations', 'products', 'reviews'])
            ->get();

        // If project location selected, filter stores by distance
        $selectedProjectLocation = null;
        if ($projectLocationId) {
            $selectedProjectLocation = ProjectLocation::where('user_id', $user->id)
                ->where('uuid', $projectLocationId)
                ->first();

            if ($selectedProjectLocation && $selectedProjectLocation->hasCoordinates()) {
                $stores = $stores->map(function($store) use ($selectedProjectLocation) {
                    $nearestLocation = $store->locations
                        ->where('is_active', true)
                        ->filter(function($location) {
                            return $location->hasCoordinates();
                        })
                        ->map(function($location) use ($selectedProjectLocation) {
                            $distance = GeolocationHelper::calculateDistance(
                                $selectedProjectLocation->latitude,
                                $selectedProjectLocation->longitude,
                                $location->latitude,
                                $location->longitude
                            );
                            return [
                                'location' => $location,
                                'distance' => $distance
                            ];
                        })
                        ->sortBy('distance')
                        ->first();

                    $store->distance = $nearestLocation ? $nearestLocation['distance'] : null;
                    $store->nearest_location = $nearestLocation ? $nearestLocation['location'] : null;
                    return $store;
                })->sortBy('distance');
            }
        }

        return view('contractor.material-procurement', compact(
            'stores',
            'projectLocations',
            'selectedProjectLocation'
        ));
    }

    /**
     * Display factory procurement page (beton, bata, genting, baja, dll)
     */
    public function factoryProcurement(Request $request): View
    {
        $user = Auth::user();
        $factoryTypeId = $request->get('factory_type');
        $projectLocationId = $request->get('project_location');

        $factoryTypes = FactoryType::where('is_active', true)->get();
        $projectLocations = ProjectLocation::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        $factoriesQuery = Factory::where('is_active', true)
            ->where('is_verified', true)
            ->where('status', 'approved')
            ->with(['factoryType', 'locations', 'products', 'reviews']);

        if ($factoryTypeId) {
            $factoriesQuery->where('factory_type_id', $factoryTypeId);
        }

        $factories = $factoriesQuery->get();

        // If project location selected, calculate distance and delivery cost
        $selectedProjectLocation = null;
        if ($projectLocationId) {
            $selectedProjectLocation = ProjectLocation::where('user_id', $user->id)
                ->where('uuid', $projectLocationId)
                ->first();

            if ($selectedProjectLocation && $selectedProjectLocation->hasCoordinates()) {
                $factories = $factories->map(function($factory) use ($selectedProjectLocation) {
                    $nearestLocation = $factory->locations
                        ->where('is_active', true)
                        ->filter(function($location) {
                            return $location->hasCoordinates();
                        })
                        ->map(function($location) use ($selectedProjectLocation, $factory) {
                            $distance = GeolocationHelper::calculateDistance(
                                $selectedProjectLocation->latitude,
                                $selectedProjectLocation->longitude,
                                $location->latitude,
                                $location->longitude
                            );
                            $deliveryCost = GeolocationHelper::calculateDeliveryCost(
                                $distance,
                                $factory->delivery_price_per_km
                            );
                            return [
                                'location' => $location,
                                'distance' => $distance,
                                'delivery_cost' => $deliveryCost
                            ];
                        })
                        ->sortBy('distance')
                        ->first();

                    $factory->distance = $nearestLocation ? $nearestLocation['distance'] : null;
                    $factory->delivery_cost = $nearestLocation ? $nearestLocation['delivery_cost'] : null;
                    $factory->nearest_location = $nearestLocation ? $nearestLocation['location'] : null;
                    return $factory;
                })->sortBy('distance');
            }
        }

        return view('contractor.factory-procurement', compact(
            'factories',
            'factoryTypes',
            'projectLocations',
            'selectedProjectLocation',
            'factoryTypeId'
        ));
    }

    /**
     * Display factory product cost calculator
     */
    public function factoryCostCalculator(Request $request): View
    {
        $factoryTypes = FactoryType::where('is_active', true)->get();
        $projectLocations = ProjectLocation::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        return view('contractor.factory-cost-calculator', compact(
            'factoryTypes',
            'projectLocations'
        ));
    }

    /**
     * Get recommended stores nearby based on project locations
     */
    private function getRecommendedStores($user, $projectLocations)
    {
        if ($projectLocations->isEmpty()) {
            return collect();
        }

        $service = new StoreRecommendationService();
        $recommended = collect();

        foreach ($projectLocations as $projectLocation) {
            if (!$projectLocation->hasCoordinates()) {
                continue;
            }

            $recommendations = $service->getRecommendations(
                $projectLocation->latitude,
                $projectLocation->longitude,
                5, // Limit per project location
                50  // Max 50km
            );

            foreach ($recommendations as $recommendation) {
                $recommended->push([
                    'store' => $recommendation['store'],
                    'project_location' => $projectLocation,
                    'distance' => $recommendation['distance'],
                    'store_location' => $recommendation['nearest_location'],
                    'recommendation_score' => $recommendation['recommendation_score'],
                ]);
            }
        }

        return $recommended->unique('store.uuid')
            ->sortByDesc('recommendation_score')
            ->take(10);
    }

    /**
     * Get recommended factories nearby based on project locations
     */
    private function getRecommendedFactories($user, $projectLocations, $factoryTypeFilter = null)
    {
        if ($projectLocations->isEmpty()) {
            return collect();
        }

        $service = new FactoryRecommendationService();
        $recommended = collect();

        foreach ($projectLocations as $projectLocation) {
            if (!$projectLocation->hasCoordinates()) {
                continue;
            }

            $recommendations = $service->getRecommendations(
                $projectLocation->latitude,
                $projectLocation->longitude,
                5, // Limit per project location
                100, // Max 100km for factories
                $factoryTypeFilter
            );

            foreach ($recommendations as $recommendation) {
                $recommended->push([
                    'factory' => $recommendation['factory'],
                    'project_location' => $projectLocation,
                    'distance' => $recommendation['distance'],
                    'factory_location' => $recommendation['nearest_location'],
                    'delivery_cost' => $recommendation['delivery_cost'],
                    'recommendation_score' => $recommendation['recommendation_score'],
                ]);
            }
        }

        return $recommended->unique('factory.uuid')
            ->sortByDesc('recommendation_score')
            ->take(10);
    }

    /**
     * Calculate service earnings
     */
    private function calculateServiceEarnings($user)
    {
        $totalEarnings = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('orderable_type', Service::class);
        })
        ->where('status', 'completed')
        ->sum('total');

        $monthlyEarnings = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('orderable_type', Service::class);
        })
        ->where('status', 'completed')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total');

        return [
            'total' => $totalEarnings,
            'monthly' => $monthlyEarnings,
        ];
    }

    /**
     * Get store integration statistics
     */
    private function getStoreStatistics($user)
    {
        $totalRequests = MaterialRequest::where('user_id', $user->id)->count();
        $pendingRequests = MaterialRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $quotedRequests = MaterialRequest::where('user_id', $user->id)
            ->where('status', 'quoted')
            ->count();
        $acceptedRequests = MaterialRequest::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        return [
            'total_requests' => $totalRequests,
            'pending' => $pendingRequests,
            'quoted' => $quotedRequests,
            'accepted' => $acceptedRequests,
        ];
    }

    /**
     * Get factory integration statistics
     */
    private function getFactoryStatistics($user, $factoryTypeFilter = null)
    {
        $query = FactoryRequest::where('user_id', $user->id);

        if ($factoryTypeFilter) {
            $query->where('factory_type_id', $factoryTypeFilter);
        }

        $totalRequests = (clone $query)->count();
        $pendingRequests = (clone $query)->where('status', 'pending')->count();
        $quotedRequests = (clone $query)->where('status', 'quoted')->count();
        $acceptedRequests = (clone $query)->where('status', 'accepted')->count();

        return [
            'total_requests' => $totalRequests,
            'pending' => $pendingRequests,
            'quoted' => $quotedRequests,
            'accepted' => $acceptedRequests,
        ];
    }
}
