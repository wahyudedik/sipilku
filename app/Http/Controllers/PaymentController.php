<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Notifications\OrderStatusNotification;
use App\Notifications\PaymentConfirmedNotification;
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
use Midtrans\Transaction as MidtransTransaction;

class PaymentController extends Controller
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
     * Process payment for an order using Midtrans.
     */
    public function process(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if order is already paid
        if ($order->status === 'completed') {
            return back()->with('error', 'Pesanan ini sudah dibayar.');
        }

        // Check if order is pending
        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan ini tidak dapat diproses.');
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->uuid,
                    'gross_amount' => (int) $order->total,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone ?? '',
                ],
                'item_details' => [
                    [
                        'id' => $order->orderable_id,
                        'price' => (int) $order->total,
                        'quantity' => 1,
                        'name' => $order->orderable->title ?? 'Item',
                    ],
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Create pending transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'type' => 'purchase',
                'amount' => -$order->total,
                'status' => 'pending',
                'payment_method' => 'midtrans',
                'description' => 'Pembayaran untuk Order: ' . $order->uuid,
                'metadata' => [
                    'snap_token' => $snapToken,
                    'order_uuid' => $order->uuid,
                ],
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'transaction_id' => $transaction->uuid,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Handle Midtrans payment notification (webhook).
     */
    public function callback(Request $request): JsonResponse
    {
        try {
            // Get notification from request body
            $notificationBody = $request->all();
            $notification = new Notification($notificationBody);

            $orderId = $notification->order_id;
            $statusCode = $notification->status_code;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;

            // Find order by UUID
            $order = Order::where('uuid', $orderId)->first();

            if (!$order) {
                Log::warning('Order not found for payment callback: ' . $orderId);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Find transaction (can be pending or processing)
            $transaction = Transaction::where('order_id', $order->id)
                ->where('payment_method', 'midtrans')
                ->whereIn('status', ['pending', 'processing'])
                ->latest()
                ->first();

            DB::beginTransaction();

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    // Payment successful
                    $this->completePayment($order, $transaction, $notification);
                } else {
                    // Payment denied
                    $this->failPayment($order, $transaction, 'Fraud detected');
                }
            } elseif ($transactionStatus === 'settlement') {
                // Payment successful
                $this->completePayment($order, $transaction, $notification);
            } elseif ($transactionStatus === 'pending') {
                // Payment pending
                if ($transaction) {
                    $transaction->update([
                        'status' => 'processing',
                        'payment_reference' => $notification->transaction_id,
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'midtrans_response' => $notification->getResponse(),
                        ]),
                    ]);
                }
            } elseif ($transactionStatus === 'deny' || $transactionStatus === 'expire' || $transactionStatus === 'cancel') {
                // Payment failed
                $this->failPayment($order, $transaction, $transactionStatus);
            }

            DB::commit();

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment callback error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Complete payment and update order.
     */
    protected function completePayment(Order $order, ?Transaction $transaction, $notification): void
    {
        // Update order
        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Generate download token if not exists
        if (!$order->download_token) {
            $order->update([
                'download_token' => \Illuminate\Support\Str::random(64),
                'download_expires_at' => now()->addDays(30),
            ]);
        }

        // Update transaction
        if ($transaction) {
            $transaction->update([
                'status' => 'completed',
                'payment_reference' => $notification->transaction_id,
                'completed_at' => now(),
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'midtrans_response' => $notification->getResponse(),
                ]),
            ]);
        } else {
            // Create transaction if not exists
            Transaction::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'type' => 'purchase',
                'amount' => -$order->total,
                'status' => 'completed',
                'payment_method' => 'midtrans',
                'payment_reference' => $notification->transaction_id,
                'description' => 'Pembayaran untuk Order: ' . $order->uuid,
                'completed_at' => now(),
                'metadata' => [
                    'midtrans_response' => $notification->getResponse(),
                ],
            ]);
        }

        // Transfer balance to seller
        $orderable = $order->orderable;
        if ($orderable && $orderable->user) {
            $orderable->user->increment('balance', $order->total);

            // Create commission transaction for seller
            Transaction::create([
                'user_id' => $orderable->user_id,
                'order_id' => $order->id,
                'type' => 'commission',
                'amount' => $order->total,
                'status' => 'completed',
                'payment_method' => 'midtrans',
                'description' => 'Penjualan: ' . ($orderable->title ?? 'Item'),
                'completed_at' => now(),
            ]);
        }

        // Update sales count
        if ($orderable) {
            $orderable->increment('sales_count');
        }

        // Send notifications
        $order->user->notify(new PaymentConfirmedNotification($order));
        $order->user->notify(new OrderStatusNotification($order, 'completed'));
    }

    /**
     * Fail payment and update order.
     */
    protected function failPayment(Order $order, ?Transaction $transaction, string $reason): void
    {
        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'description' => $transaction->description . ' - Failed: ' . $reason,
            ]);
        }

        $order->user->notify(new OrderStatusNotification($order, 'cancelled'));
    }

    /**
     * Display payment history.
     */
    public function history(Request $request): View
    {
        $query = Transaction::where('user_id', Auth::id())
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

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('payments.history', compact('transactions'));
    }

    /**
     * Display payment status page.
     */
    public function status(Order $order): View
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction = Transaction::where('order_id', $order->id)
            ->where('payment_method', 'midtrans')
            ->latest()
            ->first();

        return view('payments.status', compact('order', 'transaction'));
    }
}
