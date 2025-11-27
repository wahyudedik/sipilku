<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\ProjectLocation;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MaterialRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $materialRequests = MaterialRequest::where('user_id', Auth::id())
            ->with(['store', 'projectLocation'])
            ->latest()
            ->paginate(15);

        // Get request groups for comparison links
        $requestGroups = MaterialRequest::where('user_id', Auth::id())
            ->whereNotNull('request_group_id')
            ->select('request_group_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('request_group_id')
            ->having('count', '>', 1)
            ->pluck('request_group_id');

        return view('contractor.material-requests.index', compact('materialRequests', 'requestGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $storeId = $request->get('store');
        $projectLocationId = $request->get('project_location');

        $stores = Store::where('is_active', true)
            ->where('is_verified', true)
            ->where('status', 'approved')
            ->get();

        $projectLocations = ProjectLocation::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        $selectedStore = $storeId ? Store::where('uuid', $storeId)->first() : null;
        $selectedProjectLocation = $projectLocationId ? ProjectLocation::where('uuid', $projectLocationId)->first() : null;

        return view('contractor.material-requests.create', compact(
            'stores',
            'projectLocations',
            'selectedStore',
            'selectedProjectLocation'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * Supports requesting from multiple stores.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_ids' => ['required', 'array', 'min:1'],
            'store_ids.*' => ['required', 'exists:stores,uuid'],
            'project_location_id' => ['nullable', 'exists:project_locations,uuid'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:1000'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'deadline' => ['nullable', 'date', 'after:today'],
        ]);

        // Generate a request group ID for grouping multiple store requests
        $requestGroupId = \Illuminate\Support\Str::uuid()->toString();

        $createdRequests = [];

        // Create a request for each selected store
        foreach ($validated['store_ids'] as $storeId) {
            $requestData = [
                'user_id' => Auth::id(),
                'store_id' => $storeId,
                'project_location_id' => $validated['project_location_id'] ?? null,
                'items' => $validated['items'],
                'message' => $validated['message'] ?? null,
                'budget' => $validated['budget'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'status' => 'pending',
                'request_group_id' => $requestGroupId,
            ];

            $createdRequests[] = MaterialRequest::create($requestData);
        }

        $storeCount = count($createdRequests);
        $message = $storeCount === 1 
            ? 'Material request created successfully.'
            : "Material request sent to {$storeCount} stores successfully.";

        return redirect()->route('contractor.material-requests.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialRequest $materialRequest): View
    {
        $this->authorize('view', $materialRequest);

        $materialRequest->load(['store', 'store.locations', 'projectLocation']);

        return view('contractor.material-requests.show', compact('materialRequest'));
    }

    /**
     * Accept a quoted material request.
     */
    public function accept(MaterialRequest $materialRequest): RedirectResponse
    {
        $this->authorize('update', $materialRequest);

        if ($materialRequest->status !== 'quoted') {
            return redirect()->back()
                ->with('error', 'Only quoted requests can be accepted.');
        }

        $materialRequest->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect()->route('contractor.material-requests.show', $materialRequest)
            ->with('success', 'Material request accepted successfully.');
    }

    /**
     * Reject a quoted material request.
     */
    public function reject(Request $request, MaterialRequest $materialRequest): RedirectResponse
    {
        $this->authorize('update', $materialRequest);

        if ($materialRequest->status !== 'quoted') {
            return redirect()->back()
                ->with('error', 'Only quoted requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $materialRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('contractor.material-requests.show', $materialRequest)
            ->with('success', 'Material request rejected.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialRequest $materialRequest): RedirectResponse
    {
        $this->authorize('delete', $materialRequest);

        if (!in_array($materialRequest->status, ['pending', 'rejected', 'cancelled'])) {
            return redirect()->back()
                ->with('error', 'Cannot delete request with current status.');
        }

        $materialRequest->delete();

        return redirect()->route('contractor.material-requests.index')
            ->with('success', 'Material request deleted successfully.');
    }
}
