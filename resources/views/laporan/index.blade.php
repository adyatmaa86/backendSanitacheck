@extends('layouts.app')

@section('title', 'Laporan Masuk')
@section('page-title', 'Laporan Masuk')
@section('page-subtitle', 'Kelola laporan sanitasi dari masyarakat.')

@section('content')

<div class="form-card mb-4">
    <form action="{{ route('laporan.index') }}" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
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
            <div class="col-12 col-md-4">
                <label class="form-label-admin">Status</label>
                <select name="status" class="form-select">
                    <option value="">— Semua Status —</option>
                    <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai"  {{ request('status') == 'selesai'  ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak"  {{ request('status') == 'ditolak'  ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold rounded-8 fs-9">
                    <span class="material-symbols-outlined align-middle fs-7">filter_list</span> Filter
                </button>
                <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-light border flex-grow-1 fw-semibold rounded-8 fs-9">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="data-card" id="laporan-data" data-petugas='@json($facilityPetugasMap)' data-busy-petugas='@json($busyPetugasIds)' data-token="{{ csrf_token() }}">
    @include('laporan.partials.table')
</div>

@push('scripts')
    @vite('resources/js/laporan.js')
@endpush

@endsection
