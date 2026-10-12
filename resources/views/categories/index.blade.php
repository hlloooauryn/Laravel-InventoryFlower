@extends('layouts.app', ['title' => 'Master Kategori'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Master Data Kategori</h2>
        <p class="text-muted mb-0 small">Kategorisasi ragam bunga, buket, tanaman hias, dan perlengkapan inventori.</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('categories.create') }}" class="btn btn-primary rounded-pill px-3 shadow-xs">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </a>
    @endif
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-bottom p-3">
                <form method="GET" action="{{ route('categories.index') }}" class="row g-2">
                    <div class="col-sm-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control bg-light" placeholder="Cari nama kategori..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-sm-4 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-dark w-100 rounded-3">Cari</button>
                        @if(request('search'))
                        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Nama Kategori</th>
                            <th>Jumlah Produk Terkait</th>
                            <th>Dibuat Pada</th>
                            @if(auth()->user()->isAdmin())
                            <th class="text-end pe-4">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">{{ $category->name }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2">
                                    {{ $category->products_count }} Produk
                                </span>
                            </td>
                            <td class="text-muted small">{{ $category->created_at ? $category->created_at->format('d M Y') : '-' }}</td>
                            @if(auth()->user()->isAdmin())
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus" {{ $category->products_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Belum ada data kategori.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
