<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FactoryPricingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display pricing management page.
     */
    public function index(Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('factories.pricing.index', compact('factory'));
    }

    /**
     * Update factory pricing.
     */
    public function update(Request $request, Factory $factory): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'delivery_price_per_km' => ['required', 'numeric', 'min:0'],
            'max_delivery_distance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $factory->update($validated);

        return redirect()->route('factories.pricing.index', $factory)
            ->with('success', 'Pricing updated successfully.');
    }
}

