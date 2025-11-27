<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveStoreRequest;
use App\Http\Requests\RejectStoreRequest;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Display a listing of stores for approval.
     */
    public function index(Request $request): View
    {
        $query = Store::with(['user', 'locations'])->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by verification
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified === '1');
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $stores = $query->paginate(15)->withQueryString();

        return view('admin.stores.index', compact('stores'));
    }

    /**
     * Display the specified store.
     */
    public function show(Store $store): View
    {
        $store->load([
            'user',
            'locations',
            'products',
            'reviews.user'
        ]);

        return view('admin.stores.show', compact('store'));
    }

    /**
     * Approve a store.
     */
    public function approve(ApproveStoreRequest $request, Store $store): RedirectResponse
    {
        $store->update([
            'status' => 'approved',
            'is_verified' => true,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Toko berhasil disetujui.');
    }

    /**
     * Reject a store.
     */
    public function reject(RejectStoreRequest $request, Store $store): RedirectResponse
    {
        $store->update([
            'status' => 'rejected',
            'is_verified' => false,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Toko berhasil ditolak.');
    }

    /**
     * Suspend a store.
     */
    public function suspend(Request $request, Store $store): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $store->update([
            'status' => 'suspended',
            'is_active' => false,
            'rejection_reason' => $request->reason,
        ]);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Toko berhasil ditangguhkan.');
    }

    /**
     * Activate a store.
     */
    public function activate(Store $store): RedirectResponse
    {
        $store->update([
            'status' => 'approved',
            'is_active' => true,
        ]);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Toko berhasil diaktifkan.');
    }

    /**
     * Bulk actions for stores.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:approve,reject,suspend,activate'],
            'store_ids' => ['required', 'array', 'min:1'],
            'store_ids.*' => ['exists:stores,uuid'],
            'rejection_reason' => ['required_if:action,reject', 'required_if:action,suspend', 'string', 'max:500'],
        ]);

        $storeIds = $request->store_ids;
        $action = $request->action;
        $count = 0;

        foreach ($storeIds as $storeId) {
            $store = Store::where('uuid', $storeId)->first();
            if (!$store) continue;

            switch ($action) {
                case 'approve':
                    $store->update([
                        'status' => 'approved',
                        'is_verified' => true,
                        'approved_at' => now(),
                        'rejection_reason' => null,
                    ]);
                    $count++;
                    break;

                case 'reject':
                    $store->update([
                        'status' => 'rejected',
                        'is_verified' => false,
                        'rejection_reason' => $request->rejection_reason,
                    ]);
                    $count++;
                    break;

                case 'suspend':
                    $store->update([
                        'status' => 'suspended',
                        'is_active' => false,
                        'rejection_reason' => $request->rejection_reason,
                    ]);
                    $count++;
                    break;

                case 'activate':
                    $store->update([
                        'status' => 'approved',
                        'is_active' => true,
                    ]);
                    $count++;
                    break;
            }
        }

        $messages = [
            'approve' => "{$count} toko berhasil disetujui.",
            'reject' => "{$count} toko berhasil ditolak.",
            'suspend' => "{$count} toko berhasil ditangguhkan.",
            'activate' => "{$count} toko berhasil diaktifkan.",
        ];

        return redirect()->route('admin.stores.index')
            ->with('success', $messages[$action] ?? 'Aksi berhasil dilakukan.');
    }

    /**
     * Remove the specified store from storage.
     */
    public function destroy(Store $store): RedirectResponse
    {
        // Delete related files
        if ($store->logo) {
            \Storage::disk('public')->delete($store->logo);
        }
        if ($store->banner) {
            \Storage::disk('public')->delete($store->banner);
        }

        $store->forceDelete();

        return redirect()->route('admin.stores.index')
            ->with('success', 'Toko berhasil dihapus permanen.');
    }
}
