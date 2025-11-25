<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\QuoteRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display buyer dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Statistics
        $totalOrders = Order::where('user_id', $user->id)->count();
        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $pendingOrders = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $totalSpent = Transaction::where('user_id', $user->id)
            ->where('type', 'purchase')
            ->where('status', 'completed')
            ->sum(DB::raw('ABS(amount)'));

        // Recent purchases (last 5)
        $recentPurchases = Order::where('user_id', $user->id)
            ->with(['orderable', 'orderable.category'])
            ->latest()
            ->limit(5)
            ->get();

        // Active service orders (not completed)
        $activeServiceOrders = Order::where('user_id', $user->id)
            ->where('type', 'service')
            ->whereIn('status', ['pending', 'processing'])
            ->with(['orderable', 'orderable.category', 'quoteRequest'])
            ->latest()
            ->limit(5)
            ->get();

        // Pending quotes
        $pendingQuotes = QuoteRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'quoted'])
            ->with(['service', 'service.user', 'service.category'])
            ->latest()
            ->limit(5)
            ->get();

        // Download history (orders with downloads)
        $downloadHistory = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('download_token')
            ->where('download_count', '>', 0)
            ->with(['orderable', 'orderable.category'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Account balance
        $balance = $user->balance;

        return view('buyer.dashboard', compact(
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'totalSpent',
            'recentPurchases',
            'activeServiceOrders',
            'pendingQuotes',
            'downloadHistory',
            'balance'
        ));
    }
}
