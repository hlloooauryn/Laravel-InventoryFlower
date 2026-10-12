@extends('layouts.app', ['title' => 'Katalog Produk'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Katalog Produk Bunga</h2>
        <p class="text-muted mb-0 small">Kelola seluruh persediaan bunga, penetapan harga jual, dan status stok.</p>
    </div>
    @if(auth()->user()->isAdmin())
    <div>
        <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-3 shadow-xs">
            <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
        </a>
    </div>
    @endif
</div>

<!-- Filter and Search Toolbar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari nama atau SKU..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm bg-light">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="stock_status" class="form-select form-select-sm bg-light">
                    <option value="">Status Stok</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Kritis</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Habis (0)</option>
                    <option value="safe" {{ request('stock_status') === 'safe' ? 'selected' : '' }}>Aman</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="sort" class="form-select form-select-sm bg-light">
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Terbaru</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nama Produk</option>
                    <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>Harga</option>
                    <option value="stock" {{ request('sort') === 'stock' ? 'selected' : '' }}>Jumlah Stok</option>
                </select>
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark w-100 rounded-3" title="Terapkan Filter">
                    <i class="bi bi-funnel"></i>
                </button>
                @if(request()->hasAny(['search', 'category_id', 'stock_status', 'sort']))
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset Filter">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Produk</th>
                    <th>SKU</th>
                    <th>Kategori</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Supplier Terhubung</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            @if($product->image)
                            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="rounded-3 shadow-xs object-fit-cover" width="48" height="48" onerror="this.src='https://placehold.co/48x48?text=🌸'">
                            @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width:48px; height:48px;">
                                <i class="bi bi-image fs-5"></i>
                            </div>
                            @endif
                            <div>
                                <a href="{{ route('products.show', $product) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                    {{ $product->name }}
                                </a>
                                <span class="small text-muted">Min: {{ $product->minimum_stock }} unit</span>
                            </div>
                        </div>
                    </td>
                    <td><code class="text-secondary fw-semibold">{{ $product->sku }}</code></td>
                    <td>
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1">
                            {{ $product->category->name }}
                        </span>
                    </td>
                    <td class="fw-semibold text-dark">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        @if($product->stock <= 0)
                            <span class="badge bg-danger rounded-pill px-2">Habis (0)</span>
                        @elseif($product->isLowStock())
                            <span class="badge bg-warning text-dark rounded-pill px-2">Kritis ({{ $product->stock }})</span>
                        @else
                            <span class="badge bg-success-subtle text-success rounded-pill px-2">{{ $product->stock }} unit</span>
                        @endif
                    </td>
                    <td>
                        @if($product->suppliers->isEmpty())
                            <span class="text-muted small">-</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                {{ $product->suppliers->count() }} Mitra
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('transactions.stock_in', ['product_id' => $product->id]) }}" class="btn btn-outline-success" title="Stok Masuk">
                                <i class="bi bi-plus-lg"></i>
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $product->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-search fs-2 d-block mb-2 text-secondary"></i>
                        Tidak ada data produk yang cocok dengan pencarian atau filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="text-muted small">
            Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
        </span>
        <div>
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>
@endsection
