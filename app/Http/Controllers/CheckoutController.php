<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{

    /**
     * Show checkout page.
     */
    public function index(): View|RedirectResponse
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::where('status', 'approved')->find($productId);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->final_price * $quantity,
                ];
                $subtotal += $product->final_price * $quantity;
            }
        }

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk valid di keranjang');
        }

        $user = Auth::user();
        $total = $subtotal; // No tax or shipping for digital products

        return view('checkout.index', compact('items', 'subtotal', 'total', 'user'));
    }

    /**
     * Process checkout.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => ['required', 'string', 'in:balance,manual,midtrans'],
        ]);

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        $user = Auth::user();
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::where('status', 'approved')->find($productId);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->final_price * $quantity,
                ];
                $total += $product->final_price * $quantity;
            }
        }

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk valid di keranjang');
        }

        // Check balance if using balance payment
        if ($request->payment_method === 'balance' && $user->balance < $total) {
            return back()->with('error', 'Saldo tidak mencukupi. Saldo Anda: Rp ' . number_format($user->balance, 0, ',', '.'));
        }

        DB::beginTransaction();
        try {
            $orders = [];

            foreach ($items as $item) {
                $product = $item['product'];
                $amount = $product->final_price;
                $orderTotal = $amount * $item['quantity'];

                // Create order
                $order = Order::create([
                    'user_id' => $user->id,
                    'orderable_id' => $product->id,
                    'orderable_type' => Product::class,
                    'type' => 'product',
                    'amount' => $amount,
                    'discount' => $product->discount_price ? ($product->price - $product->discount_price) : 0,
                    'total' => $orderTotal,
                    'status' => $request->payment_method === 'balance' ? 'completed' : 'pending',
                    'payment_method' => $request->payment_method,
                    'completed_at' => $request->payment_method === 'balance' ? now() : null,
                    'download_token' => $request->payment_method === 'balance' ? Str::random(64) : null,
                    'download_expires_at' => $request->payment_method === 'balance' ? now()->addDays(30) : null,
                    'download_count' => 0,
                    'max_downloads' => 5,
                ]);

                // Update product sales count
                $product->increment('sales_count');

                // Create transaction if using balance
                if ($request->payment_method === 'balance') {
                    // Deduct from buyer
                    $user->decrement('balance', $orderTotal);

                    // Add to seller
                    $product->user->increment('balance', $orderTotal);

                    Transaction::create([
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'type' => 'purchase',
                        'amount' => -$orderTotal,
                        'description' => 'Pembelian: ' . $product->title,
                        'status' => 'completed',
                    ]);

                    Transaction::create([
                        'user_id' => $product->user_id,
                        'order_id' => $order->id,
                        'type' => 'commission',
                        'amount' => $orderTotal,
                        'description' => 'Penjualan: ' . $product->title,
                        'status' => 'completed',
                    ]);
                }

                $orders[] = $order;

                // Send notification
                $user->notify(new OrderStatusNotification($order, $order->status));
            }

            DB::commit();

            // Clear cart
            Session::forget('cart');

            if ($request->payment_method === 'balance') {
                return redirect()->route('orders.show', $orders[0])->with('success', 'Pembelian berhasil! File dapat diunduh sekarang.');
            } else {
                return redirect()->route('orders.index')->with('success', 'Pesanan dibuat. Menunggu konfirmasi pembayaran.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
