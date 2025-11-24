<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display notification center.
     */
    public function index(Request $request): View
    {
        $query = Auth::user()->notifications()->latest();

        // Filter by read/unread
        if ($request->has('filter') && $request->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->has('filter') && $request->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(string $id): JsonResponse|RedirectResponse
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Delete notification.
     */
    public function destroy(string $id): RedirectResponse
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();
        }

        return redirect()->back()->with('success', 'Notifikasi dihapus.');
    }

    /**
     * Show notification preferences.
     */
    public function preferences(): View
    {
        $preferences = NotificationPreference::where('user_id', Auth::id())
            ->get()
            ->keyBy('type');

        $notificationTypes = [
            'message' => 'Pesan',
            'order' => 'Pesanan',
            'product_approved' => 'Persetujuan Produk',
            'service_approved' => 'Persetujuan Jasa',
            'payment' => 'Pembayaran',
            'balance_topup' => 'Top-up Saldo',
            'quote' => 'Quote Request',
        ];

        return view('notifications.preferences', compact('preferences', 'notificationTypes'));
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'preferences' => 'required|array',
            'preferences.*.email_enabled' => 'boolean',
            'preferences.*.database_enabled' => 'boolean',
        ]);

        foreach ($request->preferences as $type => $settings) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'type' => $type,
                ],
                [
                    'email_enabled' => $settings['email_enabled'] ?? false,
                    'database_enabled' => $settings['database_enabled'] ?? false,
                ]
            );
        }

        return redirect()->back()->with('success', 'Preferensi notifikasi berhasil diperbarui.');
    }

    /**
     * Get unread notifications count (AJAX).
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
