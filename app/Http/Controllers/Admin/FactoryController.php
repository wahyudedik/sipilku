<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveFactoryRequest;
use App\Http\Requests\RejectFactoryRequest;
use App\Models\Factory;
use App\Models\FactoryType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FactoryController extends Controller
{
    /**
     * Display a listing of factories for approval.
     */
    public function index(Request $request): View
    {
        $query = Factory::with(['user', 'factoryType', 'umkm', 'locations'])->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by factory type
        if ($request->has('factory_type') && $request->factory_type !== 'all') {
            $query->where('factory_type_id', $request->factory_type);
        }

        // Filter by verification
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified === '1');
        }

        // Filter by category (industri/umkm)
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
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
                  })
                  ->orWhereHas('factoryType', function($typeQuery) use ($search) {
                      $typeQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $factories = $query->paginate(15)->withQueryString();
        $factoryTypes = FactoryType::where('is_active', true)->get();

        return view('admin.factories.index', compact('factories', 'factoryTypes'));
    }

    /**
     * Display the specified factory.
     */
    public function show(Factory $factory): View
    {
        $factory->load([
            'user',
            'factoryType',
            'umkm',
            'locations',
            'products',
            'reviews.user'
        ]);

        return view('admin.factories.show', compact('factory'));
    }

    /**
     * Approve a factory.
     */
    public function approve(ApproveFactoryRequest $request, Factory $factory): RedirectResponse
    {
        $factory->update([
            'status' => 'approved',
            'is_verified' => true,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('admin.factories.index')
            ->with('success', 'Pabrik berhasil disetujui.');
    }

    /**
     * Reject a factory.
     */
    public function reject(RejectFactoryRequest $request, Factory $factory): RedirectResponse
    {
        $factory->update([
            'status' => 'rejected',
            'is_verified' => false,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.factories.index')
            ->with('success', 'Pabrik berhasil ditolak.');
    }

    /**
     * Suspend a factory.
     */
    public function suspend(Request $request, Factory $factory): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $factory->update([
            'status' => 'suspended',
            'is_active' => false,
            'rejection_reason' => $request->reason,
        ]);

        return redirect()->route('admin.factories.index')
            ->with('success', 'Pabrik berhasil ditangguhkan.');
    }

    /**
     * Activate a factory.
     */
    public function activate(Factory $factory): RedirectResponse
    {
        $factory->update([
            'status' => 'approved',
            'is_active' => true,
        ]);

        return redirect()->route('admin.factories.index')
            ->with('success', 'Pabrik berhasil diaktifkan.');
    }

    /**
     * Bulk actions for factories.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:approve,reject,suspend,activate'],
            'factory_ids' => ['required', 'array', 'min:1'],
            'factory_ids.*' => ['exists:factories,uuid'],
            'rejection_reason' => ['required_if:action,reject', 'required_if:action,suspend', 'string', 'max:500'],
        ]);

        $factoryIds = $request->factory_ids;
        $action = $request->action;
        $count = 0;

        foreach ($factoryIds as $factoryId) {
            $factory = Factory::where('uuid', $factoryId)->first();
            if (!$factory) continue;

            switch ($action) {
                case 'approve':
                    $factory->update([
                        'status' => 'approved',
                        'is_verified' => true,
                        'approved_at' => now(),
                        'rejection_reason' => null,
                    ]);
                    $count++;
                    break;

                case 'reject':
                    $factory->update([
                        'status' => 'rejected',
                        'is_verified' => false,
                        'rejection_reason' => $request->rejection_reason,
                    ]);
                    $count++;
                    break;

                case 'suspend':
                    $factory->update([
                        'status' => 'suspended',
                        'is_active' => false,
                        'rejection_reason' => $request->rejection_reason,
                    ]);
                    $count++;
                    break;

                case 'activate':
                    $factory->update([
                        'status' => 'approved',
                        'is_active' => true,
                    ]);
                    $count++;
                    break;
            }
        }

        $messages = [
            'approve' => "{$count} pabrik berhasil disetujui.",
            'reject' => "{$count} pabrik berhasil ditolak.",
            'suspend' => "{$count} pabrik berhasil ditangguhkan.",
            'activate' => "{$count} pabrik berhasil diaktifkan.",
        ];

        return redirect()->route('admin.factories.index')
            ->with('success', $messages[$action] ?? 'Aksi berhasil dilakukan.');
    }

    /**
     * Remove the specified factory from storage.
     */
    public function destroy(Factory $factory): RedirectResponse
    {
        // Delete related files
        if ($factory->logo) {
            \Storage::disk('public')->delete($factory->logo);
        }
        if ($factory->banner) {
            \Storage::disk('public')->delete($factory->banner);
        }

        $factory->forceDelete();

        return redirect()->route('admin.factories.index')
            ->with('success', 'Pabrik berhasil dihapus permanen.');
    }
}
