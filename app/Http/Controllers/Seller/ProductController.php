<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = auth()->user()->products()
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('seller.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)
            ->whereIn('type', ['product', 'both'])
            ->orderBy('name')
            ->get();

        return view('seller.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            $data['preview_image'] = $request->file('preview_image')->store('products/previews', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        // Handle product file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('products/files', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientMimeType();
        }

        // Set default status
        $data['status'] = 'pending';
        $data['user_id'] = auth()->id();

        $product = Product::create($data);

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil dibuat dan menunggu persetujuan admin.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        $product->load(['category', 'reviews.user']);

        return view('seller.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        $categories = Category::where('is_active', true)
            ->whereIn('type', ['product', 'both'])
            ->orderBy('name')
            ->get();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $request->validated();

        // Generate slug if changed
        if ($data['title'] !== $product->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            // Delete old image
            if ($product->preview_image) {
                Storage::disk('public')->delete($product->preview_image);
            }
            $data['preview_image'] = $request->file('preview_image')->store('products/previews', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images
            if ($product->gallery_images) {
                foreach ($product->gallery_images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        // Handle product file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($product->file_path) {
                Storage::disk('public')->delete($product->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('products/files', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientMimeType();
        }

        // Reset status to pending if product was rejected
        if ($product->status === 'rejected') {
            $data['status'] = 'pending';
            $data['rejection_reason'] = null;
        }

        $product->update($data);

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        // Delete files
        if ($product->preview_image) {
            Storage::disk('public')->delete($product->preview_image);
        }
        if ($product->gallery_images) {
            foreach ($product->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        if ($product->file_path) {
            Storage::disk('public')->delete($product->file_path);
        }

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
