<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function history(Request $request): View
    {
        $query = StockMovement::with(['product.category', 'user.profile']);

        if ($type = $request->input('type')) {
            if (in_array($type, ['IN', 'OUT'], true)) {
                $query->where('type', $type);
            }
        }

        if ($productId = $request->input('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $movements = $query->latest('created_at')->paginate(15)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('transactions.history', compact('movements', 'products'));
    }

    public function showStockIn(Request $request): View
    {
        $products = Product::orderBy('name')->get();
        $selectedProductId = $request->input('product_id');

        return view('transactions.stock_in', compact('products', 'selectedProductId'));
    }

    public function processStockIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'product_id.required' => 'Silakan pilih produk yang akan ditambahkan stoknya.',
            'quantity.min' => 'Jumlah barang masuk minimal 1 unit.',
        ]);

        return DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

            $product->increment('stock', $validated['quantity']);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'IN',
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?: 'Penerimaan barang / Restock',
            ]);

            return redirect()->route('products.index')
                ->with('success', "Stok produk '{$product->name}' berhasil ditambah sebanyak {$validated['quantity']} unit.");
        });
    }

    public function showStockOut(Request $request): View
    {
        $products = Product::orderBy('name')->get();
        $selectedProductId = $request->input('product_id');

        return view('transactions.stock_out', compact('products', 'selectedProductId'));
    }

    public function processStockOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'product_id.required' => 'Silakan pilih produk yang akan dikurangi stoknya.',
            'quantity.min' => 'Jumlah barang keluar minimal 1 unit.',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // Lock row to prevent race conditions & negative stock
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

                if ($validated['quantity'] > $product->stock) {
                    throw new Exception("Stok tidak mencukupi! Anda meminta {$validated['quantity']} unit, namun stok tersedia saat ini hanya {$product->stock} unit.");
                }

                $product->decrement('stock', $validated['quantity']);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'OUT',
                    'quantity' => $validated['quantity'],
                    'note' => $validated['note'] ?: 'Pengeluaran stok / Penjualan',
                ]);

                return redirect()->route('products.index')
                    ->with('success', "Stok produk '{$product->name}' berhasil dikurangi sebanyak {$validated['quantity']} unit.");
            });
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
