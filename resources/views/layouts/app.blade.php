<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Flower Shop' }} - Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="theme-pastel">
    <nav class="navbar navbar-expand-lg navbar-pastel sticky-top py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/logo.jpg') }}" alt="Flower Shop Logo" class="brand-logo shadow-sm rounded-circle" onerror="this.src='https://placehold.co/44x44?text=FS'">
                <div>
                    <span class="brand-title d-block">Flower Shop</span>
                    <span class="brand-tagline d-block">Inventory Management</span>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ps-lg-3 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="bi bi-flower1 me-1"></i> Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="bi bi-tags me-1"></i> Kategori
                        </a>
                    </li>

                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                            <i class="bi bi-truck me-1"></i> Supplier
                        </a>
                    </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-arrow-left-right me-1"></i> Mutasi Stok
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('transactions.stock_in') }}"><i class="bi bi-box-arrow-in-down text-success me-2"></i> Stok Masuk</a></li>
                            <li><a class="dropdown-item" href="{{ route('transactions.stock_out') }}"><i class="bi bi-box-arrow-up text-danger me-2"></i> Stok Keluar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('transactions.history') }}"><i class="bi bi-clock-history me-2"></i> Riwayat Transaksi</a></li>
                        </ul>
                    </li>

                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="bi bi-people me-1"></i> Pengguna
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <div class="user-chip d-flex align-items-center gap-2 px-3 py-1 bg-white rounded-pill shadow-xs">
                        <span class="user-avatar bg-pastel-primary text-primary fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                        </span>
                        <div class="d-none d-sm-block text-start lh-1">
                            <span class="user-name d-block fw-semibold small">{{ auth()->user()->profile->full_name ?? auth()->user()->username }}</span>
                            <span class="badge {{ auth()->user()->isAdmin() ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} rounded-pill" style="font-size: 10px;">
                                {{ strtoupper(auth()->user()->role) }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Keluar dari sistem">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan input:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="footer-pastel py-4 mt-auto border-top text-center text-muted small">
        <div class="container">
            <p class="mb-0">Flower Shop Management System</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
