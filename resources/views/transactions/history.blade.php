@extends('layouts.app', ['title' => 'Riwayat Mutasi Stok'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Riwayat Mutasi Stok (Audit Trail)</h2>
        <p class="text-muted mb-0 small">Audit log pencatatan kronologis mutasi keluar-masuk barang dan penanggung jawab.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('transactions.stock_in') }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-xs">
            <i class="bi bi-plus-circle me-1"></i> Stok Masuk
        </a>
        <a href="{{ route('transactions.stock_out') }}" class="btn btn-danger btn-sm rounded-pill px-3 shadow-xs">
            <i class="bi bi-dash-circle me-1"></i> Stok Keluar
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('transactions.history') }}" class="row g-2 align-items-center">
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm bg-light">
                    <option value="">Semua Tipe</option>
                    <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>Stok Masuk (IN)</option>
                    <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>Stok Keluar (OUT)</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="product_id" class="form-select form-select-sm bg-light">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }} ({{ $p->sku }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control form-control-sm bg-light" value="{{ request('start_date') }}" title="Tanggal Mulai">
            </div>

            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control form-control-sm bg-light" value="{{ request('end_date') }}" title="Tanggal Selesai">
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark w-100 rounded-3" title="Filter Data">
                    <i class="bi bi-funnel"></i>
                </button>
                @if(request()->hasAny(['type', 'product_id', 'start_date', 'end_date']))
                <a href="{{ route('transactions.history') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Waktu Transaksi</th>
                    <th>Nama Produk</th>
                    <th>Jenis Mutasi</th>
                    <th>Jumlah</th>
                    <th>Petugas</th>
                    <th class="pe-4">Catatan / Alasan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $tx)
                <tr>
                    <td class="ps-4 text-muted small">{{ $tx->created_at->format('d M Y, H:i') }} WIB</td>
                    <td>
                        <span class="fw-semibold text-dark">{{ $tx->product->name }}</span>
                        <code class="d-block text-secondary small">{{ $tx->product->sku }}</code>
                    </td>
                    <td>
                        <span class="badge {{ $tx->type === 'IN' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} rounded-pill px-3 py-1">
                            {{ $tx->type === 'IN' ? 'Stok Masuk' : 'Stok Keluar' }}
                        </span>
                    </td>
                    <td class="fw-bold {{ $tx->type === 'IN' ? 'text-success' : 'text-danger' }} fs-6">
                        {{ $tx->type === 'IN' ? '+' : '-' }}{{ $tx->quantity }} unit
                    </td>
                    <td>
                        <span class="small fw-semibold text-dark">{{ $tx->user->profile->full_name ?? $tx->user->username }}</span>
                        <span class="text-muted d-block small">({{ $tx->user->role }})</span>
                    </td>
                    <td class="text-muted small pe-4">{{ $tx->note ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                        Belum ada data pergerakan stok yang sesuai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movements->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $movements->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
