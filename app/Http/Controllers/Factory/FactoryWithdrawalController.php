<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FactoryWithdrawalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display withdrawal history.
     */
    public function index(Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $withdrawals = Withdrawal::where('user_id', $factory->user_id)
            ->latest()
            ->paginate(15);

        // Calculate available balance
        $totalEarnings = \App\Models\FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->sum('total_cost');

        $platformFee = 0.10;
        $totalCommission = $totalEarnings * $platformFee;
        $netEarnings = $totalEarnings - $totalCommission;

        $pendingWithdrawals = Withdrawal::where('user_id', $factory->user_id)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = max(0, $netEarnings - $pendingWithdrawals);

        return view('factories.withdrawals.index', compact('factory', 'withdrawals', 'availableBalance'));
    }

    /**
     * Show form to create withdrawal request.
     */
    public function create(Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Calculate available balance
        $totalEarnings = \App\Models\FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->sum('total_cost');

        $platformFee = 0.10;
        $totalCommission = $totalEarnings * $platformFee;
        $netEarnings = $totalEarnings - $totalCommission;

        $pendingWithdrawals = Withdrawal::where('user_id', $factory->user_id)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = max(0, $netEarnings - $pendingWithdrawals);

        return view('factories.withdrawals.create', compact('factory', 'availableBalance'));
    }

    /**
     * Store withdrawal request.
     */
    public function store(Request $request, Factory $factory): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Calculate available balance
        $totalEarnings = \App\Models\FactoryRequest::where('factory_id', $factory->uuid)
            ->where('status', 'accepted')
            ->sum('total_cost');

        $platformFee = 0.10;
        $totalCommission = $totalEarnings * $platformFee;
        $netEarnings = $totalEarnings - $totalCommission;

        $pendingWithdrawals = Withdrawal::where('user_id', $factory->user_id)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = max(0, $netEarnings - $pendingWithdrawals);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10000', 'max:' . $availableBalance],
            'method' => ['required', 'in:bank_transfer,e_wallet'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'bank_name' => ['required_if:method,bank_transfer', 'string', 'max:255'],
            'e_wallet_type' => ['required_if:method,e_wallet', 'in:gopay,ovo,dana,linkaja'],
        ]);

        $validated['user_id'] = $factory->user_id;
        $validated['status'] = 'pending';

        Withdrawal::create($validated);

        return redirect()->route('factories.withdrawals.index', $factory)
            ->with('success', 'Withdrawal request submitted successfully.');
    }
}

