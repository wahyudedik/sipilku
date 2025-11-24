<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestPayoutRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommissionController extends Controller
{
    /**
     * Display commission dashboard and statistics.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Get commission transactions
        $query = Transaction::where('user_id', $user->id)
            ->where('type', 'commission')
            ->with(['order.orderable'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->paginate(20)->withQueryString();

        // Calculate statistics
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

        $availableBalance = $user->balance; // Balance yang bisa di-withdraw

        // Monthly statistics
        $monthlyStats = Transaction::where('user_id', $user->id)
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Get withdrawal history
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('seller.commissions.index', compact(
            'commissions',
            'totalCommissions',
            'pendingCommissions',
            'totalPayouts',
            'availableBalance',
            'monthlyStats',
            'withdrawals'
        ));
    }

    /**
     * Display commission report.
     */
    public function report(Request $request): View
    {
        $user = Auth::user();

        $query = Transaction::where('user_id', $user->id)
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->with(['order.orderable']);

        // Date range filter
        $dateFrom = $request->get('date_from', now()->subMonths(3)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        $commissions = $query->get();

        // Group by product/service
        $byItem = $commissions->groupBy(function ($transaction) {
            return $transaction->order ? $transaction->order->orderable_id : 'unknown';
        })->map(function ($group) {
            return [
                'item' => $group->first()->order->orderable ?? null,
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        })->sortByDesc('total');

        // Group by month
        $byMonth = $commissions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m');
        })->map(function ($group) {
            return [
                'month' => $group->first()->created_at->format('F Y'),
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        })->sortKeys();

        $totalAmount = $commissions->sum('amount');
        $totalCount = $commissions->count();

        return view('seller.commissions.report', compact(
            'commissions',
            'byItem',
            'byMonth',
            'totalAmount',
            'totalCount',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Show payout request form.
     */
    public function requestPayout(): View
    {
        $user = Auth::user();
        $availableBalance = $user->balance;

        // Get recent withdrawals
        $recentWithdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('seller.commissions.request-payout', compact('availableBalance', 'recentWithdrawals'));
    }

    /**
     * Process payout request.
     */
    public function processPayout(RequestPayoutRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $amount = $request->amount;

        // Check minimum withdrawal
        if ($amount < 50000) {
            return back()->with('error', 'Minimum penarikan adalah Rp 50.000');
        }

        // Check available balance
        if ($user->balance < $amount) {
            return back()->with('error', 'Saldo tidak mencukupi. Saldo tersedia: Rp ' . number_format($user->balance, 0, ',', '.'));
        }

        DB::beginTransaction();
        try {
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'method' => $request->method,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name ?? null,
                'e_wallet_type' => $request->e_wallet_type ?? null,
                'status' => 'pending',
            ]);

            // Hold balance (deduct from available balance)
            $user->decrement('balance', $amount);

            // Create pending payout transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'payout',
                'amount' => -$amount,
                'status' => 'pending',
                'payment_method' => $request->method,
                'description' => 'Penarikan saldo: Rp ' . number_format($amount, 0, ',', '.'),
                'metadata' => [
                    'withdrawal_id' => $withdrawal->id,
                    'account_name' => $request->account_name,
                    'account_number' => $request->account_number,
                ],
            ]);

            DB::commit();

            return redirect()->route('seller.commissions.index')
                ->with('success', 'Permintaan penarikan berhasil dibuat. Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show withdrawal detail.
     */
    public function showWithdrawal(Withdrawal $withdrawal): View
    {
        // Verify ownership
        if ($withdrawal->user_id !== Auth::id()) {
            abort(403);
        }

        $withdrawal->load('user');

        return view('seller.commissions.show-withdrawal', compact('withdrawal'));
    }
}
