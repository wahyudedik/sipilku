<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancialController extends Controller
{
    /**
     * Display transaction monitoring page.
     */
    public function transactions(Request $request): View
    {
        $query = Transaction::with(['user', 'order'])->latest();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(30)->withQueryString();

        // Statistics
        $totalRevenue = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->sum('amount');

        $totalCommissions = Transaction::where('type', 'commission')
            ->where('status', 'completed')
            ->sum('amount');

        $totalPayouts = abs(Transaction::where('type', 'payout')
            ->where('status', 'completed')
            ->sum('amount'));

        $pendingPayouts = abs(Transaction::where('type', 'payout')
            ->where('status', 'pending')
            ->sum('amount'));

        $todayRevenue = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $monthRevenue = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return view('admin.financial.transactions', compact(
            'transactions',
            'totalRevenue',
            'totalCommissions',
            'totalPayouts',
            'pendingPayouts',
            'todayRevenue',
            'monthRevenue'
        ));
    }

    /**
     * Display financial reports page.
     */
    public function reports(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subMonths(1)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Revenue by type
        $revenueByType = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->select('type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        // Revenue by month
        $revenueByMonth = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Revenue by payment method
        $revenueByPaymentMethod = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Top sellers by commission
        $topSellers = Transaction::where('type', 'commission')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->select('user_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Summary statistics
        $totalRevenue = Transaction::where('type', 'purchase')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->sum('amount');

        $totalCommissions = Transaction::where('type', 'commission')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->sum('amount');

        $totalPayouts = abs(Transaction::where('type', 'payout')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->sum('amount'));

        $platformProfit = $totalRevenue - $totalCommissions - $totalPayouts;

        return view('admin.financial.reports', compact(
            'revenueByType',
            'revenueByMonth',
            'revenueByPaymentMethod',
            'topSellers',
            'totalRevenue',
            'totalCommissions',
            'totalPayouts',
            'platformProfit',
            'dateFrom',
            'dateTo'
        ));
    }
}
