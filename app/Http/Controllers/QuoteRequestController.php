<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequestRequest;
use App\Models\ProjectLocation;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Services\ServiceRequestIntegrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{

    /**
     * Show the form for creating a new quote request.
     */
    public function create(Request $request, Service $service): View
    {
        // Only allow quote requests for approved services
        if ($service->status !== 'approved') {
            abort(404);
        }

        // Don't allow seller to request quote for their own service
        if ($service->user_id === Auth::id()) {
            abort(403, 'Anda tidak dapat meminta quote untuk jasa Anda sendiri.');
        }

        $integrationService = app(ServiceRequestIntegrationService::class);
        $projectLocationId = $request->get('project_location');
        $projectLocation = $projectLocationId 
            ? ProjectLocation::where('uuid', $projectLocationId)->where('user_id', Auth::id())->first()
            : null;

        // Get recommendations
        $recommendations = $integrationService->getRecommendationsForService(
            $service,
            $projectLocation?->latitude,
            $projectLocation?->longitude,
            $projectLocation
        );

        // Get material cost estimates
        $materialEstimates = $integrationService->estimateMaterialCosts(
            $service,
            $projectLocation?->latitude,
            $projectLocation?->longitude,
            $projectLocation
        );

        // Get user's project locations
        $projectLocations = Auth::check() 
            ? ProjectLocation::where('user_id', Auth::id())->where('is_active', true)->get()
            : collect();

        return view('quote-requests.create', compact(
            'service',
            'recommendations',
            'materialEstimates',
            'projectLocations',
            'projectLocation'
        ));
    }

    /**
     * Store a newly created quote request.
     */
    public function store(StoreQuoteRequestRequest $request, Service $service): RedirectResponse
    {
        // Only allow quote requests for approved services
        if ($service->status !== 'approved') {
            abort(404);
        }

        // Don't allow seller to request quote for their own service
        if ($service->user_id === Auth::id()) {
            abort(403, 'Anda tidak dapat meminta quote untuk jasa Anda sendiri.');
        }

        QuoteRequest::create([
            'service_id' => $service->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'requirements' => $request->requirements ?? [],
            'budget' => $request->budget,
            'deadline' => $request->deadline,
            'status' => 'pending',
        ]);

        return redirect()->back()
            ->with('success', 'Permintaan quote berhasil dikirim. Seller akan menghubungi Anda segera.');
    }
}
