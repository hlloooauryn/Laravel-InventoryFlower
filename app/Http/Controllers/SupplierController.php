<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supplier::withCount('products');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama supplier wajib diisi.',
        ]);

        $supplier = Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' berhasil ditambahkan.");
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama supplier wajib diisi.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->products()->detach();
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' berhasil dihapus.");
    }
}
