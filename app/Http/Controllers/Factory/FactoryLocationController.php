<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\FactoryLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FactoryLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of factory locations.
     */
    public function index(Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $locations = $factory->locations()->latest()->get();
        return view('factories.locations.index', compact('factory', 'locations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('factories.locations.create', compact('factory'));
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request, Factory $factory): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'operating_hours' => ['nullable', 'array'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $validated['factory_id'] = $factory->uuid;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['country'] = $validated['country'] ?? 'Indonesia';

        // If this is set as primary, unset other primary locations
        if ($validated['is_primary'] ?? false) {
            $factory->locations()->update(['is_primary' => false]);
        }

        FactoryLocation::create($validated);

        return redirect()->route('factories.locations.index', $factory)
            ->with('success', 'Lokasi pabrik berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Factory $factory, FactoryLocation $location): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $location->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        return view('factories.locations.edit', compact('factory', 'location'));
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, Factory $factory, FactoryLocation $location): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $location->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'operating_hours' => ['nullable', 'array'],
            'is_primary' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // If this is set as primary, unset other primary locations
        if ($validated['is_primary'] ?? false) {
            $factory->locations()->where('uuid', '!=', $location->uuid)->update(['is_primary' => false]);
        }

        $location->update($validated);

        return redirect()->route('factories.locations.index', $factory)
            ->with('success', 'Lokasi pabrik berhasil diperbarui.');
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Factory $factory, FactoryLocation $location): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $location->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent deleting if it's the only location
        if ($factory->locations()->count() <= 1) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus lokasi terakhir.');
        }

        $location->delete();

        return redirect()->route('factories.locations.index', $factory)
            ->with('success', 'Lokasi pabrik berhasil dihapus.');
    }
}

