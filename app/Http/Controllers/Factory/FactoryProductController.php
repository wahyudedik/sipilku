<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Factory\BulkImportFactoryProductRequest;
use App\Http\Requests\Factory\FactoryProductRequest;
use App\Http\Requests\Factory\UpdateFactoryProductRequest;
use App\Models\Factory;
use App\Models\FactoryProduct;
use App\Services\FactoryTypeProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FactoryProductController extends Controller
{
    protected FactoryTypeProductService $typeService;

    public function __construct(FactoryTypeProductService $typeService)
    {
        $this->middleware(['auth', 'verified']);
        $this->typeService = $typeService;
    }

    /**
     * Display a listing of products for a factory.
     */
    public function index(Request $request, Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $query = $factory->products();

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            match($request->status) {
                'available' => $query->where('is_available', true),
                'unavailable' => $query->where('is_available', false),
                'out_of_stock' => $query->where(function($q) {
                    $q->whereNotNull('stock')->where('stock', '<=', 0);
                }),
                'low_stock' => $query->where(function($q) {
                    $q->whereNotNull('stock')->where('stock', '>', 0)->where('stock', '<=', 10);
                }),
                'in_stock' => $query->where(function($q) {
                    $q->whereNull('stock')->orWhere('stock', '>', 0);
                }),
                default => null,
            };
        }

        // Filter by unit
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        // Filter by product category
        if ($request->filled('product_category')) {
            $query->where('product_category', $request->product_category);
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => $factory->products()->count(),
            'available' => $factory->products()->where('is_available', true)->count(),
            'unavailable' => $factory->products()->where('is_available', false)->count(),
            'out_of_stock' => $factory->products()->whereNotNull('stock')->where('stock', '<=', 0)->count(),
            'low_stock' => $factory->products()->whereNotNull('stock')->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'featured' => $factory->products()->where('is_featured', true)->count(),
        ];

        // Available units for filter
        $availableUnits = FactoryProduct::where('factory_id', $factory->uuid)
            ->distinct()
            ->pluck('unit')
            ->filter()
            ->sort()
            ->values();

        return view('factories.products.index', compact('factory', 'products', 'stats', 'availableUnits'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Factory $factory): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Get factory type to suggest quality grades
        $factoryType = $factory->factoryType;
        $typeSlug = $factoryType->slug ?? '';
        $typeConfig = $this->typeService->getConfig($typeSlug);
        $productCategories = $this->typeService->getProductCategories($typeSlug);
        $defaultUnits = $this->typeService->getDefaultUnits($typeSlug);
        $specificationsTemplate = $this->typeService->getSpecificationsTemplate($typeSlug);

        return view('factories.products.create', compact(
            'factory', 
            'factoryType', 
            'typeConfig',
            'productCategories',
            'defaultUnits',
            'specificationsTemplate'
        ));
    }

    /**
     * Store a newly created product.
     */
    public function store(FactoryProductRequest $request, Factory $factory): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();
        $data['factory_id'] = $factory->uuid;

        // Generate slug
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);

        // Handle images upload
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('factories/products', 'public');
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

        // Handle quality grade
        if ($request->has('quality_grade')) {
            $qualityGrade = [];
            if ($request->filled('quality_grade.grade')) {
                $qualityGrade['grade'] = $request->input('quality_grade.grade');
            }
            if ($request->filled('quality_grade.value')) {
                $qualityGrade['value'] = $request->input('quality_grade.value');
            }
            if ($request->filled('quality_grade.description')) {
                $qualityGrade['description'] = $request->input('quality_grade.description');
            }
            $data['quality_grade'] = !empty($qualityGrade) ? $qualityGrade : null;
        }

        // Handle available units
        if ($request->has('available_units') && is_array($request->available_units)) {
            $data['available_units'] = array_filter($request->available_units);
        }

        // Set defaults
        $data['stock'] = $request->filled('stock') ? (int)$data['stock'] : null; // null = unlimited
        $data['min_order'] = $data['min_order'] ?? 1;
        $data['is_available'] = $request->has('is_available');
        $data['is_featured'] = $request->has('is_featured');

        FactoryProduct::create($data);

        return redirect()->route('factories.products.index', $factory)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified product.
     */
    public function show(Factory $factory, FactoryProduct $product): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $product->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        return view('factories.products.show', compact('factory', 'product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Factory $factory, FactoryProduct $product): View
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $product->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        $factoryType = $factory->factoryType;
        $typeSlug = $factoryType->slug ?? '';
        $typeConfig = $this->typeService->getConfig($typeSlug);
        $productCategories = $this->typeService->getProductCategories($typeSlug);
        $defaultUnits = $this->typeService->getDefaultUnits($typeSlug);
        $specificationsTemplate = $this->typeService->getSpecificationsTemplate($typeSlug);

        return view('factories.products.edit', compact(
            'factory', 
            'product', 
            'factoryType', 
            'typeConfig',
            'productCategories',
            'defaultUnits',
            'specificationsTemplate'
        ));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateFactoryProductRequest $request, Factory $factory, FactoryProduct $product): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $product->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        // Handle images upload
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('factories/products', 'public');
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

        // Handle quality grade
        if ($request->has('quality_grade')) {
            $qualityGrade = [];
            if ($request->filled('quality_grade.grade')) {
                $qualityGrade['grade'] = $request->input('quality_grade.grade');
            }
            if ($request->filled('quality_grade.value')) {
                $qualityGrade['value'] = $request->input('quality_grade.value');
            }
            if ($request->filled('quality_grade.description')) {
                $qualityGrade['description'] = $request->input('quality_grade.description');
            }
            $data['quality_grade'] = !empty($qualityGrade) ? $qualityGrade : null;
        }

        // Handle available units
        if ($request->has('available_units') && is_array($request->available_units)) {
            $data['available_units'] = array_filter($request->available_units);
        }

        // Update slug if name changed
        if ($data['name'] !== $product->name) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        }

        $data['stock'] = $request->filled('stock') ? (int)$data['stock'] : null;
        $data['is_available'] = $request->has('is_available');
        $data['is_featured'] = $request->has('is_featured');

        $product->update($data);

        return redirect()->route('factories.products.index', $factory)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Factory $factory, FactoryProduct $product): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $product->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        // Delete images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('factories.products.index', $factory)
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Bulk import products from Excel/CSV.
     */
    public function bulkImport(BulkImportFactoryProductRequest $request, Factory $factory): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            if ($extension === 'csv') {
                $this->importFromCSV($file, $factory);
            } else {
                $this->importFromExcel($file, $factory);
            }

            return redirect()->route('factories.products.index', $factory)
                ->with('success', 'Produk berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->route('factories.products.index', $factory)
                ->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }
    }

    /**
     * Import from CSV file.
     */
    private function importFromCSV($file, Factory $factory): void
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
                // Parse specifications if provided
                $specifications = null;
                if (!empty($data['specifications'])) {
                    $specs = json_decode($data['specifications'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $specifications = $specs;
                    }
                }

                // Parse quality grade if provided
                $qualityGrade = null;
                if (!empty($data['quality_grade'])) {
                    $grade = json_decode($data['quality_grade'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $qualityGrade = $grade;
                    }
                }

                // Parse available units
                $availableUnits = null;
                if (!empty($data['available_units'])) {
                    $units = explode(',', $data['available_units']);
                    $availableUnits = array_map('trim', $units);
                }

                FactoryProduct::create([
                    'factory_id' => $factory->uuid,
                    'name' => $data['name'] ?? '',
                    'slug' => Str::slug($data['name'] ?? '') . '-' . Str::random(6),
                    'description' => $data['description'] ?? null,
                    'sku' => !empty($data['sku']) ? $data['sku'] : null,
                    'code' => !empty($data['code']) ? $data['code'] : null,
                    'price' => !empty($data['price']) ? (float)$data['price'] : 0,
                    'discount_price' => !empty($data['discount_price']) ? (float)$data['discount_price'] : null,
                    'unit' => !empty($data['unit']) ? $data['unit'] : 'pcs',
                    'available_units' => $availableUnits,
                    'specifications' => $specifications,
                    'quality_grade' => $qualityGrade,
                    'stock' => isset($data['stock']) && $data['stock'] !== '' ? (int)$data['stock'] : null,
                    'min_order' => !empty($data['min_order']) ? (int)$data['min_order'] : 1,
                    'is_available' => isset($data['is_available']) ? (in_array(strtolower($data['is_available']), ['1', 'true', 'yes', 'aktif'])) : true,
                    'is_featured' => isset($data['is_featured']) ? (in_array(strtolower($data['is_featured']), ['1', 'true', 'yes'])) : false,
                ]);

                $imported++;
            } catch (\Exception $e) {
                // Log error but continue with next row
                \Log::warning('Failed to import factory product row: ' . $e->getMessage());
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
    private function importFromExcel($file, Factory $factory): void
    {
        // For Excel files, we'll use a similar approach to CSV
        // In production, you might want to use Maatwebsite\Excel package
        $this->importFromCSV($file, $factory);
    }

    /**
     * Update product availability status.
     */
    public function updateAvailability(Request $request, Factory $factory, FactoryProduct $product): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id || $product->factory_id !== $factory->uuid) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'is_available' => ['required', 'boolean'],
        ]);

        $product->update(['is_available' => $request->is_available]);

        return redirect()->back()
            ->with('success', 'Status ketersediaan produk berhasil diperbarui.');
    }

    /**
     * Bulk update stock.
     */
    public function bulkUpdateStock(Request $request, Factory $factory): RedirectResponse
    {
        // Verify ownership
        if (Auth::id() !== $factory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:factory_products,uuid'],
            'products.*.stock' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($request->products as $productData) {
            $product = FactoryProduct::where('uuid', $productData['id'])
                ->where('factory_id', $factory->uuid)
                ->first();

            if ($product) {
                $stock = isset($productData['stock']) && $productData['stock'] !== '' 
                    ? (int)$productData['stock'] 
                    : null;
                $product->update(['stock' => $stock]);
            }
        }

        return redirect()->route('factories.products.index', $factory)
            ->with('success', 'Stok produk berhasil diperbarui.');
    }

    /**
     * Get quality grade options based on factory type.
     * @deprecated Use FactoryTypeProductService instead
     */
    private function getQualityGradeOptions($factoryType): array
    {
        if (!$factoryType) {
            return [];
        }

        $typeSlug = $factoryType->slug ?? '';
        $config = $this->typeService->getConfig($typeSlug);
        
        return $config['quality_grades'] ?? [];
    }
}

