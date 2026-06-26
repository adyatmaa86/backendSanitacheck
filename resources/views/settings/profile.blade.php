@extends('layouts.app')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')
@section('page-subtitle', 'Perbarui informasi akun dan kata sandi Anda.')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="form-card">
            <div class="form-card-title">
                <span class="material-symbols-outlined">person</span>
                Informasi Akun
            </div>

            <form action="{{ route('settings.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label-admin">Nama Lengkap</label>
                    <div class="input-group">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name) }}" required />
                        <span class="input-group-text form-addon-role">({{ ucfirst(Auth::user()->role) }})</span>
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" required />
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Nomor Telepon</label>
                    <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', Auth::user()->phone_number) }}" placeholder="Contoh: 081234567890 atau 628123456789" required />
                    @error('phone_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Role</label>
                    <input type="text" class="form-control" value="{{ ucfirst(Auth::user()->role) }}" disabled />
                    <input type="hidden" name="role" value="{{ Auth::user()->role }}" />
                </div>

                <hr class="my-4 border-adm-color">

                <div class="form-card-title">
                    <span class="material-symbols-outlined">lock</span>
                    Ubah Kata Sandi
                </div>
                <p class="fs-11 text-slate-400 mb-3">Kosongkan jika tidak ingin mengubah kata sandi.</p>

                <div class="mb-3">
                    <label class="form-label-admin">Kata Sandi Baru</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" />
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" />
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light px-4 fw-semibold rounded-8">Batal</a>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold rounded-8">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
