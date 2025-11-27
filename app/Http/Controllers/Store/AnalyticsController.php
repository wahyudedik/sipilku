<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\StoreView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display store analytics dashboard.
     */
    public function index(Request $request): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        // Date range filter
        $dateRange = $request->get('range', '30'); // days
        $startDate = now()->subDays($dateRange);
        $endDate = now();

        // Store View Statistics
        $viewStats = $this->getViewStatistics($store, $startDate, $endDate);

        // Product Popularity Analytics
        $productAnalytics = $this->getProductPopularity($store, $startDate, $endDate);

        // Sales Reports
        $salesReports = $this->getSalesReports($store, $startDate, $endDate);

        // Performance Metrics
        $performanceMetrics = $this->getPerformanceMetrics($store, $startDate, $endDate);

        // Comparison Data (for comparison reports)
        $comparisonData = $this->getComparisonData($store, $startDate, $endDate);

        return view('store.analytics.index', compact(
            'store',
            'viewStats',
            'productAnalytics',
            'salesReports',
            'performanceMetrics',
            'comparisonData',
            'dateRange',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get store view statistics.
     */
    private function getViewStatistics(Store $store, $startDate, $endDate): array
    {
        $totalViews = StoreView::where('store_id', $store->uuid)
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->count();

        $uniqueViews = StoreView::where('store_id', $store->uuid)
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->distinct('ip_address')
            ->count('ip_address');

        $registeredUserViews = StoreView::where('store_id', $store->uuid)
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->whereNotNull('user_id')
            ->count();

        // Daily views for chart
        $dailyViews = StoreView::where('store_id', $store->uuid)
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Views by referrer
        $viewsByReferrer = StoreView::where('store_id', $store->uuid)
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->whereNotNull('referrer')
            ->selectRaw('referrer, COUNT(*) as views')
            ->groupBy('referrer')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        // Previous period comparison
        $previousStartDate = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $previousEndDate = $startDate;
        $previousTotalViews = StoreView::where('store_id', $store->uuid)
            ->whereBetween('viewed_at', [$previousStartDate, $previousEndDate])
            ->count();

        $viewsGrowth = $previousTotalViews > 0
            ? (($totalViews - $previousTotalViews) / $previousTotalViews) * 100
            : 0;

        return [
            'total_views' => $totalViews,
            'unique_views' => $uniqueViews,
            'registered_user_views' => $registeredUserViews,
            'daily_views' => $dailyViews,
            'views_by_referrer' => $viewsByReferrer,
            'views_growth' => round($viewsGrowth, 2),
            'previous_total_views' => $previousTotalViews,
        ];
    }

    /**
     * Get product popularity analytics.
     */
    private function getProductPopularity(Store $store, $startDate, $endDate): array
    {
        // Most viewed products (if we track product views separately)
        // For now, we'll use order data and material request items
        
        // Products in material requests
        $productRequests = MaterialRequest::where('store_id', $store->uuid)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->flatMap(function($request) {
                return $request->items ?? [];
            })
            ->groupBy('product_id')
            ->map(function($items) {
                return [
                    'count' => $items->sum('quantity'),
                    'requests' => $items->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(10);

        // Top products by sales - simplified approach
        $topProducts = StoreProduct::where('store_id', $store->uuid)
            ->orderByDesc('sold_count')
            ->limit(10)
            ->get();

        // Product performance metrics - using material requests data
        $materialRequests = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->get();

        $productPerformance = collect();
        foreach ($materialRequests as $request) {
            if (is_array($request->items)) {
                foreach ($request->items as $item) {
                    $productId = $item['product_id'] ?? null;
                    if ($productId) {
                        $product = StoreProduct::where('uuid', $productId)->first();
                        if ($product) {
                            $quantity = $item['quantity'] ?? 0;
                            $price = $item['price'] ?? $product->final_price;
                            $revenue = $quantity * $price;

                            $existing = $productPerformance->firstWhere('product.uuid', $productId);
                            if ($existing) {
                                $existing['requests_count']++;
                                $existing['total_quantity'] += $quantity;
                                $existing['total_revenue'] += $revenue;
                            } else {
                                $productPerformance->push([
                                    'product' => $product,
                                    'requests_count' => 1,
                                    'total_quantity' => $quantity,
                                    'total_revenue' => $revenue,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        $productPerformance = $productPerformance->sortByDesc('total_revenue')->take(10);

        return [
            'product_requests' => $productRequests,
            'top_products' => $topProducts,
            'product_performance' => $productPerformance,
        ];
    }

    /**
     * Get sales reports.
     */
    private function getSalesReports(Store $store, $startDate, $endDate): array
    {
        $materialRequests = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->get();

        $totalSales = $materialRequests->sum('quoted_price');
        $totalOrders = $materialRequests->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Daily sales
        $dailySales = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->selectRaw('DATE(accepted_at) as date, SUM(quoted_price) as sales, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sales by product category
        $salesByCategory = DB::table('material_requests')
            ->join('store_products', function($join) {
                $join->whereRaw('JSON_CONTAINS(material_requests.items, JSON_OBJECT("product_id", store_products.uuid))');
            })
            ->join('store_categories', 'store_products.store_category_id', '=', 'store_categories.uuid')
            ->where('material_requests.store_id', $store->uuid)
            ->where('material_requests.status', 'accepted')
            ->whereBetween('material_requests.accepted_at', [$startDate, $endDate])
            ->selectRaw('store_categories.name as category, SUM(material_requests.quoted_price) as sales, COUNT(*) as orders')
            ->groupBy('store_categories.name')
            ->orderByDesc('sales')
            ->get();

        // Top customers
        $topCustomers = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->map(function($requests, $userId) {
                return [
                    'user' => $requests->first()->user,
                    'orders' => $requests->count(),
                    'total_spent' => $requests->sum('quoted_price'),
                ];
            })
            ->sortByDesc('total_spent')
            ->take(10);

        // Previous period comparison
        $previousStartDate = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $previousEndDate = $startDate;
        $previousTotalSales = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$previousStartDate, $previousEndDate])
            ->sum('quoted_price');

        $salesGrowth = $previousTotalSales > 0
            ? (($totalSales - $previousTotalSales) / $previousTotalSales) * 100
            : 0;

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => round($averageOrderValue, 2),
            'daily_sales' => $dailySales,
            'sales_by_category' => $salesByCategory,
            'top_customers' => $topCustomers,
            'sales_growth' => round($salesGrowth, 2),
            'previous_total_sales' => $previousTotalSales,
        ];
    }

    /**
     * Get store performance metrics.
     */
    private function getPerformanceMetrics(Store $store, $startDate, $endDate): array
    {
        // Conversion rate (views to orders)
        $totalViews = StoreView::where('store_id', $store->uuid)
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->count();

        $totalOrders = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->count();

        $conversionRate = $totalViews > 0 ? ($totalOrders / $totalViews) * 100 : 0;

        // Average response time (time from request to quote)
        $averageResponseTime = MaterialRequest::where('store_id', $store->uuid)
            ->whereNotNull('quoted_at')
            ->whereNotNull('created_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function($request) {
                return $request->created_at->diffInHours($request->quoted_at);
            })
            ->average();

        // Order completion rate
        $totalRequests = MaterialRequest::where('store_id', $store->uuid)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedRequests = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->count();

        $completionRate = $totalRequests > 0 ? ($completedRequests / $totalRequests) * 100 : 0;

        // Customer retention
        $uniqueCustomers = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');

        $returningCustomers = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [$startDate, $endDate])
            ->get()
            ->groupBy('user_id')
            ->filter(function($requests) {
                return $requests->count() > 1;
            })
            ->count();

        $retentionRate = $uniqueCustomers > 0 ? ($returningCustomers / $uniqueCustomers) * 100 : 0;

        // Average rating
        $averageRating = \App\Models\StoreReview::where('store_id', $store->uuid)
            ->where('is_approved', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('rating') ?? 0;

        return [
            'conversion_rate' => round($conversionRate, 2),
            'average_response_time' => round($averageResponseTime ?? 0, 2),
            'completion_rate' => round($completionRate, 2),
            'customer_retention_rate' => round($retentionRate, 2),
            'unique_customers' => $uniqueCustomers,
            'returning_customers' => $returningCustomers,
            'average_rating' => round($averageRating, 2),
            'total_views' => $totalViews,
            'total_orders' => $totalOrders,
        ];
    }

    /**
     * Get comparison data for store comparison reports.
     */
    private function getComparisonData(Store $store, $startDate, $endDate): array
    {
        // Get average metrics for all stores (for comparison)
        $allStoresAvg = [
            'avg_views' => StoreView::whereBetween('viewed_at', [$startDate, $endDate])
                ->selectRaw('AVG(view_count) as avg')
                ->fromSub(function($query) use ($startDate, $endDate) {
                    $query->from('store_views')
                        ->selectRaw('store_id, COUNT(*) as view_count')
                        ->whereBetween('viewed_at', [$startDate, $endDate])
                        ->groupBy('store_id');
                }, 'store_view_counts')
                ->value('avg') ?? 0,

            'avg_sales' => MaterialRequest::where('status', 'accepted')
                ->whereBetween('accepted_at', [$startDate, $endDate])
                ->selectRaw('AVG(total_sales) as avg')
                ->fromSub(function($query) use ($startDate, $endDate) {
                    $query->from('material_requests')
                        ->selectRaw('store_id, SUM(quoted_price) as total_sales')
                        ->where('status', 'accepted')
                        ->whereBetween('accepted_at', [$startDate, $endDate])
                        ->groupBy('store_id');
                }, 'store_sales')
                ->value('avg') ?? 0,

            'avg_orders' => MaterialRequest::where('status', 'accepted')
                ->whereBetween('accepted_at', [$startDate, $endDate])
                ->selectRaw('AVG(order_count) as avg')
                ->fromSub(function($query) use ($startDate, $endDate) {
                    $query->from('material_requests')
                        ->selectRaw('store_id, COUNT(*) as order_count')
                        ->where('status', 'accepted')
                        ->whereBetween('accepted_at', [$startDate, $endDate])
                        ->groupBy('store_id');
                }, 'store_orders')
                ->value('avg') ?? 0,
        ];

        // Current store metrics
        $currentStoreMetrics = [
            'views' => StoreView::where('store_id', $store->uuid)
                ->whereBetween('viewed_at', [$startDate, $endDate])
                ->count(),
            'sales' => MaterialRequest::where('store_id', $store->uuid)
                ->where('status', 'accepted')
                ->whereBetween('accepted_at', [$startDate, $endDate])
                ->sum('quoted_price'),
            'orders' => MaterialRequest::where('store_id', $store->uuid)
                ->where('status', 'accepted')
                ->whereBetween('accepted_at', [$startDate, $endDate])
                ->count(),
        ];

        return [
            'all_stores_avg' => $allStoresAvg,
            'current_store' => $currentStoreMetrics,
        ];
    }
}
