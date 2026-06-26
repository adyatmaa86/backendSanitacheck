@extends('layouts.app')

@section('title', 'Pantau Petugas')
@section('page-title', 'Pantau Petugas')
@section('page-subtitle', 'Monitoring real-time petugas yang sedang aktif melakukan pengerjaan.')

@section('content')

@php
    $activePetugasCount = \App\Models\User::where('role', 'petugas')->where('status_pengerjaan', 'aktif')->count();
    $totalPetugasCount = \App\Models\User::where('role', 'petugas')->count();
    $readyPetugasCount = $totalPetugasCount - $activePetugasCount;
@endphp

<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-yellow-50">
                    <span class="material-symbols-outlined text-yellow-600">engineering</span>
                </div>
                <span class="stat-badge bg-yellow-50 text-yellow-600">SIBUK</span>
            </div>
            <div>
                <div class="stat-value text-yellow-600">{{ $activePetugasCount }}</div>
                <div class="stat-label">Petugas Aktif Bekerja</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-green-soft">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                </div>
                <span class="stat-badge bg-green-200 text-dark-green">READY</span>
            </div>
            <div>
                <div class="stat-value text-emerald-500">{{ $readyPetugasCount }}</div>
                <div class="stat-label">Petugas Standby (Ready)</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-blue-50">
                    <span class="material-symbols-outlined text-blue-primary">badge</span>
                </div>
            </div>
            <div>
                <div class="stat-value">{{ $totalPetugasCount }}</div>
                <div class="stat-label">Total Petugas Lapangan</div>
            </div>
        </div>
    </div>
</div>

<!-- Controls Bar -->
<div class="controls-bar">
    <form id="filterForm" action="{{ route('admin.pantau-petugas') }}" method="GET">
        <div class="d-flex flex-row flex-wrap gap-2 align-items-md-center">
            <!-- Search -->
            <div class="search-input-wrap flex-grow-1">
                <span class="material-symbols-outlined">search</span>
                <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}" placeholder="Cari nama atau email petugas..." autocomplete="off"/>
            </div>
        </div>
    </form>
</div>

<!-- Petugas Table Wrapper -->
<div class="data-card" id="tableContainer">
    @include('admin.partials.pantau-table')
</div>

@push('scripts')
<script>initPantauFilter();</script>
@endpush
@endsection
