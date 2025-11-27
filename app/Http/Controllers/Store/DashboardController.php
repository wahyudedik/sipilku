<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\Order;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\StoreReview;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display store owner dashboard.
     */
    public function index(): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            return redirect()->route('stores.create')
                ->with('info', 'Anda belum memiliki toko. Silakan daftarkan toko Anda.');
        }

        // Sales Statistics
        $salesStats = $this->getSalesStatistics($store);

        // Order Management
        $orders = $this->getOrders($store);
        $orderStats = $this->getOrderStatistics($store);

        // Inventory Alerts
        $inventoryAlerts = $this->getInventoryAlerts($store);

        // Earnings & Commission
        $earnings = $this->getEarnings($store);

        // Recent Material Requests
        $recentMaterialRequests = MaterialRequest::where('store_id', $store->uuid)
            ->with(['user', 'projectLocation'])
            ->latest()
            ->limit(5)
            ->get();

        // Recent Reviews
        $recentReviews = StoreReview::where('store_id', $store->uuid)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        return view('store.dashboard', compact(
            'store',
            'salesStats',
            'orders',
            'orderStats',
            'inventoryAlerts',
            'earnings',
            'recentMaterialRequests',
            'recentReviews'
        ));
    }

    /**
     * Get sales statistics.
     */
    private function getSalesStatistics(Store $store): array
    {
        // Get orders from material requests (accepted material requests)
        $acceptedMaterialRequests = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->get();

        $totalSales = $acceptedMaterialRequests->sum('quoted_price');
        $monthlySales = $acceptedMaterialRequests
            ->where('accepted_at', '>=', now()->startOfMonth())
            ->sum('quoted_price');
        $totalOrders = $acceptedMaterialRequests->count();
        $monthlyOrders = $acceptedMaterialRequests
            ->where('accepted_at', '>=', now()->startOfMonth())
            ->count();

        // Calculate growth
        $lastMonthSales = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->sum('quoted_price');

        $salesGrowth = $lastMonthSales > 0 
            ? (($monthlySales - $lastMonthSales) / $lastMonthSales) * 100 
            : 0;

        return [
            'total_sales' => $totalSales,
            'monthly_sales' => $monthlySales,
            'total_orders' => $totalOrders,
            'monthly_orders' => $monthlyOrders,
            'sales_growth' => round($salesGrowth, 2),
            'average_order_value' => $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0,
        ];
    }

    /**
     * Get orders for the store.
     */
    private function getOrders(Store $store): array
    {
        $materialRequests = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->with(['user', 'projectLocation'])
            ->latest()
            ->limit(10)
            ->get();

        return $materialRequests->map(function($request) {
            return [
                'id' => $request->uuid,
                'type' => 'material_request',
                'customer' => $request->user->name,
                'amount' => $request->quoted_price,
                'status' => $request->delivery_status ?? 'pending',
                'created_at' => $request->accepted_at ?? $request->created_at,
                'project_location' => $request->projectLocation?->name,
            ];
        })->toArray();
    }

    /**
     * Get order statistics.
     */
    private function getOrderStatistics(Store $store): array
    {
        $materialRequests = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->get();

        return [
            'pending' => $materialRequests->where('delivery_status', 'pending')->count(),
            'preparing' => $materialRequests->where('delivery_status', 'preparing')->count(),
            'ready' => $materialRequests->where('delivery_status', 'ready')->count(),
            'in_transit' => $materialRequests->where('delivery_status', 'in_transit')->count(),
            'delivered' => $materialRequests->where('delivery_status', 'delivered')->count(),
            'total' => $materialRequests->count(),
        ];
    }

    /**
     * Get inventory alerts (low stock products).
     */
    private function getInventoryAlerts(Store $store): array
    {
        $lowStockProducts = StoreProduct::where('store_id', $store->uuid)
            ->where('is_active', true)
            ->whereNotNull('stock')
            ->where('stock', '<=', 10)
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        $outOfStockProducts = StoreProduct::where('store_id', $store->uuid)
            ->where('is_active', true)
            ->whereNotNull('stock')
            ->where('stock', '<=', 0)
            ->limit(10)
            ->get();

        return [
            'low_stock' => $lowStockProducts,
            'out_of_stock' => $outOfStockProducts,
            'low_stock_count' => $lowStockProducts->count(),
            'out_of_stock_count' => $outOfStockProducts->count(),
        ];
    }

    /**
     * Get earnings and commission.
     */
    private function getEarnings(Store $store): array
    {
        // Get total earnings from accepted material requests
        $totalEarnings = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->sum('quoted_price');

        // Calculate commission (assuming 10% platform fee)
        $platformFee = 0.10;
        $totalCommission = $totalEarnings * $platformFee;
        $netEarnings = $totalEarnings - $totalCommission;

        // Monthly earnings
        $monthlyEarnings = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->where('accepted_at', '>=', now()->startOfMonth())
            ->sum('quoted_price');

        $monthlyCommission = $monthlyEarnings * $platformFee;
        $monthlyNetEarnings = $monthlyEarnings - $monthlyCommission;

        // Pending withdrawals
        $pendingWithdrawals = Withdrawal::where('user_id', $store->user_id)
            ->where('status', 'pending')
            ->sum('amount');

        // Available balance (net earnings - pending withdrawals)
        $availableBalance = $netEarnings - $pendingWithdrawals;

        return [
            'total_earnings' => $totalEarnings,
            'total_commission' => $totalCommission,
            'net_earnings' => $netEarnings,
            'monthly_earnings' => $monthlyEarnings,
            'monthly_commission' => $monthlyCommission,
            'monthly_net_earnings' => $monthlyNetEarnings,
            'pending_withdrawals' => $pendingWithdrawals,
            'available_balance' => max(0, $availableBalance),
        ];
    }
}

