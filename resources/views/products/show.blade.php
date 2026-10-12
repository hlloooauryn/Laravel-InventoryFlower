@extends('layouts.app', ['title' => 'Detail Produk: ' . $product->name])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Detail Produk</h2>
        <p class="text-muted mb-0 small">Informasi terperinci stok, supplier, dan histori audit produk.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white mb-4">
            @if($product->image)
            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded-4 mb-3 shadow-xs" style="max-height: 240px; object-fit: cover;">
            @else
            <div class="bg-light rounded-4 d-flex align-items-center justify-content-center text-muted mx-auto mb-3" style="width:160px; height:160px;">
                <i class="bi bi-image fs-1"></i>
            </div>
            @endif
            <h4 class="fw-bold text-dark mb-1">{{ $product->name }}</h4>
            <div class="mb-2"><code class="fs-6">{{ $product->sku }}</code></div>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 mb-3">{{ $product->category->name }}</span>
            <div class="fs-4 fw-bold text-primary mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</div>

            <div class="d-flex justify-content-around border-top pt-3 text-start">
                <div>
                    <span class="text-muted small d-block">Stok Saat Ini</span>
                    <span class="fs-5 fw-bold {{ $product->isLowStock() ? 'text-danger' : 'text-success' }}">{{ $product->stock }} unit</span>
                </div>
                <div>
                    <span class="text-muted small d-block">Batas Minimum</span>
                    <span class="fs-5 fw-bold text-secondary">{{ $product->minimum_stock }} unit</span>
                </div>
            </div>
        </div>

        <!-- Suppliers Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-truck me-2"></i>Mitra Supplier</h6>
            @if($product->suppliers->isEmpty())
                <p class="text-muted small mb-0">Belum ada mitra supplier yang ditautkan ke produk ini.</p>
            @else
                <ul class="list-group list-group-flush small">
                    @foreach($product->suppliers as $sup)
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold text-dark">{{ $sup->name }}</div>
                            <span class="text-muted">{{ $sup->contact ?? 'Tanpa kontak' }}</span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Harga Beli</span>
                            <span class="fw-semibold text-success">
                                {{ $sup->pivot->supplier_price ? 'Rp ' . number_format($sup->pivot->supplier_price, 0, ',', '.') : '-' }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Stock Audit Log -->
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark fs-6"><i class="bi bi-clock-history text-primary me-2"></i>Histori Mutasi Stok (Audit Trail)</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('transactions.stock_in', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        + Stok Masuk
                    </a>
                    <a href="{{ route('transactions.stock_out', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        - Stok Keluar
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @if($product->stockMovements->isEmpty())
                <div class="text-center py-5 text-muted small">
                    Belum ada rekaman pergerakan stok untuk produk ini.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Petugas</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->stockMovements as $mov)
                            <tr>
                                <td class="text-muted">{{ $mov->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $mov->type === 'IN' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2">
                                        {{ $mov->type === 'IN' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $mov->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                    {{ $mov->type === 'IN' ? '+' : '-' }}{{ $mov->quantity }}
                                </td>
                                <td class="text-muted">{{ $mov->user->profile->full_name ?? $mov->user->username }}</td>
                                <td class="text-muted">{{ $mov->note ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
