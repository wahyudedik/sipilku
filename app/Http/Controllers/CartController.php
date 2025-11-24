<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index(): View
    {
        $cart = Session::get('cart', []);
        $items = [];
        $subtotal = 0;
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::where('status', 'approved')->find($productId);
            if ($product) {
                $itemSubtotal = $product->final_price * $quantity;
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                ];
                $subtotal += $itemSubtotal;
                $total += $itemSubtotal;
            }
        }

        return view('cart.index', compact('items', 'subtotal', 'total'));
    }

    /**
     * Add product to cart.
     */
    public function add(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        // Only allow approved products
        if ($product->status !== 'approved') {
            return response()->json(['error' => 'Produk tidak tersedia'], 400);
        }

        $cart = Session::get('cart', []);
        $quantity = $request->input('quantity', 1);

        // For digital products, quantity is always 1
        if (isset($cart[$product->id])) {
            // Already in cart
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Produk sudah ada di keranjang']);
            }
            return redirect()->route('cart.index')->with('info', 'Produk sudah ada di keranjang');
        }

        $cart[$product->id] = 1; // Digital products are always quantity 1
        Session::put('cart', $cart);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Produk ditambahkan ke keranjang',
                'cart_count' => count($cart),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang');
    }

    /**
     * Remove product from cart.
     */
    public function remove(Product $product): RedirectResponse
    {
        $cart = Session::get('cart', []);
        unset($cart[$product->id]);
        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang');
    }

    /**
     * Clear the cart.
     */
    public function clear(): RedirectResponse
    {
        Session::forget('cart');
        return redirect()->route('cart.index')->with('success', 'Keranjang dikosongkan');
    }

    /**
     * Get cart count (for AJAX).
     */
    public function count(): JsonResponse
    {
        $cart = Session::get('cart', []);
        return response()->json(['count' => count($cart)]);
    }
}
