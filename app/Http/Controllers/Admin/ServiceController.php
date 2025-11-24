<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveServiceRequest;
use App\Http\Requests\RejectServiceRequest;
use App\Models\Service;
use App\Notifications\ServiceApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Service::with(['user', 'category'])->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate(15)->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): View
    {
        $service->load(['user', 'category', 'reviews.user']);

        return view('admin.services.show', compact('service'));
    }

    /**
     * Approve a service.
     */
    public function approve(ApproveServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        // Send notification to seller
        $service->user->notify(new ServiceApprovedNotification($service, true));

        return redirect()->route('admin.services.index')
            ->with('success', 'Jasa berhasil disetujui.');
    }

    /**
     * Reject a service.
     */
    public function reject(RejectServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Send notification to seller
        $service->user->notify(new ServiceApprovedNotification($service, false));

        return redirect()->route('admin.services.index')
            ->with('success', 'Jasa berhasil ditolak.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        // Delete images
        if ($service->preview_image) {
            \Storage::disk('public')->delete($service->preview_image);
        }
        if ($service->gallery_images) {
            foreach ($service->gallery_images as $image) {
                \Storage::disk('public')->delete($image);
            }
        }
        if ($service->portfolio) {
            foreach ($service->portfolio as $portfolio) {
                if (isset($portfolio['image'])) {
                    \Storage::disk('public')->delete($portfolio['image']);
                }
            }
        }

        $service->forceDelete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Jasa berhasil dihapus permanen.');
    }
}
