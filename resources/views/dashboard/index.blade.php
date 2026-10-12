@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Dashboard Ringkasan Inventori</h2>
        <p class="text-muted mb-0 small">Gambaran umum ketersediaan stok, nilai aset, dan sirkulasi bunga saat ini.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('transactions.stock_in') }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-xs">
            <i class="bi bi-plus-circle me-1"></i> Stok Masuk
        </a>
        <a href="{{ route('transactions.stock_out') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-xs">
            <i class="bi bi-dash-circle me-1"></i> Stok Keluar
        </a>
    </div>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase">Total Produk</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalProducts) }}</h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                    <i class="bi bi-flower1 fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase">Valuasi Inventori</span>
                    <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalValuation, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-circle">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase">Stok Kritis</span>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($lowStockCount) }}</h3>
                </div>
                <div class="p-3 bg-danger-subtle text-danger rounded-circle">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase">Mutasi Bulan Ini</span>
                    <div class="fw-bold fs-5 text-dark mb-0">
                        <span class="text-success">+{{ number_format($monthlyIn) }}</span> / <span class="text-danger">-{{ number_format($monthlyOut) }}</span>
                    </div>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-circle">
                    <i class="bi bi-arrow-left-right fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Peringatan Stok Rendah -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark fs-6"><i class="bi bi-bell-fill text-warning me-2"></i>Produk Perlu Restock Segera</h5>
                <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $lowStockProducts->count() }} Item</span>
            </div>
            <div class="card-body px-4 pb-4">
                @if($lowStockProducts->isEmpty())
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-check2-circle text-success fs-1 d-block mb-2"></i>
                    Semua stok produk saat ini dalam batas aman!
                </div>
                @else
                <div class="list-group list-group-flush">
                    @foreach($lowStockProducts as $prod)
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div>
                            <h6 class="mb-0 fw-semibold text-dark">{{ $prod->name }}</h6>
                            <span class="text-muted small">SKU: {{ $prod->sku }} &bull; Kat: {{ $prod->category->name }}</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-danger rounded-pill mb-1 d-inline-block">{{ $prod->stock }} / Min: {{ $prod->minimum_stock }}</span>
                            <div>
                                <a href="{{ route('transactions.stock_in', ['product_id' => $prod->id]) }}" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill" style="font-size: 11px;">
                                    + Tambah
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi Terbaru -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark fs-6"><i class="bi bi-clock-history text-primary me-2"></i>Transaksi Mutasi Terbaru</h5>
                <a href="{{ route('transactions.history') }}" class="text-primary text-decoration-none small fw-semibold">Lihat Semua &rarr;</a>
            </div>
            <div class="card-body px-4 pb-4">
                @if($recentTransactions->isEmpty())
                <div class="text-center py-4 text-muted small">
                    Belum ada riwayat aktivitas mutasi stok.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Produk</th>
                                <th>Tipe</th>
                                <th>Qty</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $tx)
                            <tr>
                                <td class="text-muted">{{ $tx->created_at->format('d M H:i') }}</td>
                                <td class="fw-semibold">{{ $tx->product->name }}</td>
                                <td>
                                    <span class="badge {{ $tx->type === 'IN' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2">
                                        {{ $tx->type === 'IN' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $tx->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->type === 'IN' ? '+' : '-' }}{{ $tx->quantity }}
                                </td>
                                <td class="text-muted">{{ $tx->user->profile->full_name ?? $tx->user->username }}</td>
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
