@extends('layouts.app')

@section('title', 'Daftar Petugas')
@section('page-title', 'Daftar Petugas')
@section('page-subtitle', 'Manajemen dan monitoring para petugas lapangan SanitaCheck.')

@section('content')

@php
    $totalPetugas = \App\Models\User::where('role', 'petugas')->count();
    $pjIds = \App\Models\Fasilitas::whereNotNull('penanggung_jawab')->pluck('penanggung_jawab');
    $tambahanIds = \DB::table('fasilitas_petugas')->pluck('user_id');
    $allActiveIds = $pjIds->merge($tambahanIds)->unique();
    $activeWithFacilities = \App\Models\User::where('role', 'petugas')
        ->whereIn('id', $allActiveIds)
        ->count();
    $idlePetugas = $totalPetugas - $activeWithFacilities;
@endphp

<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-blue-50">
                    <span class="material-symbols-outlined text-blue-primary">badge</span>
                </div>
            </div>
            <div>
                <div class="stat-value">{{ $totalPetugas }}</div>
                <div class="stat-label">Total Petugas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-green-soft">
                    <span class="material-symbols-outlined text-emerald">domain</span>
                </div>
                <span class="stat-badge bg-green-200 text-dark-green">AKTIF</span>
            </div>
            <div>
                <div class="stat-value text-emerald">{{ $activeWithFacilities }}</div>
                <div class="stat-label">Mengelola Fasilitas</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-amber-soft">
                    <span class="material-symbols-outlined text-amber-500">person_off</span>
                </div>
                <span class="stat-badge bg-yellow-100 text-dark-amber">STANDBY</span>
            </div>
            <div>
                <div class="stat-value text-amber-500">{{ $idlePetugas }}</div>
                <div class="stat-label">Petugas Standby</div>
            </div>
        </div>
    </div>
</div>

<!-- Controls Bar -->
<div class="controls-bar">
    <form id="filterForm" action="{{ route('petugas.index') }}" method="GET">
        <div class="d-flex flex-row flex-wrap gap-2 align-items-md-center">
            <!-- Search -->
            <div class="search-input-wrap flex-grow-1">
                <span class="material-symbols-outlined">search</span>
                <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}" placeholder="Cari nama atau email petugas..." autocomplete="off"/>
            </div>
            <!-- Add Petugas Shortcut for Admin -->
            @if(Auth::user()->role === 'admin')
                <div>
                    <a href="{{ route('settings.add-petugas') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-2 px-sm-3 rounded-8 text-decoration-none fs-8rem fw-bold btn-h-36">
                        <span class="material-symbols-outlined icon-sm">person_add</span> <span class="d-none d-sm-inline">Tambah Petugas</span>
                    </a>
                </div>
            @endif
        </div>
    </form>
</div>

<!-- Petugas Table Wrapper -->
<div class="data-card" id="tableContainer">
    @include('petugas.partials.table')
</div>

@push('scripts')
    @vite('resources/js/petugas.js')
@endpush
@endsection
