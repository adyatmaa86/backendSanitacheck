@extends('layouts.app')

@section('title', 'Daftar Petugas')
@section('page-title', 'Daftar Petugas')
@section('page-subtitle', 'Manajemen dan monitoring para petugas lapangan SanitaCheck.')

@section('content')

@php
    $totalPetugas = \App\Models\User::where('role', 'petugas')->count();
    $activeWithFacilities = \App\Models\User::where('role', 'petugas')
        ->whereIn('id', \App\Models\Fasilitas::pluck('penanggung_jawab'))
        ->count();
    $idlePetugas = $totalPetugas - $activeWithFacilities;
@endphp

<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#eff6ff;">
                    <span class="material-symbols-outlined" style="color:#1a56db;">badge</span>
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
                <div class="stat-icon" style="background:#e8fdf0;">
                    <span class="material-symbols-outlined" style="color:#10b981;">domain</span>
                </div>
                <span class="stat-badge" style="background:#d1fae5;color:#065f46;">AKTIF</span>
            </div>
            <div>
                <div class="stat-value" style="color:#10b981;">{{ $activeWithFacilities }}</div>
                <div class="stat-label">Mengelola Fasilitas</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#fffbeb;">
                    <span class="material-symbols-outlined" style="color:#f59e0b;">person_off</span>
                </div>
                <span class="stat-badge" style="background:#fef3c7;color:#92400e;">STANDBY</span>
            </div>
            <div>
                <div class="stat-value" style="color:#f59e0b;">{{ $idlePetugas }}</div>
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
                    <a href="{{ route('settings.add-petugas') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-2 px-sm-3"
                        style="font-size:0.8rem;font-weight:700;border-radius:8px;height:36px;text-decoration:none;">
                        <span class="material-symbols-outlined" style="font-size:0.95rem;">person_add</span> <span class="d-none d-sm-inline">Tambah Petugas</span>
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
