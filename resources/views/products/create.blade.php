@extends('layouts.app', ['title' => 'Tambah Produk Baru'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="h3 fw-bold text-dark mb-1">Tambah Produk Baru</h2>
                <p class="text-muted mb-0 small">Daftarkan varian bunga atau item inventori baru ke katalog toko.</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="sku" class="form-label fw-semibold small text-secondary">Kode SKU <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $skuCandidate) }}" required>
                        </div>
                        @error('sku')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-semibold small text-secondary">Kategori Bunga <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small text-secondary">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Mawar Merah Holland Super" required>
                    @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label fw-semibold small text-secondary">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" step="100" min="1" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                        </div>
                        @error('price')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="stock" class="form-label fw-semibold small text-secondary">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" min="0" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" required>
                        <small class="text-muted d-block mt-1">Dicatat otomatis ke mutasi masuk.</small>
                        @error('stock')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="minimum_stock" class="form-label fw-semibold small text-secondary">Batas Minimum Stok <span class="text-danger">*</span></label>
                        <input type="number" min="0" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', 5) }}" required>
                        <small class="text-muted d-block mt-1">Pemicu peringatan restock.</small>
                        @error('minimum_stock')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label fw-semibold small text-secondary">Foto Produk (Opsional)</label>
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">Format: JPG, PNG, WEBP (Maks: 2MB).</small>
                    @error('image')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Supplier Association -->
                <div class="mb-4 p-3 bg-light rounded-4 border">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-truck me-1"></i> Hubungkan Mitra Supplier (Opsional)</h6>
                    <p class="text-muted small mb-3">Pilih supplier penyedia bunga ini beserta harga beli/kulakan masing-masing.</p>

                    @foreach($suppliers as $sup)
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="suppliers[]" value="{{ $sup->id }}" id="sup_{{ $sup->id }}" {{ is_array(old('suppliers')) && in_array($sup->id, old('suppliers')) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-secondary small" for="sup_{{ $sup->id }}">
                                    {{ $sup->name }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white">Harga Beli Rp</span>
                                <input type="number" step="100" min="0" name="supplier_prices[{{ $sup->id }}]" class="form-control bg-white" placeholder="Harga beli dari supplier..." value="{{ old('supplier_prices.' . $sup->id) }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
