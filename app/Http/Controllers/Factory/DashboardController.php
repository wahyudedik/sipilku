<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\FactoryRequest;
use App\Models\FactoryReview;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display factory owner dashboard.
     */
    public function index(Request $request): View
    {
        $factory = Auth::user()->factories()->first();

        if (!$factory) {
            return redirect()->route('factories.create')
                ->with('info', 'Anda belum memiliki pabrik. Silakan daftarkan pabrik Anda.');
        }

        // Order Statistics
        $orderStats = $this->getOrderStatistics($factory);

        // Order Management
        $orders = $this->getOrders($factory, $request->get('status'));

        // Sales Statistics
        $salesStats = $this->getSalesStatistics($factory);

        // Earnings & Commission
        $earnings = $this->getEarnings($factory);

        // Recent Quote Requests
        $recentQuoteRequests = FactoryRequest::where('factory_id', $factory->uuid)
            ->with(['user', 'projectLocation', 'factoryType'])
            ->latest()
            ->limit(5)
            ->get();

        // Recent Reviews
        $recentReviews = FactoryReview::where('factory_id', $factory->uuid)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Capacity & Availability
        $capacityStatus = $this->getCapacityStatus($factory);

        // Delivery Schedule (upcoming deliveries)
        $upcomingDeliveries = $this->getUpcomingDeliveries($factory);

        return view('factories.dashboard', compact(
            'factory',
            'orderStats',
            'orders',
            'salesStats',
            'earnings',
            'recentQuoteRequests',
            'recentReviews',
            'capacityStatus',
            'upcomingDeliveries'
        ));
    }

    /**
     * Get order statistics.
     */
    private function getOrderStatistics(Factory $factory): array
    {
        $factoryRequests = FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->get();

        return [
            'pending' => $factoryRequests->where('delivery_status', 'pending')->count(),
            'preparing' => $factoryRequests->where('delivery_status', 'preparing')->count(),
            'ready' => $factoryRequests->where('delivery_status', 'ready')->count(),
            'in_transit' => $factoryRequests->where('delivery_status', 'in_transit')->count(),
            'delivered' => $factoryRequests->where('delivery_status', 'delivered')->count(),
            'total' => $factoryRequests->count(),
            'pending_quotes' => FactoryRequest::where('factory_id', $factory->uuid)
                ->where('status', 'pending')
                ->count(),
            'quoted' => FactoryRequest::where('factory_id', $factory->uuid)
                ->where('status', 'quoted')
                ->count(),
        ];
    }

    /**
     * Get orders for the factory.
     */
    private function getOrders(Factory $factory, ?string $status = null): array
    {
        $query = FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->with(['user', 'projectLocation', 'factoryType']);

        if ($status) {
            $query->where('delivery_status', $status);
        }

        $factoryRequests = $query->latest()->limit(20)->get();

        return $factoryRequests->map(function($request) {
            return [
                'id' => $request->uuid,
                'type' => 'factory_request',
                'customer' => $request->user->name,
                'amount' => $request->total_cost,
                'status' => $request->delivery_status ?? 'pending',
                'created_at' => $request->accepted_at ?? $request->created_at,
                'project_location' => $request->projectLocation?->name,
                'tracking_number' => $request->tracking_number,
                'items_count' => count($request->items ?? []),
            ];
        })->toArray();
    }

    /**
     * Get sales statistics.
     */
    private function getSalesStatistics(Factory $factory): array
    {
        $acceptedRequests = FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->get();

        $totalSales = $acceptedRequests->sum('total_cost');
        $monthlySales = $acceptedRequests
            ->where('accepted_at', '>=', now()->startOfMonth())
            ->sum('total_cost');
        $totalOrders = $acceptedRequests->count();
        $monthlyOrders = $acceptedRequests
            ->where('accepted_at', '>=', now()->startOfMonth())
            ->count();

        // Calculate growth
        $lastMonthSales = FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->whereBetween('accepted_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->sum('total_cost');

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
     * Get earnings and commission.
     */
    private function getEarnings(Factory $factory): array
    {
        // Get total earnings from accepted factory requests
        $totalEarnings = FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->sum('total_cost');

        // Calculate commission (assuming 10% platform fee)
        $platformFee = 0.10;
        $totalCommission = $totalEarnings * $platformFee;
        $netEarnings = $totalEarnings - $totalCommission;

        // Monthly earnings
        $monthlyEarnings = FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->where('accepted_at', '>=', now()->startOfMonth())
            ->sum('total_cost');

        $monthlyCommission = $monthlyEarnings * $platformFee;
        $monthlyNetEarnings = $monthlyEarnings - $monthlyCommission;

        // Pending withdrawals
        $pendingWithdrawals = Withdrawal::where('user_id', $factory->user_id)
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

    /**
     * Get capacity and availability status.
     */
    private function getCapacityStatus(Factory $factory): array
    {
        // Get active orders count
        $activeOrders = FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->whereIn('delivery_status', ['pending', 'preparing', 'ready', 'in_transit'])
            ->count();

        // Get factory capacity (if set) - capacity is stored as JSON or integer
        $capacity = null;
        if ($factory->capacity) {
            if (is_array($factory->capacity)) {
                // If capacity is JSON array, get max capacity or daily capacity
                $capacity = $factory->capacity['max'] ?? $factory->capacity['daily'] ?? $factory->capacity['monthly'] ?? null;
            } else {
                $capacity = is_numeric($factory->capacity) ? (int)$factory->capacity : null;
            }
        }
        
        $capacityUsed = $activeOrders;
        $capacityPercentage = $capacity ? ($capacityUsed / $capacity) * 100 : null;

        // Check availability based on capacity
        $isAvailable = true;
        if ($capacity && $capacityPercentage >= 90) {
            $isAvailable = false;
        }

        return [
            'capacity' => $capacity,
            'capacity_used' => $capacityUsed,
            'capacity_percentage' => $capacityPercentage ? round($capacityPercentage, 2) : null,
            'is_available' => $isAvailable,
            'status' => $isAvailable ? 'Available' : 'Near Capacity',
        ];
    }

    /**
     * Get upcoming deliveries.
     */
    private function getUpcomingDeliveries(Factory $factory, int $days = 7): array
    {
        $upcoming = FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->whereIn('delivery_status', ['ready', 'in_transit'])
            ->where(function($query) use ($days) {
                $query->whereNull('delivered_at')
                    ->orWhere('delivered_at', '>=', now())
                    ->orWhere('delivered_at', '<=', now()->addDays($days));
            })
            ->with(['user', 'projectLocation'])
            ->orderBy('ready_at', 'asc')
            ->limit(10)
            ->get();

        return $upcoming->map(function($request) {
            return [
                'id' => $request->uuid,
                'customer' => $request->user->name,
                'project_location' => $request->projectLocation?->name,
                'status' => $request->delivery_status,
                'ready_at' => $request->ready_at,
                'tracking_number' => $request->tracking_number,
                'total_cost' => $request->total_cost,
            ];
        })->toArray();
    }

    /**
     * Get factory type-specific dashboard data.
     */
    private function getTypeSpecificData(Factory $factory): array
    {
        $factoryType = $factory->factoryType;
        if (!$factoryType) {
            return [];
        }

        $data = [
            'type_name' => $factoryType->name,
            'type_slug' => $factoryType->slug,
        ];

        // Type-specific statistics
        switch (strtolower($factoryType->slug)) {
            case 'beton':
            case 'concrete':
                $data['active_productions'] = FactoryRequest::where('factory_id', $factory->uuid)
                    ->where('status', 'accepted')
                    ->whereIn('delivery_status', ['preparing', 'ready'])
                    ->count();
                $data['ready_mix_orders'] = FactoryRequest::where('factory_id', $factory->uuid)
                    ->where('status', 'accepted')
                    ->where('delivery_status', 'ready')
                    ->count();
                break;
            
            case 'bata':
            case 'brick':
                $data['stock_levels'] = $factory->products()
                    ->where('is_available', true)
                    ->whereNotNull('stock')
                    ->sum('stock');
                break;
            
            case 'precast':
                $data['production_queue'] = FactoryRequest::where('factory_id', $factory->uuid)
                    ->where('status', 'accepted')
                    ->whereIn('delivery_status', ['pending', 'preparing'])
                    ->count();
                break;
        }

        return $data;
    }
}

