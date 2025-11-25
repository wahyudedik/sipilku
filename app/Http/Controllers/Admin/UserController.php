<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by seller status
        if ($request->has('seller_status') && $request->seller_status !== '') {
            if ($request->seller_status === 'pending') {
                $query->where('is_seller', true)->where('is_active', false);
            } elseif ($request->seller_status === 'approved') {
                $query->where('is_seller', true)->where('is_active', true);
            } elseif ($request->seller_status === 'not_seller') {
                $query->where('is_seller', false);
            }
        }

        $users = $query->with('roles')->latest()->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // Handle password update
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Update user
        $user->update($data);

        // Update roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        // Prevent deleting admin
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus user dengan role admin');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Approve seller.
     */
    public function approveSeller(User $user): RedirectResponse
    {
        if (!$user->is_seller) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User ini bukan seller');
        }

        $user->update([
            'is_active' => true,
        ]);

        // Ensure seller role is assigned
        if (!$user->hasRole('seller')) {
            $user->assignRole('seller');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Seller berhasil disetujui');
    }

    /**
     * Reject seller.
     */
    public function rejectSeller(User $user): RedirectResponse
    {
        if (!$user->is_seller) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User ini bukan seller');
        }

        $user->update([
            'is_seller' => false,
            'is_active' => false,
        ]);

        // Remove seller role
        $user->removeRole('seller');

        return redirect()->route('admin.users.index')
            ->with('success', 'Seller berhasil ditolak');
    }

    /**
     * Activate user.
     */
    public function activate(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diaktifkan');
    }

    /**
     * Deactivate user.
     */
    public function deactivate(User $user): RedirectResponse
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dinonaktifkan');
    }
}
