<?php

namespace App\Services;

use App\Models\Factory;
use App\Models\FactoryView;
use App\Models\FactoryProduct;
use App\Models\FactoryReview;
use App\Models\FactoryRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FactoryAnalyticsService
{
    protected $cacheTtl = 60; // minutes

    /**
     * Get overview metrics for factory dashboard
     */
    public function getOverviewMetrics($factoryId, $startDate, $endDate)
    {
        $cacheKey = "factory.{$factoryId}.overview." . md5($startDate . $endDate);

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtl), function () use ($factoryId, $startDate, $endDate) {
            $totalViews = FactoryView::where('factory_id', $factoryId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $uniqueVisitors = FactoryView::where('factory_id', $factoryId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->distinct('user_id')
                ->count('user_id');

            $totalProducts = FactoryProduct::where('factory_id', $factoryId)->count();

            $totalOrders = FactoryRequest::where('factory_id', $factoryId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $completedOrders = FactoryRequest::where('factory_id', $factoryId)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $totalRevenue = FactoryRequest::where('factory_id', $factoryId)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_price');

            $avgRating = FactoryReview::where('factory_id', $factoryId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('rating');

            $totalReviews = FactoryReview::where('factory_id', $factoryId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            return [
                'total_views' => $totalViews,
                'unique_visitors' => $uniqueVisitors,
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'total_revenue' => $totalRevenue,
                'average_rating' => round($avgRating ?? 0, 2),
                'total_reviews' => $totalReviews,
                'conversion_rate' => $totalViews > 0 ? round(($totalOrders / $totalViews) * 100, 2) : 0,
            ];
        });
    }

    /**
     * Get view statistics with trends
     */
    public function getViewStatistics($factoryId, $startDate, $endDate)
    {
        $views = FactoryView::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_views'),
                DB::raw('COUNT(DISTINCT user_id) as unique_visitors')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $topReferrers = FactoryView::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('referrer', DB::raw('COUNT(*) as count'))
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $peakHours = FactoryView::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return [
            'daily_views' => $views,
            'top_referrers' => $topReferrers,
            'peak_hours' => $peakHours,
        ];
    }

    /**
     * Get detailed view statistics
     */
    public function getDetailedViewStatistics($factoryId, $startDate, $endDate)
    {
        $cacheKey = "factory.{$factoryId}.detailed_views." . md5($startDate . $endDate);

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtl), function () use ($factoryId, $startDate, $endDate) {
            return [
                'overview' => $this->getViewStatistics($factoryId, $startDate, $endDate),
                'hourly_distribution' => $this->getHourlyDistribution($factoryId, $startDate, $endDate),
            ];
        });
    }

    /**
     * Get product popularity analytics
     */
    public function getProductPopularity($factoryId, $startDate, $endDate)
    {
        $cacheKey = "factory.{$factoryId}.product_popularity." . md5($startDate . $endDate);

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtl), function () use ($factoryId, $startDate, $endDate) {
            return FactoryProduct::where('factory_id', $factoryId)
                ->with(['factoryRequests' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->get()
                ->map(function ($product) use ($startDate, $endDate) {
                    $requests = $product->factoryRequests()
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->get();

                    return [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'total_requests' => $requests->count(),
                        'total_quantity_sold' => $requests->sum('quantity'),
                        'total_revenue' => $requests->where('status', 'completed')->sum('total_price'),
                        'average_price' => $product->base_price,
                        'stock_status' => $product->stock_status,
                    ];
                })
                ->sortByDesc('total_requests')
                ->take(20)
                ->values();
        });
    }

    /**
     * Get detailed product popularity analytics
     */
    public function getDetailedProductPopularity($factoryId, $startDate, $endDate)
    {
        return [
            'top_products' => $this->getProductPopularity($factoryId, $startDate, $endDate),
            'product_trends' => $this->getProductTrends($factoryId, $startDate, $endDate),
        ];
    }

    /**
     * Get sales data and reports
     */
    public function getSalesData($factoryId, $startDate, $endDate)
    {
        $sales = FactoryRequest::where('factory_id', $factoryId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_price) as total_revenue'),
                DB::raw('AVG(total_price) as average_order_value')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'daily_sales' => $sales,
            'total_revenue' => $sales->sum('total_revenue'),
            'total_orders' => $sales->sum('total_orders'),
            'average_order_value' => $sales->avg('average_order_value'),
        ];
    }

    /**
     * Get detailed sales reports
     */
    public function getDetailedSalesReports($factoryId, $startDate, $endDate, $groupBy = 'day')
    {
        $dateFormat = match ($groupBy) {
            'week' => 'YEARWEEK(created_at)',
            'month' => 'DATE_FORMAT(created_at, "%Y-%m")',
            default => 'DATE(created_at)',
        };

        $sales = FactoryRequest::where('factory_id', $factoryId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("$dateFormat as period"),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_price) as total_revenue'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('AVG(total_price) as average_order_value')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        return [
            'sales_data' => $sales,
            'summary' => [
                'total_revenue' => $sales->sum('total_revenue'),
                'total_orders' => $sales->sum('total_orders'),
                'total_quantity' => $sales->sum('total_quantity'),
                'average_order_value' => $sales->avg('average_order_value'),
            ],
            'top_customers' => $this->getTopCustomers($factoryId, $startDate, $endDate),
        ];
    }

    /**
     * Get factory performance metrics
     */
    public function getPerformanceMetrics($factoryId, $startDate, $endDate)
    {
        $totalRequests = FactoryRequest::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedRequests = FactoryRequest::where('factory_id', $factoryId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $cancelledRequests = FactoryRequest::where('factory_id', $factoryId)
            ->where('status', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'fulfillment_rate' => $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100, 2) : 0,
            'cancellation_rate' => $totalRequests > 0 ? round(($cancelledRequests / $totalRequests) * 100, 2) : 0,
            'customer_satisfaction' => $this->getCustomerSatisfactionScore($factoryId, $startDate, $endDate),
            'repeat_customer_rate' => $this->getRepeatCustomerRate($factoryId, $startDate, $endDate),
        ];
    }

    /**
     * Get detailed performance metrics
     */
    public function getDetailedPerformanceMetrics($factoryId, $startDate, $endDate)
    {
        return [
            'overview' => $this->getPerformanceMetrics($factoryId, $startDate, $endDate),
            'order_status_breakdown' => $this->getOrderStatusBreakdown($factoryId, $startDate, $endDate),
        ];
    }

    /**
     * Get factory comparison reports by type
     */
    public function getFactoryComparisonReports($factoryTypeId, $startDate, $endDate)
    {
        $cacheKey = "factory_comparison.{$factoryTypeId}." . md5($startDate . $endDate);

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtl), function () use ($factoryTypeId, $startDate, $endDate) {
            $query = Factory::with('factoryType');

            if ($factoryTypeId) {
                $query->where('factory_type_id', $factoryTypeId);
            }

            return $query->get()->map(function ($factory) use ($startDate, $endDate) {
                $totalViews = FactoryView::where('factory_id', $factory->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $totalOrders = FactoryRequest::where('factory_id', $factory->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $totalRevenue = FactoryRequest::where('factory_id', $factory->id)
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_price');

                $avgRating = FactoryReview::where('factory_id', $factory->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->avg('rating');

                return [
                    'factory_id' => $factory->id,
                    'factory_name' => $factory->name,
                    'factory_type' => $factory->factoryType->name ?? 'N/A',
                    'total_views' => $totalViews,
                    'total_orders' => $totalOrders,
                    'total_revenue' => $totalRevenue,
                    'average_rating' => round($avgRating ?? 0, 2),
                    'conversion_rate' => $totalViews > 0 ? round(($totalOrders / $totalViews) * 100, 2) : 0,
                ];
            })->sortByDesc('total_revenue');
        });
    }

    /**
     * Get quality trends and review analytics
     */
    public function getQualityTrends($factoryId, $startDate, $endDate)
    {
        $reviews = FactoryReview::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(rating) as average_rating'),
                DB::raw('AVG(quality_rating) as average_quality'),
                DB::raw('AVG(delivery_rating) as average_delivery'),
                DB::raw('AVG(service_rating) as average_service'),
                DB::raw('COUNT(*) as review_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $ratingDistribution = FactoryReview::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        return [
            'daily_trends' => $reviews,
            'rating_distribution' => $ratingDistribution,
            'average_scores' => [
                'overall' => $reviews->avg('average_rating'),
                'quality' => $reviews->avg('average_quality'),
                'delivery' => $reviews->avg('average_delivery'),
                'service' => $reviews->avg('average_service'),
            ],
        ];
    }

    /**
     * Get detailed quality trends
     */
    public function getDetailedQualityTrends($factoryId, $startDate, $endDate)
    {
        return [
            'trends' => $this->getQualityTrends($factoryId, $startDate, $endDate),
            'sentiment_analysis' => $this->getReviewSentimentAnalysis($factoryId, $startDate, $endDate),
        ];
    }

    /**
     * Get location-specific analytics
     */
    public function getLocationAnalytics($factoryId, $startDate, $endDate)
    {
        $factory = Factory::with('factoryLocations')->findOrFail($factoryId);

        return $factory->factoryLocations->map(function ($location) use ($startDate, $endDate, $factoryId) {
            $nearbyRequests = FactoryRequest::where('factory_id', $factoryId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->filter(function ($request) use ($location) {
                    if (!$request->delivery_latitude || !$request->delivery_longitude) {
                        return false;
                    }
                    $distance = $this->calculateDistance(
                        $location->latitude,
                        $location->longitude,
                        $request->delivery_latitude,
                        $request->delivery_longitude
                    );
                    return $distance <= 50; // Within 50km radius
                });

            return [
                'location_id' => $location->id,
                'location_name' => $location->address,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'total_orders' => $nearbyRequests->count(),
                'total_revenue' => $nearbyRequests->where('status', 'completed')->sum('total_price'),
            ];
        });
    }

    /**
     * Get detailed location analytics
     */
    public function getDetailedLocationAnalytics($factoryId, $startDate, $endDate)
    {
        return [
            'location_data' => $this->getLocationAnalytics($factoryId, $startDate, $endDate),
            'regional_performance' => $this->getRegionalPerformance($factoryId, $startDate, $endDate),
        ];
    }

    /**
     * Helper methods
     */

    private function getHourlyDistribution($factoryId, $startDate, $endDate)
    {
        return FactoryView::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();
    }

    private function getProductTrends($factoryId, $startDate, $endDate)
    {
        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $previousStart = Carbon::parse($startDate)->subDays($days);

        return FactoryProduct::where('factory_id', $factoryId)
            ->withCount([
                'factoryRequests as current_period_requests' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
                'factoryRequests as previous_period_requests' => function ($query) use ($previousStart, $startDate) {
                    $query->whereBetween('created_at', [$previousStart, $startDate]);
                }
            ])
            ->get()
            ->map(function ($product) {
                $growth = 0;
                if ($product->previous_period_requests > 0) {
                    $growth = (($product->current_period_requests - $product->previous_period_requests) / $product->previous_period_requests) * 100;
                }
                return [
                    'product_name' => $product->name,
                    'current_requests' => $product->current_period_requests,
                    'previous_requests' => $product->previous_period_requests,
                    'growth_percentage' => round($growth, 2),
                ];
            });
    }

    private function getTopCustomers($factoryId, $startDate, $endDate)
    {
        return FactoryRequest::where('factory_id', $factoryId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->join('users', 'factory_requests.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(factory_requests.id) as total_orders'),
                DB::raw('SUM(factory_requests.total_price) as total_spent')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
    }

    private function getCustomerSatisfactionScore($factoryId, $startDate, $endDate)
    {
        $avgRating = FactoryReview::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('rating');

        return round(($avgRating ?? 0) * 20, 2); // Convert 5-star to 100-point scale
    }

    private function getRepeatCustomerRate($factoryId, $startDate, $endDate)
    {
        $totalCustomers = FactoryRequest::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');

        $repeatCustomers = FactoryRequest::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('user_id', DB::raw('COUNT(*) as order_count'))
            ->groupBy('user_id')
            ->having('order_count', '>', 1)
            ->count();

        return $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 2) : 0;
    }

    private function getOrderStatusBreakdown($factoryId, $startDate, $endDate)
    {
        return FactoryRequest::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
    }

    private function getReviewSentimentAnalysis($factoryId, $startDate, $endDate)
    {
        $reviews = FactoryReview::where('factory_id', $factoryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'positive' => $reviews->where('rating', '>=', 4)->count(),
            'neutral' => $reviews->where('rating', '=', 3)->count(),
            'negative' => $reviews->where('rating', '<=', 2)->count(),
            'total' => $reviews->count(),
        ];
    }

    private function getRegionalPerformance($factoryId, $startDate, $endDate)
    {
        return FactoryRequest::where('factory_id', $factoryId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'delivery_city',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->whereNotNull('delivery_city')
            ->groupBy('delivery_city')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    /**
     * Export report in various formats
     */
    public function exportReport($factoryId, $reportType, $format, $startDate, $endDate)
    {
        $data = match ($reportType) {
            'overview' => $this->getOverviewMetrics($factoryId, $startDate, $endDate),
            'sales' => $this->getDetailedSalesReports($factoryId, $startDate, $endDate, 'day'),
            'products' => $this->getDetailedProductPopularity($factoryId, $startDate, $endDate),
            'performance' => $this->getDetailedPerformanceMetrics($factoryId, $startDate, $endDate),
            'quality' => $this->getDetailedQualityTrends($factoryId, $startDate, $endDate),
            'location' => $this->getDetailedLocationAnalytics($factoryId, $startDate, $endDate),
            default => $this->getOverviewMetrics($factoryId, $startDate, $endDate),
        };

        return response()->json([
            'message' => 'Export functionality - requires export libraries (Laravel Excel, DomPDF)',
            'data' => $data,
            'format' => $format
        ]);
    }

    // Legacy methods for backward compatibility
    public function getDashboardSummary($factoryUuid, $rangeDays = 30)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($rangeDays);
        return $this->getOverviewMetrics($factoryUuid, $startDate, $endDate);
    }

    public function getProductPopularityByFactoryType($factoryTypeId, $rangeDays = 30, $limit = 10)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($rangeDays);
        return $this->getFactoryComparisonReports($factoryTypeId, $startDate, $endDate)->take($limit);
    }

    public function getSalesReport($factoryUuid, $from, $to)
    {
        $startDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);
        return $this->getDetailedSalesReports($factoryUuid, $startDate, $endDate, 'day');
    }

    public function getFactoryComparisonByType($factoryTypeId, $options = [])
    {
        $startDate = isset($options['from']) ? Carbon::parse($options['from']) : Carbon::now()->subDays(30);
        $endDate = isset($options['to']) ? Carbon::parse($options['to']) : Carbon::now();
        return $this->getFactoryComparisonReports($factoryTypeId, $startDate, $endDate);
    }

    public function getQualityAndReviewTrends($factoryUuid, $days = 30)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($days);
        return $this->getQualityTrends($factoryUuid, $startDate, $endDate);
    }
}
