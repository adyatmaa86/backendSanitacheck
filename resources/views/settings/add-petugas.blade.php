@extends('layouts.app')

@section('title', 'Tambah Petugas')
@section('page-title', 'Tambah Petugas')
@section('page-subtitle', 'Buat akun baru untuk petugas inspeksi sanitasi.')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="form-card">
            <div class="form-card-title">
                <span class="material-symbols-outlined">person_add</span>
                Data Petugas Baru
            </div>

            <form action="{{ route('settings.store-petugas') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label-admin">Nama Lengkap</label>
                    <div class="input-group">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama petugas" required />
                        <span class="input-group-text fw-semibold form-addon">(Petugas)</span>
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Masukkan email petugas" required />
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

                <div class="info-box mb-4">
                    <span class="material-symbols-outlined">info</span>
                    <div>
                        <h6>Informasi</h6>
                        <p>Akun yang dibuat akan memiliki role <strong>Petugas</strong>. Petugas dapat melakukan inspeksi dan melihat data, namun tidak dapat mengelola fasilitas atau akun lain.</p>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light px-4 fw-semibold rounded-8">Batal</a>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold rounded-8">Tambah Petugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
