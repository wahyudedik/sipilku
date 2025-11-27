<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFactoryTypeRequest;
use App\Http\Requests\Admin\UpdateFactoryTypeRequest;
use App\Models\FactoryType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FactoryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = FactoryType::query();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $factoryTypes = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.factory-types.index', compact('factoryTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.factory-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFactoryTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('factory-types', 'public');
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('factory-types/icons', 'public');
        }

        FactoryType::create($data);

        return redirect()->route('admin.factory-types.index')
            ->with('success', 'Tipe pabrik berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FactoryType $factoryType): View
    {
        $factoryType->load(['factories']);
        return view('admin.factory-types.show', compact('factoryType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FactoryType $factoryType): View
    {
        return view('admin.factory-types.edit', compact('factoryType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFactoryTypeRequest $request, FactoryType $factoryType): RedirectResponse
    {
        $data = $request->validated();
        
        if ($request->has('name') && $data['name'] !== $factoryType->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($factoryType->image) {
                Storage::disk('public')->delete($factoryType->image);
            }
            $data['image'] = $request->file('image')->store('factory-types', 'public');
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($factoryType->icon) {
                Storage::disk('public')->delete($factoryType->icon);
            }
            $data['icon'] = $request->file('icon')->store('factory-types/icons', 'public');
        }

        $factoryType->update($data);

        return redirect()->route('admin.factory-types.index')
            ->with('success', 'Tipe pabrik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FactoryType $factoryType): RedirectResponse
    {
        // Check if factory type has factories
        if ($factoryType->factories()->count() > 0) {
            return redirect()->route('admin.factory-types.index')
                ->with('error', 'Tipe pabrik tidak dapat dihapus karena masih memiliki pabrik.');
        }

        // Delete images
        if ($factoryType->image) {
            Storage::disk('public')->delete($factoryType->image);
        }
        if ($factoryType->icon) {
            Storage::disk('public')->delete($factoryType->icon);
        }

        $factoryType->delete();

        return redirect()->route('admin.factory-types.index')
            ->with('success', 'Tipe pabrik berhasil dihapus.');
    }
}
