<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\ProjectLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $projectLocations = ProjectLocation::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('contractor.project-locations.index', compact('projectLocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('contractor.project-locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['country'] = $validated['country'] ?? 'Indonesia';

        ProjectLocation::create($validated);

        return redirect()->route('contractor.project-locations.index')
            ->with('success', 'Project location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectLocation $projectLocation): View
    {
        $this->authorize('view', $projectLocation);

        return view('contractor.project-locations.show', compact('projectLocation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectLocation $projectLocation): View
    {
        $this->authorize('update', $projectLocation);

        return view('contractor.project-locations.edit', compact('projectLocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectLocation $projectLocation): RedirectResponse
    {
        $this->authorize('update', $projectLocation);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $projectLocation->update($validated);

        return redirect()->route('contractor.project-locations.index')
            ->with('success', 'Project location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectLocation $projectLocation): RedirectResponse
    {
        $this->authorize('delete', $projectLocation);

        $projectLocation->delete();

        return redirect()->route('contractor.project-locations.index')
            ->with('success', 'Project location deleted successfully.');
    }
}
