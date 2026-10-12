@extends('layouts.app', ['title' => 'Master Supplier'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Master Data Supplier</h2>
        <p class="text-muted mb-0 small">Daftar mitra penyedia bunga dan tanaman untuk kebutuhan restock.</p>
    </div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary rounded-pill px-3 shadow-xs">
        <i class="bi bi-plus-lg me-1"></i> Tambah Supplier Baru
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-header bg-white border-bottom p-3">
        <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2">
            <div class="col-sm-8 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light" placeholder="Cari nama atau kontak..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-4 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark w-100 rounded-3">Cari</button>
                @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Nama Supplier</th>
                    <th>Kontak / Telp</th>
                    <th>Alamat</th>
                    <th>Jumlah Produk Dipasok</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $sup)
                <tr>
                    <td class="ps-4 fw-semibold text-dark">{{ $sup->name }}</td>
                    <td><span class="badge bg-light text-secondary border px-2 py-1">{{ $sup->contact ?? '-' }}</span></td>
                    <td class="text-muted small">{{ $sup->address ?? '-' }}</td>
                    <td>
                        <span class="badge bg-info-subtle text-info rounded-pill px-2">
                            {{ $sup->products_count }} Produk
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('suppliers.edit', $sup) }}" class="btn btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier {{ $sup->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Belum ada data supplier.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $suppliers->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
