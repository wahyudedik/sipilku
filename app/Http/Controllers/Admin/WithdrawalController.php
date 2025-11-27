<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    /**
     * Display withdrawal approval page.
     */
    public function index(Request $request): View
    {
        $query = Withdrawal::with('user')->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->has('method') && $request->method !== 'all') {
            $query->where('method', $request->method);
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
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $withdrawals = $query->paginate(30)->withQueryString();

        // Statistics
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $pendingAmount = Withdrawal::where('status', 'pending')->sum('amount');
        $totalWithdrawals = Withdrawal::where('status', 'completed')->sum('amount');
        $monthWithdrawals = Withdrawal::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return view('admin.financial.withdrawals', compact(
            'withdrawals',
            'pendingWithdrawals',
            'pendingAmount',
            'totalWithdrawals',
            'monthWithdrawals'
        ));
    }

    /**
     * Display withdrawal detail.
     */
    public function show(Withdrawal $withdrawal): View
    {
        $withdrawal->load('user');

        // Get related transaction
        $transaction = Transaction::where('type', 'payout')
            ->where('metadata->withdrawal_id', $withdrawal->id)
            ->first();

        return view('admin.financial.withdrawal-detail', compact('withdrawal', 'transaction'));
    }

    /**
     * Approve withdrawal.
     */
    public function approve(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Hanya withdrawal dengan status pending yang dapat disetujui.');
        }

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        try {
            // Update withdrawal status
            $withdrawal->update([
                'status' => 'completed',
                'processed_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Update related transaction
            $transaction = Transaction::where('type', 'payout')
                ->where('metadata->withdrawal_id', $withdrawal->id)
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'description' => $transaction->description . ' - Approved',
                ]);
            }

            DB::commit();

            return redirect()->route('admin.withdrawals.index')
                ->with('success', 'Withdrawal berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject withdrawal.
     */
    public function reject(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Hanya withdrawal dengan status pending yang dapat ditolak.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        try {
            // Update withdrawal status
            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'admin_notes' => $request->admin_notes,
            ]);

            // Refund balance to user
            $withdrawal->user->increment('balance', $withdrawal->amount);

            // Update related transaction
            $transaction = Transaction::where('type', 'payout')
                ->where('metadata->withdrawal_id', $withdrawal->id)
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'failed',
                    'description' => $transaction->description . ' - Rejected: ' . $request->rejection_reason,
                ]);
            }

            // Create refund transaction
            Transaction::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'refund',
                'amount' => $withdrawal->amount,
                'status' => 'completed',
                'description' => 'Refund withdrawal: ' . $withdrawal->uuid,
                'metadata' => [
                    'withdrawal_id' => $withdrawal->id,
                    'reason' => 'Withdrawal rejected',
                ],
                'completed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.withdrawals.index')
                ->with('success', 'Withdrawal berhasil ditolak dan saldo telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve withdrawals.
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $request->validate([
            'withdrawal_ids' => ['required', 'array', 'min:1'],
            'withdrawal_ids.*' => ['exists:withdrawals,uuid'],
        ]);

        $withdrawals = Withdrawal::whereIn('uuid', $request->withdrawal_ids)
            ->where('status', 'pending')
            ->get();

        $count = 0;
        foreach ($withdrawals as $withdrawal) {
            try {
                $withdrawal->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                ]);

                $transaction = Transaction::where('type', 'payout')
                    ->where('metadata->withdrawal_id', $withdrawal->id)
                    ->first();

                if ($transaction) {
                    $transaction->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                }

                $count++;
            } catch (\Exception $e) {
                continue;
            }
        }

        return redirect()->route('admin.withdrawals.index')
            ->with('success', "{$count} withdrawal berhasil disetujui.");
    }
}
