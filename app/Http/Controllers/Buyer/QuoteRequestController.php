<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptQuoteRequest;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    /**
     * Display a listing of buyer's quote requests.
     */
    public function index(Request $request): View
    {
        $query = auth()->user()->quoteRequests()->with(['service.user'])->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $quoteRequests = $query->paginate(15)->withQueryString();

        return view('buyer.quote-requests.index', compact('quoteRequests'));
    }

    /**
     * Display the specified quote request.
     */
    public function show(QuoteRequest $quoteRequest): View
    {
        // Ensure this quote request belongs to the authenticated user
        if ($quoteRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $quoteRequest->load(['service.user']);

        return view('buyer.quote-requests.show', compact('quoteRequest'));
    }

    /**
     * Accept a quote.
     */
    public function accept(AcceptQuoteRequest $request, QuoteRequest $quoteRequest): RedirectResponse
    {
        // Ensure this quote request belongs to the authenticated user
        if ($quoteRequest->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow accepting quoted requests
        if ($quoteRequest->status !== 'quoted') {
            return redirect()->back()
                ->with('error', 'Quote belum tersedia atau sudah diproses.');
        }

        $quoteRequest->update([
            'status' => 'accepted',
        ]);

        // Create order from accepted quote
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'quote_request_id' => $quoteRequest->id,
            'orderable_id' => $quoteRequest->service_id,
            'orderable_type' => \App\Models\Service::class,
            'type' => 'service',
            'amount' => $quoteRequest->quoted_price,
            'total' => $quoteRequest->quoted_price,
            'status' => 'pending',
            'payment_method' => 'manual', // Default, can be changed
            'notes' => 'Order dari quote request: ' . $quoteRequest->message,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Quote diterima. Silakan lanjutkan ke proses pembayaran.');
    }

    /**
     * Reject a quote.
     */
    public function reject(QuoteRequest $quoteRequest): RedirectResponse
    {
        // Ensure this quote request belongs to the authenticated user
        if ($quoteRequest->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow rejecting quoted requests
        if ($quoteRequest->status !== 'quoted') {
            return redirect()->back()
                ->with('error', 'Quote belum tersedia atau sudah diproses.');
        }

        $quoteRequest->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('buyer.quote-requests.show', $quoteRequest)
            ->with('success', 'Quote ditolak.');
    }

    /**
     * Compare multiple quotes (quote comparison page).
     */
    public function compare(Request $request): View
    {
        $quoteIds = $request->get('quotes', []);
        
        if (empty($quoteIds)) {
            return view('buyer.quote-requests.compare', ['quotes' => collect()]);
        }

        $quotes = QuoteRequest::whereIn('id', $quoteIds)
            ->where('user_id', auth()->id())
            ->where('status', 'quoted')
            ->with(['service.user'])
            ->get();

        return view('buyer.quote-requests.compare', compact('quotes'));
    }
}
