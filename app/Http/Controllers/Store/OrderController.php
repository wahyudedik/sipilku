<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display order management page.
     */
    public function index(Request $request): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        $query = MaterialRequest::where('store_id', $store->uuid)
            ->where('status', 'accepted')
            ->with(['user', 'projectLocation']);

        // Filter by delivery status
        if ($request->has('status') && $request->status) {
            $query->where('delivery_status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest('accepted_at')->paginate(15)->withQueryString();

        return view('store.orders.index', compact('orders', 'store'));
    }

    /**
     * Show order details.
     */
    public function show(MaterialRequest $materialRequest): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store || $materialRequest->store_id !== $store->uuid) {
            abort(403, 'Unauthorized');
        }

        $materialRequest->load(['user', 'projectLocation', 'order']);

        return view('store.orders.show', compact('materialRequest', 'store'));
    }
}

