<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\FactoryRequest;
use App\Models\FactoryType;
use App\Models\ProjectLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FactoryRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $factoryTypeFilter = $request->get('factory_type');

        $factoryRequestsQuery = FactoryRequest::where('user_id', Auth::id())
            ->with(['factory', 'factory.factoryType', 'projectLocation']);

        if ($factoryTypeFilter) {
            $factoryRequestsQuery->whereHas('factory', function($q) use ($factoryTypeFilter) {
                $q->where('factory_type_id', $factoryTypeFilter);
            });
        }

        $factoryRequests = $factoryRequestsQuery->latest()->paginate(15);
        $factoryTypes = FactoryType::where('is_active', true)->get();

        return view('contractor.factory-requests.index', compact('factoryRequests', 'factoryTypes', 'factoryTypeFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $factoryId = $request->get('factory');
        $factoryTypeId = $request->get('factory_type');
        $projectLocationId = $request->get('project_location');

        $factoryTypes = FactoryType::where('is_active', true)->get();

        $factoriesQuery = Factory::where('is_active', true)
            ->where('is_verified', true)
            ->where('status', 'approved')
            ->with('factoryType');

        if ($factoryTypeId) {
            $factoriesQuery->where('factory_type_id', $factoryTypeId);
        }

        $factories = $factoriesQuery->get();

        $projectLocations = ProjectLocation::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        $selectedFactory = $factoryId ? Factory::where('uuid', $factoryId)->first() : null;
        $selectedProjectLocation = $projectLocationId ? ProjectLocation::where('uuid', $projectLocationId)->first() : null;

        return view('contractor.factory-requests.create', compact(
            'factories',
            'factoryTypes',
            'projectLocations',
            'selectedFactory',
            'selectedProjectLocation',
            'factoryTypeId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'factory_ids' => ['required', 'array', 'min:1'],
            'factory_ids.*' => ['required', 'exists:factories,uuid'],
            'factory_type_id' => ['nullable', 'exists:factory_types,uuid'],
            'project_location_id' => ['nullable', 'exists:project_locations,uuid'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.specifications' => ['nullable', 'array'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:1000'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'deadline' => ['nullable', 'date', 'after:today'],
        ]);

        // Generate request group ID for multiple factory requests
        $requestGroupId = \Illuminate\Support\Str::uuid()->toString();

        // Create requests for each factory
        $createdCount = 0;
        foreach ($validated['factory_ids'] as $factoryId) {
            FactoryRequest::create([
                'user_id' => Auth::id(),
                'request_group_id' => $requestGroupId,
                'factory_id' => $factoryId,
                'factory_type_id' => $validated['factory_type_id'] ?? null,
                'project_location_id' => $validated['project_location_id'] ?? null,
                'items' => $validated['items'],
                'message' => $validated['message'] ?? null,
                'budget' => $validated['budget'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'status' => 'pending',
            ]);
            $createdCount++;
        }

        $message = $createdCount > 1 
            ? "Quote requests sent to {$createdCount} factories. You can compare quotes once they respond."
            : 'Factory request created successfully.';

        return redirect()->route('contractor.factory-requests.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(FactoryRequest $factoryRequest): View
    {
        $this->authorize('view', $factoryRequest);

        $factoryRequest->load([
            'factory',
            'factory.factoryType',
            'factory.locations',
            'projectLocation'
        ]);

        return view('contractor.factory-requests.show', compact('factoryRequest'));
    }

    /**
     * Accept a quoted factory request.
     */
    public function accept(FactoryRequest $factoryRequest): RedirectResponse
    {
        $this->authorize('update', $factoryRequest);

        if ($factoryRequest->status !== 'quoted') {
            return redirect()->back()
                ->with('error', 'Only quoted requests can be accepted.');
        }

        $factoryRequest->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect()->route('contractor.factory-requests.show', $factoryRequest)
            ->with('success', 'Factory request accepted successfully.');
    }

    /**
     * Reject a quoted factory request.
     */
    public function reject(Request $request, FactoryRequest $factoryRequest): RedirectResponse
    {
        $this->authorize('update', $factoryRequest);

        if ($factoryRequest->status !== 'quoted') {
            return redirect()->back()
                ->with('error', 'Only quoted requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $factoryRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('contractor.factory-requests.show', $factoryRequest)
            ->with('success', 'Factory request rejected.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FactoryRequest $factoryRequest): RedirectResponse
    {
        $this->authorize('delete', $factoryRequest);

        if (!in_array($factoryRequest->status, ['pending', 'rejected', 'cancelled'])) {
            return redirect()->back()
                ->with('error', 'Cannot delete request with current status.');
        }

        $factoryRequest->delete();

        return redirect()->route('contractor.factory-requests.index')
            ->with('success', 'Factory request deleted successfully.');
    }
}
