<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index(): View
    {
        // User Statistics
        $totalUsers = User::count();
        $totalBuyers = User::role('buyer')->count();
        $totalSellers = User::role('seller')->count();
        $pendingSellers = User::where('is_seller', true)
            ->where('is_active', false)
            ->count();

        // Product Statistics
        $totalProducts = Product::count();
        $pendingProducts = Product::where('status', 'pending')->count();
        $approvedProducts = Product::where('status', 'approved')->count();
        $rejectedProducts = Product::where('status', 'rejected')->count();

        // Service Statistics
        $totalServices = Service::count();
        $pendingServices = Service::where('status', 'pending')->count();
        $approvedServices = Service::where('status', 'approved')->count();

        // Order Statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        // Transaction Statistics
        $totalRevenue = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->sum('amount');
        $totalCommissions = Transaction::where('type', 'commission')
            ->where('status', 'completed')
            ->sum('amount');
        $totalPayouts = abs(Transaction::where('type', 'payout')
            ->where('status', 'completed')
            ->sum('amount'));
        $platformProfit = $totalRevenue - $totalCommissions - $totalPayouts;

        // Today Statistics
        $todayRevenue = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');
        $todayOrders = Order::whereDate('created_at', today())->count();

        // Month Statistics
        $monthRevenue = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $monthOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Withdrawal Statistics
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $pendingWithdrawalAmount = Withdrawal::where('status', 'pending')->sum('amount');
        $totalWithdrawals = Withdrawal::where('status', 'completed')->sum('amount');

        // Store & Factory Statistics
        $totalStores = \App\Models\Store::count();
        $pendingStores = \App\Models\Store::where('status', 'pending')->count();
        $totalFactories = \App\Models\Factory::count();
        $pendingFactories = \App\Models\Factory::where('status', 'pending')->count();

        // Recent Activities
        $recentUsers = User::latest()->limit(5)->get();
        $recentOrders = Order::with(['user', 'orderable'])->latest()->limit(5)->get();
        $recentWithdrawals = Withdrawal::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalBuyers',
            'totalSellers',
            'pendingSellers',
            'totalProducts',
            'pendingProducts',
            'approvedProducts',
            'rejectedProducts',
            'totalServices',
            'pendingServices',
            'approvedServices',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalRevenue',
            'totalCommissions',
            'totalPayouts',
            'platformProfit',
            'todayRevenue',
            'todayOrders',
            'monthRevenue',
            'monthOrders',
            'pendingWithdrawals',
            'pendingWithdrawalAmount',
            'totalWithdrawals',
            'totalStores',
            'pendingStores',
            'totalFactories',
            'pendingFactories',
            'recentUsers',
            'recentOrders',
            'recentWithdrawals'
        ));
    }
}
