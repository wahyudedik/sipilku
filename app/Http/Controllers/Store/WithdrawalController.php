<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\Store;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    /**
     * Display withdrawal history.
     */
    public function index(): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        $withdrawals = Withdrawal::where('user_id', $store->user_id)
            ->latest()
            ->paginate(15);

        // Calculate available balance
        $earnings = $this->calculateEarnings($store);
        $pendingWithdrawals = Withdrawal::where('user_id', $store->user_id)
            ->where('status', 'pending')
            ->sum('amount');
        $availableBalance = max(0, $earnings['net_earnings'] - $pendingWithdrawals);

        return view('store.withdrawals.index', compact('withdrawals', 'store', 'availableBalance', 'earnings'));
    }

    /**
     * Show payout request form.
     */
    public function create(): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        // Calculate available balance
        $earnings = $this->calculateEarnings($store);
        $pendingWithdrawals = Withdrawal::where('user_id', $store->user_id)
            ->where('status', 'pending')
            ->sum('amount');
        $availableBalance = max(0, $earnings['net_earnings'] - $pendingWithdrawals);

        return view('store.withdrawals.create', compact('store', 'availableBalance', 'earnings'));
    }

    /**
     * Store payout request.
     */
    public function store(Request $request): RedirectResponse
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100000'], // Minimum 100k
            'method' => ['required', 'in:bank_transfer,e_wallet'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'bank_name' => ['required_if:method,bank_transfer', 'string', 'max:100'],
            'e_wallet_type' => ['required_if:method,e_wallet', 'in:ovo,dana,gopay,linkaja'],
        ]);

        // Calculate available balance
        $earnings = $this->calculateEarnings($store);
        $pendingWithdrawals = Withdrawal::where('user_id', $store->user_id)
            ->where('status', 'pending')
            ->sum('amount');
        $availableBalance = max(0, $earnings['net_earnings'] - $pendingWithdrawals);

        if ($validated['amount'] > $availableBalance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jumlah penarikan melebihi saldo yang tersedia.');
        }

        $validated['user_id'] = $store->user_id;
        $validated['status'] = 'pending';

        Withdrawal::create($validated);

        return redirect()->route('store.withdrawals.index')
            ->with('success', 'Permintaan penarikan berhasil diajukan. Menunggu persetujuan admin.');
    }

    /**
     * Calculate earnings for the store.
     */
    private function calculateEarnings(Store $store): array
    {
        $totalEarnings = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->sum('quoted_price');

        $platformFee = 0.10; // 10% platform fee
        $totalCommission = $totalEarnings * $platformFee;
        $netEarnings = $totalEarnings - $totalCommission;

        return [
            'total_earnings' => $totalEarnings,
            'total_commission' => $totalCommission,
            'net_earnings' => $netEarnings,
        ];
    }
}

