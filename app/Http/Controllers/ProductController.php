<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'suppliers']);

        // 1. Search (Name or SKU)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // 2. Filter Category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // 3. Filter Stock Condition
        if ($stockStatus = $request->input('stock_status')) {
            if ($stockStatus === 'low') {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            } elseif ($stockStatus === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($stockStatus === 'safe') {
                $query->whereColumn('stock', '>', 'minimum_stock');
            }
        }

        // 4. Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['name', 'price', 'stock', 'created_at', 'sku'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        // 5. Pagination
        $products = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $skuCandidate = 'FL-' . strtoupper(Str::random(6));

        return view('products.create', compact('categories', 'suppliers', 'skuCandidate'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'suppliers' => ['nullable', 'array'],
            'suppliers.*' => ['exists:suppliers,id'],
            'supplier_prices' => ['nullable', 'array'],
        ], [
            'sku.unique' => 'SKU sudah digunakan oleh produk lain.',
            'price.gt' => 'Harga jual harus lebih dari 0.',
            'stock.min' => 'Stok awal tidak boleh bernilai negatif.',
            'minimum_stock.min' => 'Batas minimum stok tidak boleh negatif.',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('products', $filename, 'public');
                $imagePath = $filename;
            }

            $product = Product::create([
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'minimum_stock' => $validated['minimum_stock'],
                'image' => $imagePath,
            ]);

            // Sync suppliers
            if (!empty($validated['suppliers'])) {
                $pivotData = [];
                foreach ($validated['suppliers'] as $supplierId) {
                    $rawPrice = $request->input("supplier_prices.{$supplierId}");
                    $pivotData[$supplierId] = [
                        'supplier_price' => is_numeric($rawPrice) ? (float) $rawPrice : null,
                    ];
                }
                $product->suppliers()->sync($pivotData);
            }

            // Record initial stock movement if > 0
            if ((int) $validated['stock'] > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'IN',
                    'quantity' => (int) $validated['stock'],
                    'note' => 'Stok awal saat pendaftaran produk baru',
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', "Produk '{$product->name}' berhasil ditambahkan ke inventori.");
        });
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'suppliers', 'stockMovements' => function ($q) {
            $q->with('user.profile')->latest('created_at')->limit(10);
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $product->load('suppliers');

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'gt:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'suppliers' => ['nullable', 'array'],
            'suppliers.*' => ['exists:suppliers,id'],
            'supplier_prices' => ['nullable', 'array'],
        ], [
            'sku.unique' => 'SKU sudah digunakan oleh produk lain.',
            'price.gt' => 'Harga jual harus lebih dari 0.',
            'minimum_stock.min' => 'Batas minimum stok tidak boleh negatif.',
        ]);

        return DB::transaction(function () use ($request, $validated, $product) {
            $updateData = [
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'price' => $validated['price'],
                'minimum_stock' => $validated['minimum_stock'],
            ];

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete('products/' . $product->image);
                }
                $file = $request->file('image');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('products', $filename, 'public');
                $updateData['image'] = $filename;
            } elseif ($request->boolean('remove_image')) {
                if ($product->image) {
                    Storage::disk('public')->delete('products/' . $product->image);
                }
                $updateData['image'] = null;
            }

            $product->update($updateData);

            // Sync suppliers
            $pivotData = [];
            if (!empty($validated['suppliers'])) {
                foreach ($validated['suppliers'] as $supplierId) {
                    $rawPrice = $request->input("supplier_prices.{$supplierId}");
                    $pivotData[$supplierId] = [
                        'supplier_price' => is_numeric($rawPrice) ? (float) $rawPrice : null,
                    ];
                }
            }
            $product->suppliers()->sync($pivotData);

            return redirect()->route('products.index')
                ->with('success', "Informasi produk '{$product->name}' berhasil diperbarui.");
        });
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Business Rule: Restrict delete if product has audit logs / stock movements
        if ($product->stockMovements()->exists()) {
            return back()->with('error', "Produk '{$product->name}' tidak dapat dihapus karena memiliki riwayat mutasi stok (Audit Trail).");
        }

        if ($product->image) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        $product->suppliers()->detach();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil dihapus.");
    }
}
