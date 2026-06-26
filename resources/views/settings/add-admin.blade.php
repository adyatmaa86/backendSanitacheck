@extends('layouts.app')

@section('title', 'Tambah Admin')
@section('page-title', 'Tambah Admin')
@section('page-subtitle', 'Buat akun baru dengan hak akses administrator penuh.')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="form-card">
            <div class="form-card-title">
                <span class="material-symbols-outlined">admin_panel_settings</span>
                Data Admin Baru
            </div>

            <form action="{{ route('settings.store-admin') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label-admin">Nama Lengkap</label>
                    <div class="input-group">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama admin" required />
                        <span class="input-group-text fw-semibold form-addon">(Admin)</span>
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Masukkan email admin" required />
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Nomor Telepon</label>
                    <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" placeholder="Contoh: 081234567890 atau 628123456789" required />
                    @error('phone_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Kata Sandi</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required />
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required />
                </div>

                <div class="info-box mb-4 bg-red-50 border-red-200">
                    <span class="material-symbols-outlined text-red-primary">warning</span>
                    <div>
                        <h6 class="text-red-darkest">Perhatian</h6>
                        <p class="text-red-darkest">Akun yang dibuat akan memiliki role <strong>Admin</strong> dengan hak akses penuh: mengelola fasilitas, inspeksi, dan akun pengguna lain. Pastikan hanya memberikan akses ini kepada pihak yang terpercaya.</p>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light px-4 fw-semibold rounded-8">Batal</a>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold rounded-8">Tambah Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
