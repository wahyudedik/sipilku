<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{

    /**
     * Display a listing of user orders.
     */
    public function index(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['orderable', 'orderable.category', 'quoteRequest'])
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['orderable', 'orderable.category', 'transactions', 'quoteRequest']);

        return view('orders.show', compact('order'));
    }
}
