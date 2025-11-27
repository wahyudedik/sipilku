<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MaterialRequestController extends Controller
{
    /**
     * Display a listing of material requests for the store.
     */
    public function index(Request $request): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        $query = MaterialRequest::where('store_id', $store->uuid)
            ->with(['user', 'projectLocation'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by delivery status
        if ($request->has('delivery_status') && $request->delivery_status) {
            $query->where('delivery_status', $request->delivery_status);
        }

        $materialRequests = $query->paginate(15)->withQueryString();

        return view('store.material-requests.index', compact('materialRequests', 'store'));
    }

    /**
     * Display the specified material request.
     */
    public function show(MaterialRequest $materialRequest): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store || $materialRequest->store_id !== $store->uuid) {
            abort(403, 'Unauthorized');
        }

        $materialRequest->load(['user', 'projectLocation', 'order']);

        return view('store.material-requests.show', compact('materialRequest', 'store'));
    }

    /**
     * Show the form for providing a quote.
     */
    public function quote(MaterialRequest $materialRequest): View
    {
        $store = Auth::user()->stores()->first();

        if (!$store || $materialRequest->store_id !== $store->uuid) {
            abort(403, 'Unauthorized');
        }

        if ($materialRequest->status !== 'pending') {
            return redirect()->route('store.material-requests.show', $materialRequest)
                ->with('error', 'This request is no longer pending.');
        }

        $materialRequest->load(['user', 'projectLocation']);

        return view('store.material-requests.quote', compact('materialRequest', 'store'));
    }

    /**
     * Store a quote for the material request.
     */
    public function storeQuote(Request $request, MaterialRequest $materialRequest): RedirectResponse
    {
        $store = Auth::user()->stores()->first();

        if (!$store || $materialRequest->store_id !== $store->uuid) {
            abort(403, 'Unauthorized');
        }

        if ($materialRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request is no longer pending.');
        }

        $validated = $request->validate([
            'quoted_price' => ['required', 'numeric', 'min:0'],
            'quote_message' => ['nullable', 'string', 'max:1000'],
        ]);

        $materialRequest->update([
            'status' => 'quoted',
            'quoted_price' => $validated['quoted_price'],
            'quote_message' => $validated['quote_message'] ?? null,
            'quoted_at' => now(),
        ]);

        // TODO: Send notification to contractor

        return redirect()->route('store.material-requests.show', $materialRequest)
            ->with('success', 'Quote submitted successfully.');
    }

    /**
     * Update delivery status.
     */
    public function updateDeliveryStatus(Request $request, MaterialRequest $materialRequest): RedirectResponse
    {
        $store = Auth::user()->stores()->first();

        if (!$store || $materialRequest->store_id !== $store->uuid) {
            abort(403, 'Unauthorized');
        }

        if ($materialRequest->status !== 'accepted') {
            return redirect()->back()
                ->with('error', 'Only accepted requests can have delivery status updated.');
        }

        $validated = $request->validate([
            'delivery_status' => ['required', 'in:pending,preparing,ready,in_transit,delivered,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $updateData = [
            'delivery_status' => $validated['delivery_status'],
        ];

        // Set timestamps based on status
        switch ($validated['delivery_status']) {
            case 'preparing':
                $updateData['preparing_at'] = now();
                break;
            case 'ready':
                $updateData['ready_at'] = now();
                break;
            case 'in_transit':
                $updateData['in_transit_at'] = now();
                if ($validated['tracking_number']) {
                    $updateData['tracking_number'] = $validated['tracking_number'];
                }
                break;
            case 'delivered':
                $updateData['delivered_at'] = now();
                break;
        }

        if (isset($validated['delivery_notes'])) {
            $updateData['delivery_notes'] = $validated['delivery_notes'];
        }

        $materialRequest->update($updateData);

        // TODO: Send notification to contractor

        return redirect()->route('store.material-requests.show', $materialRequest)
            ->with('success', 'Delivery status updated successfully.');
    }

    /**
     * Cancel a material request (store side).
     */
    public function cancel(Request $request, MaterialRequest $materialRequest): RedirectResponse
    {
        $store = Auth::user()->stores()->first();

        if (!$store || $materialRequest->store_id !== $store->uuid) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($materialRequest->status, ['pending', 'quoted'])) {
            return redirect()->back()
                ->with('error', 'Cannot cancel request with current status.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $materialRequest->update([
            'status' => 'cancelled',
            'rejection_reason' => $validated['cancellation_reason'],
            'rejected_at' => now(),
        ]);

        // TODO: Send notification to contractor

        return redirect()->route('store.material-requests.index')
            ->with('success', 'Material request cancelled.');
    }
}

