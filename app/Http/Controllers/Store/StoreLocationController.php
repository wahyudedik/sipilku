<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StoreLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of store locations.
     */
    public function index(Store $store): View
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $locations = $store->locations()->latest()->get();
        return view('stores.locations.index', compact('store', 'locations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(Store $store): View
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('stores.locations.create', compact('store'));
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request, Store $store): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:255', 'default:Indonesia'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $validated['store_id'] = $store->id;
        $validated['country'] = $validated['country'] ?? 'Indonesia';

        // If this is set as primary, unset others
        if ($validated['is_primary'] ?? false) {
            $store->locations()->update(['is_primary' => false]);
        }

        StoreLocation::create($validated);

        return redirect()->route('stores.locations.index', $store)
            ->with('success', 'Lokasi toko berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Store $store, StoreLocation $location): View
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id || $location->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('stores.locations.edit', compact('store', 'location'));
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, Store $store, StoreLocation $location): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id || $location->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        // If this is set as primary, unset others
        if ($validated['is_primary'] ?? false) {
            $store->locations()->where('id', '!=', $location->id)->update(['is_primary' => false]);
        }

        $location->update($validated);

        return redirect()->route('stores.locations.index', $store)
            ->with('success', 'Lokasi toko berhasil diperbarui.');
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Store $store, StoreLocation $location): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id || $location->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        $location->delete();

        return redirect()->route('stores.locations.index', $store)
            ->with('success', 'Lokasi toko berhasil dihapus.');
    }
}
