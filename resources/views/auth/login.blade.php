<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Flower Shop Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="theme-pastel d-flex align-items-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white text-center pt-4 pb-2 border-0">
                        <img src="{{ asset('assets/logo.jpg') }}" alt="Logo" class="rounded-circle shadow-sm mb-3" width="72" height="72" onerror="this.src='https://placehold.co/72x72?text=FS'">
                        <h4 class="brand-title mb-1">Flower Shop</h4>
                        <p class="text-muted small">Silakan masuk ke panel sistem inventori</p>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small border-0 mb-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close small p-2" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger py-2 px-3 small border-0 mb-3" role="alert">
                            <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
                        </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold small text-secondary">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="username" id="username" class="form-control bg-light border-start-0 @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="admin / staff" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label small text-muted" for="remember">
                                        Ingat Saya
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                            </button>
                        </form>

                        <div class="mt-4 pt-3 border-top text-center">
                            <span class="badge bg-light text-secondary border px-3 py-2 small fw-normal">
                                Default Admin: <b>admin</b> / <b>admin123</b>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
