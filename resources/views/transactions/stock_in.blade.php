@extends('layouts.app', ['title' => 'Stok Masuk (Barang Datang)'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="h3 fw-bold text-dark mb-1">Penerimaan Stok Masuk</h2>
                <p class="text-muted mb-0 small">Catat penambahan stok barang dari supplier atau panen baru.</p>
            </div>
            <a href="{{ route('transactions.history') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-clock-history me-1"></i> Histori
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <form action="{{ route('transactions.stock_in') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="product_id" class="form-label fw-semibold small text-secondary">Pilih Produk <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required autofocus>
                        <option value="">-- Pilih Produk yang Masuk --</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id', $selectedProductId) == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} (SKU: {{ $p->sku }} | Stok Saat Ini: {{ $p->stock }})
                        </option>
                        @endforeach
                    </select>
                    @error('product_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label fw-semibold small text-secondary">Jumlah Masuk (Unit) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-plus-lg"></i></span>
                        <input type="number" min="1" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" placeholder="Contoh: 25" required>
                    </div>
                    @error('quantity')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="note" class="form-label fw-semibold small text-secondary">Catatan / Keterangan Mutasi</label>
                    <textarea name="note" id="note" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="Contoh: Kiriman bunga mawar segar dari CV Florist Nusantara">{{ old('note') }}</textarea>
                    @error('note')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-box-arrow-in-down me-1"></i> Simpan Stok Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
