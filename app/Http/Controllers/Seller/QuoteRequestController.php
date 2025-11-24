<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\RespondQuoteRequest;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    /**
     * Display a listing of quote requests for seller's services.
     */
    public function index(Request $request): View
    {
        $query = QuoteRequest::whereHas('service', function($q) {
            $q->where('user_id', auth()->id());
        })->with(['service', 'user'])->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by service
        if ($request->has('service') && $request->service) {
            $query->where('service_id', $request->service);
        }

        $quoteRequests = $query->paginate(15)->withQueryString();
        $services = auth()->user()->services()->where('status', 'approved')->get();

        return view('seller.quote-requests.index', compact('quoteRequests', 'services'));
    }

    /**
     * Display the specified quote request.
     */
    public function show(QuoteRequest $quoteRequest): View
    {
        // Ensure this quote request belongs to seller's service
        if ($quoteRequest->service->user_id !== auth()->id()) {
            abort(403);
        }

        $quoteRequest->load(['service', 'user']);

        return view('seller.quote-requests.show', compact('quoteRequest'));
    }

    /**
     * Respond to a quote request (provide quote).
     */
    public function respond(RespondQuoteRequest $request, QuoteRequest $quoteRequest): RedirectResponse
    {
        // Ensure this quote request belongs to seller's service
        if ($quoteRequest->service->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow responding to pending requests
        if ($quoteRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Quote request sudah direspon sebelumnya.');
        }

        $quoteRequest->update([
            'quoted_price' => $request->quoted_price,
            'quote_message' => $request->quote_message,
            'status' => 'quoted',
            'quoted_at' => now(),
        ]);

        return redirect()->route('seller.quote-requests.show', $quoteRequest)
            ->with('success', 'Quote berhasil dikirim ke buyer.');
    }
}
