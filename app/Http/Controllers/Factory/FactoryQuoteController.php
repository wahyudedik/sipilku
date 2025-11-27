<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\FactoryRequest;
use App\Helpers\GeolocationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FactoryQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display factory quote requests.
     */
    public function index(Request $request, Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $status = $request->get('status', 'all');
        $query = FactoryRequest::where('factory_id', $factory->uuid)
            ->with(['user', 'projectLocation', 'factoryType']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(15);

        return view('factories.quotes.index', compact('factory', 'requests', 'status'));
    }

    /**
     * Show quote request details.
     */
    public function show(Factory $factory, FactoryRequest $factoryRequest): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $factoryRequest->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        $factoryRequest->load(['user', 'projectLocation', 'factoryType']);

        // Calculate delivery cost if project location has coordinates
        $deliveryCost = null;
        $distance = null;
        if ($factoryRequest->projectLocation && $factoryRequest->projectLocation->hasCoordinates()) {
            $nearestLocation = $factory->locations
                ->where('is_active', true)
                ->filter(function($location) {
                    return $location->hasCoordinates();
                })
                ->map(function($location) use ($factoryRequest) {
                    $distance = GeolocationHelper::calculateDistance(
                        $factoryRequest->projectLocation->latitude,
                        $factoryRequest->projectLocation->longitude,
                        $location->latitude,
                        $location->longitude
                    );
                    return [
                        'location' => $location,
                        'distance' => $distance,
                    ];
                })
                ->sortBy('distance')
                ->first();

            if ($nearestLocation && $factory->delivery_price_per_km) {
                $distance = $nearestLocation['distance'];
                $deliveryCost = GeolocationHelper::calculateDeliveryCost(
                    $distance,
                    $factory->delivery_price_per_km
                );
            }
        }

        return view('factories.quotes.show', compact('factory', 'factoryRequest', 'deliveryCost', 'distance'));
    }

    /**
     * Submit quote for a factory request.
     */
    public function quote(Request $request, Factory $factory, FactoryRequest $factoryRequest): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $factoryRequest->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        if ($factoryRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending requests can be quoted.');
        }

        $validated = $request->validate([
            'quoted_price' => ['required', 'numeric', 'min:0'],
            'delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'additional_fees' => ['nullable', 'array'],
            'additional_fees.*.name' => ['required_with:additional_fees', 'string', 'max:255'],
            'additional_fees.*.amount' => ['required_with:additional_fees', 'numeric', 'min:0'],
            'quote_message' => ['nullable', 'string', 'max:1000'],
        ]);

        // Calculate total cost
        $quotedPrice = $validated['quoted_price'];
        $deliveryCost = $validated['delivery_cost'] ?? 0;
        $additionalFeesTotal = 0;

        if (isset($validated['additional_fees']) && is_array($validated['additional_fees'])) {
            $additionalFeesTotal = array_sum(array_column($validated['additional_fees'], 'amount'));
        }

        $totalCost = $quotedPrice + $deliveryCost + $additionalFeesTotal;

        $factoryRequest->update([
            'status' => 'quoted',
            'quoted_price' => $quotedPrice,
            'delivery_cost' => $deliveryCost,
            'additional_fees' => $validated['additional_fees'] ?? null,
            'total_cost' => $totalCost,
            'quote_message' => $validated['quote_message'] ?? null,
            'quoted_at' => now(),
        ]);

        return redirect()->route('factories.quotes.show', [$factory, $factoryRequest])
            ->with('success', 'Quote submitted successfully.');
    }

    /**
     * Update delivery status for accepted orders.
     */
    public function updateDeliveryStatus(Request $request, Factory $factory, FactoryRequest $factoryRequest): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $factoryRequest->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        if ($factoryRequest->status !== 'accepted') {
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
            'delivery_notes' => $validated['delivery_notes'] ?? null,
        ];

        if ($validated['tracking_number']) {
            $updateData['tracking_number'] = $validated['tracking_number'];
        }

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
                break;
            case 'delivered':
                $updateData['delivered_at'] = now();
                break;
        }

        $factoryRequest->update($updateData);

        return redirect()->back()
            ->with('success', 'Delivery status updated successfully.');
    }
}

