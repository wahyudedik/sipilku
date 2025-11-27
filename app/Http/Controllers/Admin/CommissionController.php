<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommissionController extends Controller
{
    /**
     * Display commission management page.
     */
    public function index(Request $request): View
    {
        $query = Transaction::where('type', 'commission')
            ->with(['user', 'order.orderable'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
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
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $commissions = $query->paginate(30)->withQueryString();

        // Statistics
        $totalCommissions = Transaction::where('type', 'commission')
            ->where('status', 'completed')
            ->sum('amount');

        $pendingCommissions = Transaction::where('type', 'commission')
            ->where('status', 'pending')
            ->sum('amount');

        $monthCommissions = Transaction::where('type', 'commission')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Top earners
        $topEarners = Transaction::where('type', 'commission')
            ->where('status', 'completed')
            ->select('user_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Sellers list for filter
        $sellers = User::whereHas('transactions', function($q) {
            $q->where('type', 'commission');
        })->get();

        return view('admin.financial.commissions', compact(
            'commissions',
            'totalCommissions',
            'pendingCommissions',
            'monthCommissions',
            'topEarners',
            'sellers'
        ));
    }
}
