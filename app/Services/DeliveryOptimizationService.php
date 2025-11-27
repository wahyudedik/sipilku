<?php

namespace App\Services;

use App\Helpers\GeolocationHelper;
use App\Models\Factory;
use App\Models\FactoryRequest;
use App\Models\ProjectLocation;
use Illuminate\Support\Collection;

class DeliveryOptimizationService
{
    /**
     * Get delivery optimization suggestions for multiple factory requests.
     */
    public function getOptimizationSuggestions(Collection $factoryRequests, ?ProjectLocation $projectLocation = null): array
    {
        if ($factoryRequests->isEmpty() || !$projectLocation || !$projectLocation->hasCoordinates()) {
            return [];
        }

        $suggestions = [];

        // Group by factory type
        $groupedByType = $factoryRequests->groupBy(function($request) {
            return $request->factory->factoryType->slug ?? 'other';
        });

        // Suggestion 1: Combine deliveries from same factory type
        foreach ($groupedByType as $typeSlug => $requests) {
            if ($requests->count() > 1) {
                $factories = $requests->pluck('factory')->unique('uuid');
                if ($factories->count() > 1) {
                    $suggestions[] = [
                        'type' => 'combine_delivery',
                        'priority' => 'high',
                        'title' => 'Kombinasi Pengiriman',
                        'description' => "Pertimbangkan untuk menggabungkan pengiriman dari {$factories->count()} pabrik {$typeSlug} untuk mengurangi biaya delivery.",
                        'estimated_savings' => $this->calculateCombinedDeliverySavings($requests, $projectLocation),
                    ];
                }
            }
        }

        // Suggestion 2: Nearest factory recommendation
        $nearestFactory = $this->findNearestFactory($factoryRequests, $projectLocation);
        if ($nearestFactory) {
            $suggestions[] = [
                'type' => 'nearest_factory',
                'priority' => 'medium',
                'title' => 'Pabrik Terdekat',
                'description' => "Pabrik {$nearestFactory['factory']->name} adalah yang terdekat ({$nearestFactory['distance']} km) dan dapat mengurangi biaya delivery.",
                'estimated_savings' => $nearestFactory['savings'] ?? null,
            ];
        }

        // Suggestion 3: Bulk order discount
        $totalQuantity = $factoryRequests->sum(function($request) {
            return collect($request->items)->sum('quantity');
        });
        if ($totalQuantity > 100) {
            $suggestions[] = [
                'type' => 'bulk_discount',
                'priority' => 'medium',
                'title' => 'Diskon Pesanan Besar',
                'description' => "Dengan total quantity {$totalQuantity} unit, pertimbangkan untuk meminta diskon dari pabrik untuk pesanan besar.",
            ];
        }

        // Suggestion 4: Delivery route optimization
        if ($factoryRequests->count() > 2) {
            $suggestions[] = [
                'type' => 'route_optimization',
                'priority' => 'low',
                'title' => 'Optimasi Rute Pengiriman',
                'description' => 'Pertimbangkan untuk mengatur jadwal pengiriman secara berurutan untuk mengoptimalkan rute dan mengurangi biaya.',
            ];
        }

        return $suggestions;
    }

    /**
     * Calculate potential savings from combining deliveries.
     */
    private function calculateCombinedDeliverySavings(Collection $requests, ProjectLocation $projectLocation): ?float
    {
        $totalDeliveryCost = $requests->sum(function($request) {
            return $request->delivery_cost ?? 0;
        });

        // Estimate combined delivery cost (assuming 30% reduction)
        $combinedCost = $totalDeliveryCost * 0.7;

        return round($totalDeliveryCost - $combinedCost, 2);
    }

    /**
     * Find nearest factory from requests.
     */
    private function findNearestFactory(Collection $factoryRequests, ProjectLocation $projectLocation): ?array
    {
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($factoryRequests as $request) {
            $factory = $request->factory;
            $nearestLocation = $factory->locations
                ->where('is_active', true)
                ->filter(function($location) {
                    return $location->hasCoordinates();
                })
                ->map(function($location) use ($projectLocation) {
                    $distance = GeolocationHelper::calculateDistance(
                        $projectLocation->latitude,
                        $projectLocation->longitude,
                        $location->latitude,
                        $location->longitude
                    );
                    return [
                        'location' => $location,
                        'distance' => $distance,
                    ];
                })
                ->sortBy('distance')
                ->first();

            if ($nearestLocation && $nearestLocation['distance'] < $minDistance) {
                $minDistance = $nearestLocation['distance'];
                $nearest = [
                    'factory' => $factory,
                    'distance' => $nearestLocation['distance'],
                    'request' => $request,
                ];
            }
        }

        if ($nearest) {
            // Calculate potential savings if using nearest factory
            $otherRequests = $factoryRequests->where('factory_id', '!=', $nearest['factory']->uuid);
            $otherDeliveryCost = $otherRequests->sum(function($request) {
                return $request->delivery_cost ?? 0;
            });
            $nearestDeliveryCost = $nearest['request']->delivery_cost ?? 0;
            
            if ($otherDeliveryCost > $nearestDeliveryCost) {
                $nearest['savings'] = round($otherDeliveryCost - $nearestDeliveryCost, 2);
            }
        }

        return $nearest;
    }

    /**
     * Get optimal delivery schedule.
     */
    public function getOptimalSchedule(Collection $factoryRequests, ProjectLocation $projectLocation): array
    {
        if ($factoryRequests->isEmpty() || !$projectLocation->hasCoordinates()) {
            return [];
        }

        // Calculate distances and sort by distance
        $scheduled = $factoryRequests->map(function($request) use ($projectLocation) {
            $factory = $request->factory;
            $nearestLocation = $factory->locations
                ->where('is_active', true)
                ->filter(function($location) {
                    return $location->hasCoordinates();
                })
                ->map(function($location) use ($projectLocation) {
                    $distance = GeolocationHelper::calculateDistance(
                        $projectLocation->latitude,
                        $projectLocation->longitude,
                        $location->latitude,
                        $location->longitude
                    );
                    return [
                        'location' => $location,
                        'distance' => $distance,
                    ];
                })
                ->sortBy('distance')
                ->first();

            return [
                'request' => $request,
                'factory' => $factory,
                'distance' => $nearestLocation ? $nearestLocation['distance'] : null,
            ];
        })
        ->filter(function($item) {
            return $item['distance'] !== null;
        })
        ->sortBy('distance')
        ->values();

        return $scheduled->toArray();
    }
}

