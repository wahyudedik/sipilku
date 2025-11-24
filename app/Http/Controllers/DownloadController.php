<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{

    /**
     * Download product file.
     */
    public function download(Order $order): StreamedResponse|RedirectResponse
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if order is completed
        if ($order->status !== 'completed') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan belum selesai. Silakan selesaikan pembayaran terlebih dahulu.');
        }

        // Check download expiration
        if ($order->download_expires_at && $order->download_expires_at->isPast()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Link download telah kedaluwarsa. Silakan hubungi admin untuk mendapatkan link baru.');
        }

        // Check download limit
        if ($order->download_count >= $order->max_downloads) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Batas download telah tercapai. Silakan hubungi admin untuk mendapatkan link baru.');
        }

        $product = $order->orderable;

        if (!$product || !$product->file_path) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'File tidak ditemukan.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($product->file_path)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'File tidak ditemukan di server.');
        }

        // Increment download count
        $order->increment('download_count');
        $product->increment('download_count');

        // Return file download
        return Storage::disk('public')->download(
            $product->file_path,
            $product->file_name ?? 'download.zip'
        );
    }

    /**
     * Download using token (for email links).
     */
    public function downloadByToken(string $token): StreamedResponse|RedirectResponse
    {
        $order = Order::where('download_token', $token)->first();

        if (!$order) {
            abort(404, 'Link download tidak valid');
        }

        // Check download expiration
        if ($order->download_expires_at && $order->download_expires_at->isPast()) {
            return redirect()->route('login')
                ->with('error', 'Link download telah kedaluwarsa. Silakan login dan cek riwayat download Anda.');
        }

        // Check download limit
        if ($order->download_count >= $order->max_downloads) {
            return redirect()->route('login')
                ->with('error', 'Batas download telah tercapai. Silakan login dan hubungi admin.');
        }

        $product = $order->orderable;

        if (!$product || !$product->file_path) {
            abort(404, 'File tidak ditemukan');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($product->file_path)) {
            abort(404, 'File tidak ditemukan di server');
        }

        // Increment download count
        $order->increment('download_count');
        $product->increment('download_count');

        // Return file download
        return Storage::disk('public')->download(
            $product->file_path,
            $product->file_name ?? 'download.zip'
        );
    }

    /**
     * Show download history.
     */
    public function history(): \Illuminate\View\View
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with(['orderable', 'orderable.category'])
            ->latest()
            ->paginate(15);

        return view('downloads.history', compact('orders'));
    }
}
