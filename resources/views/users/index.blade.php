@extends('layouts.app', ['title' => 'Manajemen Pengguna'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Manajemen Pengguna (RBAC)</h2>
        <p class="text-muted mb-0 small">Kelola akses akun Administrator dan Staff inventori toko bunga.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill px-3 shadow-xs">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-header bg-white border-bottom p-3">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2">
            <div class="col-sm-8 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light" placeholder="Cari username atau nama lengkap..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-4 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark w-100 rounded-3">Cari</button>
                @if(request('search'))
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Pengguna</th>
                    <th>Nama Lengkap</th>
                    <th>Hak Akses (Role)</th>
                    <th>Terdaftar Sejak</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td class="ps-4">
                        <span class="fw-semibold text-dark">{{ $u->username }}</span>
                        @if($u->id === auth()->id())
                        <span class="badge bg-success-subtle text-success ms-1 small">Akun Anda</span>
                        @endif
                    </td>
                    <td>{{ $u->profile->full_name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $u->isAdmin() ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} rounded-pill px-3 py-1">
                            {{ strtoupper($u->role) }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('users.edit', $u) }}" class="btn btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->username }}?');">
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
                    <td colspan="5" class="text-center py-4 text-muted">
                        Belum ada data pengguna.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
