@extends('layouts.app')

@section('title', 'Audit Kepatuhan Sanitasi')
@section('page-title', 'Audit Kepatuhan Sanitasi')
@section('page-subtitle', 'Lengkapi daftar periksa di bawah ini. Semua kolom bertanda * wajib diisi.')

@section('content')
<form action="{{ route('inspections.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf

    <div class="row g-3 mb-3">

        <!-- Facility Selection -->
        <div class="col-md-6">
            <div class="form-card h-100">
                <div class="form-card-title">
                    <span class="material-symbols-outlined">fact_check</span>
                    Detail Fasilitas
                </div>
                <label class="form-label-admin">Pilih Fasilitas *</label>
                <select name="fasilitas_id" id="fasilitas_id" required class="form-select @error('fasilitas_id') is-invalid @enderror">
                    <option value="">— Pilih fasilitas —</option>
                    @foreach($facilities as $fac)
                        <option value="{{ $fac->id }}">{{ $fac->nama_fasilitas }} ({{ $fac->lokasi }})</option>
                    @endforeach
                </select>
                @error('fasilitas_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Inspection Date -->
        <div class="col-md-6">
            <div class="form-card h-100">
                <div class="form-card-title">
                    <span class="material-symbols-outlined">calendar_today</span>
                    Waktu Pelaksanaan
                </div>
                <label class="form-label-admin">Tanggal & Waktu Inspeksi *</label>
                <input type="datetime-local" name="tanggal_inspeksi" id="tanggal_inspeksi"
                    required value="{{ old('tanggal_inspeksi', date('Y-m-d\TH:i')) }}" class="form-control @error('tanggal_inspeksi') is-invalid @enderror"/>
                @error('tanggal_inspeksi')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Compliance Checklist -->
    <div class="form-card mb-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-slate-900 m-0 fs-095"> Daftar Periksa Kepatuhan</h5>
        </div>

        <!-- Overall Cleanliness -->
            <div class="mb-4">
                <label class="form-label-admin mb-2">Kondisi Kebersihan Umum *</label>
                <div class="row g-2">
                    <div class="col-md-4 radio-option">
                        <input type="radio" name="kondisi_kebersihan" value="baik" id="cond_baik" required {{ old('kondisi_kebersihan') === 'baik' ? 'checked' : '' }} checked/>
                    <label class="radio-card" for="cond_baik">
                        <div>
                            <h6>Sangat Baik</h6>
                            <p>Sangat bersih & wangi</p>
                        </div>
                        <span class="material-symbols-outlined text-blue-primary icon-xl">check_circle</span>
                    </label>
                </div>
                    <div class="col-md-4 radio-option">
                        <input type="radio" name="kondisi_kebersihan" value="cukup" id="cond_cukup" {{ old('kondisi_kebersihan') === 'cukup' ? 'checked' : '' }}/>
                        <label class="radio-card" for="cond_cukup">
                            <div>
                                <h6>Cukup Baik</h6>
                                <p>Cukup bersih & terawat</p>
                            </div>
                            <span class="material-symbols-outlined text-slate-400 icon-xl">radio_button_unchecked</span>
                        </label>
                    </div>
                    <div class="col-md-4 radio-option">
                        <input type="radio" name="kondisi_kebersihan" value="buruk" id="cond_buruk" {{ old('kondisi_kebersihan') === 'buruk' ? 'checked' : '' }}/>
                        <label class="radio-card" for="cond_buruk">
                            <div>
                                <h6>Kurang Layak</h6>
                                <p>Kotor & perlu tindakan</p>
                            </div>
                            <span class="material-symbols-outlined text-slate-400 icon-xl">radio_button_unchecked</span>
                        </label>
                    </div>
                </div>
                @error('kondisi_kebersihan')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

        <hr class="hr-light">

        <!-- Water & Soap -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="toggle-row">
                    <div class="d-flex align-items-center gap-3">
                        <div class="toggle-icon bg-blue-50">
                            <span class="material-symbols-outlined text-blue-primary icon-lg">water_drop</span>
                        </div>
                        <div>
                            <h6>Ketersediaan Air</h6>
                            <p>Periksa aliran air pada wastafel utama</p>
                        </div>
                    </div>
                    <div class="btn-group">
                        <input type="radio" class="btn-check" name="ketersediaan_air" id="water_yes" value="tersedia" {{ old('ketersediaan_air') !== 'tidak' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary btn-sm rounded-left-6" for="water_yes">Ya</label>
                        <input type="radio" class="btn-check" name="ketersediaan_air" id="water_no" value="tidak" {{ old('ketersediaan_air') === 'tidak' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary btn-sm rounded-right-6" for="water_no">Tidak</label>
                    </div>
                    @error('ketersediaan_air')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="toggle-row">
                    <div class="d-flex align-items-center gap-3">
                        <div class="toggle-icon bg-green-50">
                            <span class="material-symbols-outlined text-green-primary icon-lg">soap</span>
                        </div>
                        <div>
                            <h6>Ketersediaan Sabun</h6>
                            <p>Dispenser sabun terisi dan berfungsi</p>
                        </div>
                    </div>
                    <div class="btn-group">
                        <input type="radio" class="btn-check" name="ketersediaan_sabun" id="soap_yes" value="tersedia" {{ old('ketersediaan_sabun') !== 'tidak' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary btn-sm rounded-left-6" for="soap_yes">Ya</label>
                        <input type="radio" class="btn-check" name="ketersediaan_sabun" id="soap_no" value="tidak" {{ old('ketersediaan_sabun') === 'tidak' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary btn-sm rounded-right-6" for="soap_no">Tidak</label>
                    </div>
                    @error('ketersediaan_sabun')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <hr class="hr-light">

        <!-- Odor Detection -->
        <div class="toggle-row mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="toggle-icon bg-green-50">
                    <span class="material-symbols-outlined text-green-primary icon-lg">wind_power</span>
                </div>
                <div>
                    <h6>Bau Tidak Sedap Terdeteksi?</h6>
                    <p>Laporkan adanya aroma zat kimia menyengat atau organik</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="fs-12 fw-bold text-slate-500">TIDAK</span>
                <div class="form-check form-switch p-0 m-0">
                    <input class="form-check-input ms-0" type="checkbox" id="odorSwitch"/>
                </div>
                <span class="fs-12 fw-bold text-blue-primary">YA</span>
            </div>
            <input type="hidden" name="bau_tidak_sedap" id="bau_tidak_sedap_val" value="tidak"/>
        </div>

        <!-- Observation Notes -->
        <div class="mb-4">
            <label class="form-label-admin" for="catatan">Catatan Observasi</label>
            <textarea name="catatan" id="catatan" rows="4" class="form-control @error('catatan') is-invalid @enderror"
                placeholder="Masukkan catatan detail temuan lapangan, letak kerusakan spesifik, atau saran pemeliharaan...">{{ old('catatan') }}</textarea>
            @error('catatan')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Kategori Tindak Lanjut (Dinamis & Read-only) -->
        <div class="toggle-row flex-column align-items-stretch d-block">
            <div class="d-flex align-items-center gap-3">
                <div class="toggle-icon bg-yellow-50">
                    <span class="material-symbols-outlined text-yellow-primary icon-lg">notifications_active</span>
                </div>
                <div class="w-100">
                    <label class="form-label-admin mb-1">Kategori Tindak Lanjut</label>
                    <input type="text" id="status_tindak_lanjut_display" class="form-control text-capitalize" readonly value="Aman (Tidak perlu tindak lanjut)"/>
                    <input type="hidden" name="status_tindak_lanjut" id="status_tindak_lanjut" value="aman"/>
                </div>
            </div>
        </div>
    </div>

    <!-- Foto After Upload -->
    <div class="form-card mb-3">
        <div class="form-card-title">
            <span class="material-symbols-outlined">image</span>
            Foto Hasil Inspeksi (After) *
        </div>
        <label class="form-label-admin">Unggah Foto After *</label>
        <input type="file" name="foto_after" required class="form-control @error('foto_after') is-invalid @enderror" accept="image/*"/>
        @error('foto_after')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <p class="text-muted small mt-1 mb-0 fs-11">Unggah dokumentasi kondisi terkini fasilitas pelayanan setelah dilakukan inspeksi/pembersihan.</p>
    </div>

    <!-- Guidelines -->
    <div class="info-box mb-4">
        <span class="material-symbols-outlined">info</span>
        <div>
            <h6>Petunjuk Pengisian</h6>
            <p>Pastikan data diisi sesuai temuan aktual di lokasi fasilitas pelayanan. Lakukan inspeksi secara teliti.</p>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light border px-4 fs-8 fw-semibold rounded-8">
            Batal
        </a>
        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-4 fs-8 fw-bold rounded-8">
            <span class="material-symbols-outlined icon-sm">check_circle</span>
            Kirim Hasil Inspeksi
        </button>
    </div>
</form>

@push('scripts')
    @vite('resources/js/inspections.js')
@endpush
@endsection
