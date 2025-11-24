<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Notifications\OrderStatusNotification;
use App\Notifications\PaymentConfirmedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'orderable'])->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'orderable', 'orderable.category', 'transactions']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Confirm payment for manual payment orders.
     */
    public function confirmPayment(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending' || $order->payment_method !== 'manual') {
            return back()->with('error', 'Hanya pesanan dengan status pending dan metode pembayaran manual yang dapat dikonfirmasi.');
        }

        DB::beginTransaction();
        try {
            // Update order status
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

            // Transfer balance to seller
            $product = $order->orderable;
            if ($product && $product->user) {
                $product->user->increment('balance', $order->total);

                // Create transaction for seller
                Transaction::create([
                    'user_id' => $product->user_id,
                    'order_id' => $order->id,
                    'type' => 'commission',
                    'amount' => $order->total,
                    'description' => 'Penjualan: ' . ($product->title ?? 'Produk'),
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            // Update product sales count
            if ($product) {
                $product->increment('sales_count');
            }

            DB::commit();

            // Send notifications
            $order->user->notify(new PaymentConfirmedNotification($order));
            $order->user->notify(new OrderStatusNotification($order, 'completed'));

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Pembayaran berhasil dikonfirmasi. Pesanan telah diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order.
     */
    public function cancel(Order $order): RedirectResponse
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'Pesanan yang sudah selesai tidak dapat dibatalkan.');
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        // Send notification
        $order->user->notify(new OrderStatusNotification($order, 'cancelled'));

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
