<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveProductRequest;
use App\Http\Requests\RejectProductRequest;
use App\Models\Product;
use App\Notifications\ProductApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['user', 'category'])->latest();

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

        $products = $query->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load(['user', 'category', 'reviews.user']);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Approve a product.
     */
    public function approve(ApproveProductRequest $request, Product $product): RedirectResponse
    {
        $product->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        // Send notification to seller
        $product->user->notify(new ProductApprovedNotification($product, true));

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil disetujui.');
    }

    /**
     * Reject a product.
     */
    public function reject(RejectProductRequest $request, Product $product): RedirectResponse
    {
        $product->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Send notification to seller
        $product->user->notify(new ProductApprovedNotification($product, false));

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditolak.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Delete files
        if ($product->preview_image) {
            \Storage::disk('public')->delete($product->preview_image);
        }
        if ($product->gallery_images) {
            foreach ($product->gallery_images as $image) {
                \Storage::disk('public')->delete($image);
            }
        }
        if ($product->file_path) {
            \Storage::disk('public')->delete($product->file_path);
        }

        $product->forceDelete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus permanen.');
    }
}
