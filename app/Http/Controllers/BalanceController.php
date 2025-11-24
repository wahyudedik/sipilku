<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopUpBalanceRequest;
use App\Models\Transaction;
use App\Notifications\BalanceTopUpNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class BalanceController extends Controller
{
    public function __construct()
    {
        // Setup Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Display balance and transaction history.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Get balance transactions
        $query = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['deposit', 'withdrawal', 'purchase', 'commission', 'refund', 'payout'])
            ->with(['order.orderable'])
            ->latest();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $transactions = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $totalDeposits = Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');

        $totalWithdrawals = abs(Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount'));

        $totalPurchases = abs(Transaction::where('user_id', $user->id)
            ->where('type', 'purchase')
            ->where('status', 'completed')
            ->sum('amount'));

        $totalCommissions = Transaction::where('user_id', $user->id)
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->sum('amount');

        return view('balance.index', compact('user', 'transactions', 'totalDeposits', 'totalWithdrawals', 'totalPurchases', 'totalCommissions'));
    }

    /**
     * Show top-up form.
     */
    public function topUp(): View
    {
        return view('balance.top-up');
    }

    /**
     * Process balance top-up.
     */
    public function processTopUp(TopUpBalanceRequest $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $amount = $request->amount;

        // Minimum top-up amount
        if ($amount < 10000) {
            return back()->with('error', 'Minimum top-up adalah Rp 10.000');
        }

        // Maximum top-up amount
        if ($amount > 10000000) {
            return back()->with('error', 'Maximum top-up adalah Rp 10.000.000');
        }

        try {
            // Create pending deposit transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'status' => 'pending',
                'payment_method' => 'midtrans',
                'description' => 'Top-up saldo: Rp ' . number_format($amount, 0, ',', '.'),
                'metadata' => [
                    'top_up_amount' => $amount,
                ],
            ]);

            // Generate Midtrans Snap token
            $params = [
                'transaction_details' => [
                    'order_id' => 'TOPUP-' . $transaction->uuid,
                    'gross_amount' => (int) $amount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                ],
                'item_details' => [
                    [
                        'id' => 'topup',
                        'price' => (int) $amount,
                        'quantity' => 1,
                        'name' => 'Top-up Saldo Sipilku',
                    ],
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Update transaction with snap token
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'snap_token' => $snapToken,
                ]),
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'transaction_id' => $transaction->uuid,
            ]);
        } catch (\Exception $e) {
            Log::error('Balance top-up error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses top-up: ' . $e->getMessage());
        }
    }

    /**
     * Handle Midtrans callback for top-up.
     */
    public function callbackTopUp(Request $request): JsonResponse
    {
        try {
            // Get notification from request body
            $notificationBody = $request->all();
            $notification = new Notification($notificationBody);

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;

            // Extract transaction UUID from order_id (format: TOPUP-{uuid})
            if (!str_starts_with($orderId, 'TOPUP-')) {
                Log::warning('Invalid top-up order ID format: ' . $orderId);
                return response()->json(['message' => 'Invalid order ID'], 400);
            }

            $transactionUuid = str_replace('TOPUP-', '', $orderId);
            $transaction = Transaction::where('uuid', $transactionUuid)
                ->where('type', 'deposit')
                ->whereIn('status', ['pending', 'processing'])
                ->first();

            if (!$transaction) {
                Log::warning('Top-up transaction not found: ' . $transactionUuid);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            DB::beginTransaction();

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    // Top-up successful
                    $this->completeTopUp($transaction, $notification);
                } else {
                    // Top-up denied
                    $this->failTopUp($transaction, 'Fraud detected');
                }
            } elseif ($transactionStatus === 'settlement') {
                // Top-up successful
                $this->completeTopUp($transaction, $notification);
            } elseif ($transactionStatus === 'pending') {
                // Top-up pending
                $transaction->update([
                    'status' => 'processing',
                    'payment_reference' => $notification->transaction_id,
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'midtrans_response' => $notification->getResponse(),
                    ]),
                ]);
            } elseif ($transactionStatus === 'deny' || $transactionStatus === 'expire' || $transactionStatus === 'cancel') {
                // Top-up failed
                $this->failTopUp($transaction, $transactionStatus);
            }

            DB::commit();

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Top-up callback error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Complete top-up and add balance to user.
     */
    protected function completeTopUp(Transaction $transaction, $notification): void
    {
        $user = $transaction->user;

        // Update transaction
        $transaction->update([
            'status' => 'completed',
            'payment_reference' => $notification->transaction_id,
            'completed_at' => now(),
            'metadata' => array_merge($transaction->metadata ?? [], [
                'midtrans_response' => $notification->getResponse(),
            ]),
        ]);

        // Add balance to user
        $user->increment('balance', $transaction->amount);

        // Send notification
        $user->notify(new BalanceTopUpNotification($transaction));
    }

    /**
     * Fail top-up.
     */
    protected function failTopUp(Transaction $transaction, string $reason): void
    {
        $transaction->update([
            'status' => 'failed',
            'description' => $transaction->description . ' - Failed: ' . $reason,
        ]);
    }

    /**
     * Show top-up status page.
     */
    public function topUpStatus(Transaction $transaction): View
    {
        // Verify ownership
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        return view('balance.top-up-status', compact('transaction'));
    }
}
