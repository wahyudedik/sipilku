<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MaterialRequestComparisonController extends Controller
{
    /**
     * Display quote comparison page for material requests.
     */
    public function compare(Request $request): View
    {
        $requestGroupId = $request->get('request_group_id');

        if (!$requestGroupId) {
            return redirect()->route('contractor.material-requests.index')
                ->with('error', 'Request group ID is required.');
        }

        // Get all material requests in the same group
        $materialRequests = MaterialRequest::where('request_group_id', $requestGroupId)
            ->where('user_id', Auth::id())
            ->with(['store', 'store.locations', 'projectLocation'])
            ->get();

        if ($materialRequests->isEmpty()) {
            return redirect()->route('contractor.material-requests.index')
                ->with('error', 'No material requests found for comparison.');
        }

        // Group by status
        $pending = $materialRequests->where('status', 'pending');
        $quoted = $materialRequests->where('status', 'quoted');
        $accepted = $materialRequests->where('status', 'accepted');
        $rejected = $materialRequests->where('status', 'rejected');

        // Sort quoted requests by price
        $quotedSorted = $quoted->sortBy('quoted_price');

        return view('contractor.material-requests.compare', compact(
            'materialRequests',
            'pending',
            'quoted',
            'quotedSorted',
            'accepted',
            'rejected',
            'requestGroupId'
        ));
    }
}

