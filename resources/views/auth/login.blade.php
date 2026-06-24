<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login - SanitaCheck</title>
    <link rel="icon" type="image/png" href="{{ asset('images/tabBG.png') }}"/>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/login.css', 'resources/js/login.js'])
</head>
<body class="d-flex align-items-center justify-content-center p-3 position-relative overflow-hidden">
    <!-- Decorative Blurs -->
    <div class="position-absolute bg-primary bg-opacity-10 rounded-circle" style="width: 320px; height: 320px; top: -100px; right: -100px; filter: blur(80px); z-index: 1;"></div>
    <div class="position-absolute bg-success bg-opacity-10 rounded-circle" style="width: 260px; height: 260px; bottom: -80px; left: -80px; filter: blur(80px); z-index: 1;"></div>

    <div class="w-100 glass-card p-4 p-sm-5 shadow-lg position-relative" style="max-width: 400px; z-index: 10;">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SanitaCheck" class="mx-auto mb-3" style="height: 64px; object-fit: contain;">
            <h2 class="h4 fw-bold text-dark mb-1">SanitaCheck Portal</h2>
            <p class="text-muted small">Monitoring Sanitasi & Kebersihan Fasilitas Umum</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small p-3 mb-4" role="alert">
                <span class="fw-bold">Gagal masuk:</span>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="d-flex flex-column gap-3">
            @csrf
            <div>
                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 10px;">Alamat Email</label>
                <div class="position-relative">
                    <span class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 20px;">mail</span>
                    <input type="email" name="email" value="{{ old('email') }}" required class="form-control ps-5 py-2.5 rounded-3 text-sm @error('email') is-invalid @enderror" placeholder="nama@sanitacheck.com"/>
                </div>
                @error('email')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 10px;">Kata Sandi</label>
                <div class="position-relative">
                    <span class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 20px;">lock</span>
                    <input type="password" name="password" required class="form-control ps-5 py-2.5 rounded-3 text-sm @error('password') is-invalid @enderror" placeholder="••••••••"/>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold mt-2 shadow-sm text-sm">
                Masuk ke Panel
            </button>
        </form>

        <div class="mt-4 pt-4 border-top text-center">
            <p class="text-muted small mb-0" style="font-size: 10px;">© 2026 SanitaCheck. Kelompok 5 UAS Praktek P Naseh.</p>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
