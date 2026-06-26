@extends('layouts.app')

@section('title', 'Riwayat Inspeksi')
@section('page-title', 'Riwayat Inspeksi')
@section('page-subtitle', 'Saring dan lihat riwayat seluruh laporan inspeksi sanitasi fasilitas.')

@section('content')

<!-- Filter Controls -->
<div class="form-card mb-4">
    <form action="{{ route('inspections.history') }}" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label-admin">Pilih Fasilitas</label>
                <select name="fasilitas_id" class="form-select">
                    <option value="">— Semua Fasilitas —</option>
                    @foreach($facilities as $fac)
                        <option value="{{ $fac->id }}" {{ request('fasilitas_id') == $fac->id ? 'selected' : '' }}>
                            {{ $fac->nama_fasilitas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label-admin">Status Tindak Lanjut</label>
                <select name="status_tindak_lanjut" class="form-select">
                    <option value="">— Semua Status —</option>
                    <option value="aman"             {{ request('status_tindak_lanjut') == 'aman'             ? 'selected' : '' }}>Aman</option>
                    <option value="perlu dibersihkan" {{ request('status_tindak_lanjut') == 'perlu dibersihkan' ? 'selected' : '' }}>Perlu Dibersihkan</option>
                    <option value="perlu perbaikan"  {{ request('status_tindak_lanjut') == 'perlu perbaikan'  ? 'selected' : '' }}>Perlu Perbaikan</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label-admin">Pilih Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control"/>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold rounded-8 fs-9">
                    <span class="material-symbols-outlined align-middle fs-7">filter_list</span> Filter
                </button>
                <a href="{{ route('inspections.history') }}" class="btn btn-sm btn-light border flex-grow-1 fw-semibold rounded-8 fs-9">
                    Reset
                </a>
            </div>
        </div>
    </form>

    @if(Auth::user()->role === 'admin')
        <hr class="hr-light border-adm-color my-3">
        <div class="d-flex justify-content-end">
            <form action="{{ route('inspections.destroy-all') }}" method="POST" class="delete-form m-0" 
                data-title="Hapus Semua Riwayat" 
                data-text="Apakah Anda yakin ingin menghapus seluruh riwayat laporan? Tindakan ini akan mengosongkan semua data riwayat dan mengatur ulang status semua petugas.">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-danger btn-delete d-flex align-items-center gap-1 fs-9 fw-bold rounded-8 py-8px px-16px">
                    <span class="material-symbols-outlined fs-105">delete_forever</span> Hapus Semua Riwayat
                </button>
            </form>
        </div>
    @endif
</div>

<!-- History Table -->
<div class="data-card">
    @include('inspections.partials.history-table')
</div>

@push('scripts')
    @vite('resources/js/history.js')
@endpush

@endsection
