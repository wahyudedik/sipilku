<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display seller dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Product Statistics
        $totalProducts = Product::where('user_id', $user->id)->count();
        $approvedProducts = Product::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        $pendingProducts = Product::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $rejectedProducts = Product::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        // Service Statistics
        $totalServices = Service::where('user_id', $user->id)->count();
        $approvedServices = Service::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        $pendingServices = Service::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Sales Statistics
        $totalProductSales = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('orderable_type', Product::class);
        })
        ->where('status', 'completed')
        ->count();

        $totalServiceOrders = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('orderable_type', Service::class);
        })
        ->where('status', 'completed')
        ->count();

        $totalProductRevenue = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('orderable_type', Product::class);
        })
        ->where('status', 'completed')
        ->sum('total');

        $totalServiceRevenue = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('orderable_type', Service::class);
        })
        ->where('status', 'completed')
        ->sum('total');

        $totalRevenue = $totalProductRevenue + $totalServiceRevenue;

        // Commission Statistics
        $totalCommissions = Transaction::where('user_id', $user->id)
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->sum('amount');

        $pendingCommissions = Transaction::where('user_id', $user->id)
            ->where('type', 'commission')
            ->where('status', 'pending')
            ->sum('amount');

        $totalPayouts = abs(Transaction::where('user_id', $user->id)
            ->where('type', 'payout')
            ->where('status', 'completed')
            ->sum('amount'));

        $availableBalance = $user->balance;

        // Recent Orders (for seller's products/services)
        $recentOrders = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['orderable', 'user', 'orderable.category'])
        ->latest()
        ->limit(5)
        ->get();

        // Service Orders (active)
        $activeServiceOrders = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('orderable_type', Service::class);
        })
        ->whereIn('status', ['pending', 'processing'])
        ->with(['orderable', 'user', 'quoteRequest'])
        ->latest()
        ->limit(5)
        ->get();

        // Top Selling Products
        $topProducts = Product::where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();

        // Top Selling Services
        $topServices = Service::where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('completed_orders', 'desc')
            ->limit(5)
            ->get();

        // Withdrawal History
        $recentWithdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Monthly Revenue (last 6 months)
        $monthlyRevenue = Order::whereHas('orderable', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('status', 'completed')
        ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->limit(6)
        ->get();

        return view('seller.dashboard', compact(
            'totalProducts',
            'approvedProducts',
            'pendingProducts',
            'rejectedProducts',
            'totalServices',
            'approvedServices',
            'pendingServices',
            'totalProductSales',
            'totalServiceOrders',
            'totalProductRevenue',
            'totalServiceRevenue',
            'totalRevenue',
            'totalCommissions',
            'pendingCommissions',
            'totalPayouts',
            'availableBalance',
            'recentOrders',
            'activeServiceOrders',
            'topProducts',
            'topServices',
            'recentWithdrawals',
            'monthlyRevenue'
        ));
    }
}
