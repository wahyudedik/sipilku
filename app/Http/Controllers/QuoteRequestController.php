<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequestRequest;
use App\Models\QuoteRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{

    /**
     * Show the form for creating a new quote request.
     */
    public function create(Service $service): View
    {
        // Only allow quote requests for approved services
        if ($service->status !== 'approved') {
            abort(404);
        }

        // Don't allow seller to request quote for their own service
        if ($service->user_id === Auth::id()) {
            abort(403, 'Anda tidak dapat meminta quote untuk jasa Anda sendiri.');
        }

        return view('quote-requests.create', compact('service'));
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
