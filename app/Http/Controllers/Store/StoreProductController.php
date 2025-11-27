<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\BulkImportRequest;
use App\Http\Requests\Store\StoreProductRequest;
use App\Http\Requests\Store\UpdateStoreProductRequest;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\StoreCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class StoreProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'store.owner']);
    }

    /**
     * Display a listing of products for a store.
     */
    public function index(Request $request, Store $store): View
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $query = $store->products()->with('category');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('store_category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            match($request->status) {
                'active' => $query->where('is_active', true),
                'inactive' => $query->where('is_active', false),
                'out_of_stock' => $query->where('stock', '<=', 0),
                'low_stock' => $query->where('stock', '>', 0)->where('stock', '<=', 10),
                default => null,
            };
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = StoreCategory::where('is_active', true)->orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => $store->products()->count(),
            'active' => $store->products()->where('is_active', true)->count(),
            'inactive' => $store->products()->where('is_active', false)->count(),
            'out_of_stock' => $store->products()->where('stock', '<=', 0)->count(),
            'low_stock' => $store->products()->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
        ];

        return view('stores.products.index', compact('store', 'products', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Store $store): View
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $categories = StoreCategory::where('is_active', true)->orderBy('name')->get();
        return view('stores.products.create', compact('store', 'categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request, Store $store): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();
        $data['store_id'] = $store->id;

        // Generate slug
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);

        // Handle images upload
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('stores/products', 'public');
            }
            $data['images'] = $imagePaths;
        }

        // Handle specifications
        if ($request->has('spec_key') && $request->has('spec_value')) {
            $specs = [];
            $keys = $request->get('spec_key', []);
            $values = $request->get('spec_value', []);
            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index])) {
                    $specs[$key] = $values[$index];
                }
            }
            $data['specifications'] = !empty($specs) ? $specs : null;
        }

        // Set defaults
        $data['stock'] = $data['stock'] ?? 0;
        $data['min_order'] = $data['min_order'] ?? 1;
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        StoreProduct::create($data);

        return redirect()->route('stores.products.index', $store)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified product.
     */
    public function show(Store $store, StoreProduct $product): View
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id || $product->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        $product->load('category');
        return view('stores.products.show', compact('store', 'product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Store $store, StoreProduct $product): View
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id || $product->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        $categories = StoreCategory::where('is_active', true)->orderBy('name')->get();
        return view('stores.products.edit', compact('store', 'product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateStoreProductRequest $request, Store $store, StoreProduct $product): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id || $product->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        // Handle images
        $existingImages = $request->get('existing_images', []);
        $newImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newImages[] = $image->store('stores/products', 'public');
            }
        }

        // Merge existing and new images
        $data['images'] = array_merge($existingImages, $newImages);

        // Delete removed images
        if ($product->images) {
            foreach ($product->images as $oldImage) {
                if (!in_array($oldImage, $data['images'])) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        }

        // Handle specifications
        if ($request->has('spec_key') && $request->has('spec_value')) {
            $specs = [];
            $keys = $request->get('spec_key', []);
            $values = $request->get('spec_value', []);
            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index])) {
                    $specs[$key] = $values[$index];
                }
            }
            $data['specifications'] = !empty($specs) ? $specs : null;
        }

        // Update slug if name changed
        if ($data['name'] !== $product->name) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        }

        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $product->update($data);

        return redirect()->route('stores.products.index', $store)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Store $store, StoreProduct $product): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id || $product->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('stores.products.index', $store)
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Bulk update stock.
     */
    public function bulkUpdateStock(Request $request, Store $store): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:store_products,uuid'],
            'products.*.stock' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->products as $productData) {
            $product = StoreProduct::where('uuid', $productData['id'])
                ->where('store_id', $store->id)
                ->first();

            if ($product) {
                $product->update(['stock' => $productData['stock']]);
            }
        }

        return redirect()->route('stores.products.index', $store)
            ->with('success', 'Stok produk berhasil diperbarui.');
    }

    /**
     * Bulk import products from Excel/CSV.
     */
    public function bulkImport(BulkImportRequest $request, Store $store): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            if ($extension === 'csv') {
                $this->importFromCSV($file, $store);
            } else {
                $this->importFromExcel($file, $store);
            }

            return redirect()->route('stores.products.index', $store)
                ->with('success', 'Produk berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->route('stores.products.index', $store)
                ->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }
    }

    /**
     * Import from CSV file.
     */
    private function importFromCSV($file, Store $store): void
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Get header row
        
        if (!$header) {
            fclose($handle);
            throw new \Exception('File CSV tidak valid atau kosong.');
        }

        // Normalize header (trim and lowercase)
        $header = array_map(function($h) {
            return trim(strtolower($h));
        }, $header);

        $imported = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 1) continue; // Skip empty rows

            // Combine header with row data
            $data = [];
            foreach ($header as $index => $key) {
                $data[$key] = isset($row[$index]) ? trim($row[$index]) : '';
            }

            // Skip if name is empty
            if (empty($data['name'])) continue;
            
            try {
                StoreProduct::create([
                    'store_id' => $store->id,
                    'name' => $data['name'] ?? '',
                    'slug' => Str::slug($data['name'] ?? '') . '-' . Str::random(6),
                    'description' => $data['description'] ?? null,
                    'sku' => !empty($data['sku']) ? $data['sku'] : null,
                    'brand' => !empty($data['brand']) ? $data['brand'] : null,
                    'price' => !empty($data['price']) ? (float)$data['price'] : 0,
                    'discount_price' => !empty($data['discount_price']) ? (float)$data['discount_price'] : null,
                    'unit' => !empty($data['unit']) ? $data['unit'] : 'pcs',
                    'stock' => isset($data['stock']) && $data['stock'] !== '' ? (int)$data['stock'] : 0,
                    'min_order' => !empty($data['min_order']) ? (int)$data['min_order'] : 1,
                    'is_active' => isset($data['is_active']) ? (in_array(strtolower($data['is_active']), ['1', 'true', 'yes', 'aktif'])) : true,
                ]);

                $imported++;
            } catch (\Exception $e) {
                // Log error but continue with next row
                \Log::warning('Failed to import product row: ' . $e->getMessage());
            }
        }

        fclose($handle);

        if ($imported === 0) {
            throw new \Exception('Tidak ada produk yang berhasil diimpor. Pastikan format file benar.');
        }
    }

    /**
     * Import from Excel file.
     */
    private function importFromExcel($file, Store $store): void
    {
        // This would require Maatwebsite\Excel package
        // For now, we'll use a simple CSV-like approach
        $this->importFromCSV($file, $store);
    }

    /**
     * Update product availability status.
     */
    public function updateAvailability(Request $request, Store $store, StoreProduct $product): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $store->user_id || $product->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $product->update(['is_active' => $request->is_active]);

        return redirect()->back()
            ->with('success', 'Status ketersediaan produk berhasil diperbarui.');
    }
}
