@extends('layouts.app')

@section('title', 'Daftar Admin')
@section('page-title', 'Daftar Admin')
@section('page-subtitle', 'Manajemen akun administrator SanitaCheck.')

@section('content')
<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#fef2f2;">
                    <span class="material-symbols-outlined" style="color:#dc2626;">admin_panel_settings</span>
                </div>
            </div>
            <div>
                <div class="stat-value">{{ $totalAdmins }}</div>
                <div class="stat-label">Total Admin</div>
            </div>
        </div>
    </div>
</div>

<!-- Controls Bar -->
<div class="controls-bar">
    <form id="filterForm" action="{{ route('admin.index') }}" method="GET">
        <div class="d-flex flex-row flex-wrap gap-2 align-items-md-center">
            <div class="search-input-wrap flex-grow-1">
                <span class="material-symbols-outlined">search</span>
                <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}" placeholder="Cari nama atau email admin..." autocomplete="off"/>
            </div>
            @if(Auth::user()->role === 'admin')
                <div>
                    <a href="{{ route('settings.add-admin') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-2 px-sm-3"
                        style="font-size:0.8rem;font-weight:700;border-radius:8px;height:36px;text-decoration:none;">
                        <span class="material-symbols-outlined" style="font-size:0.95rem;">admin_panel_settings</span> <span class="d-none d-sm-inline">Tambah Admin</span>
                    </a>
                </div>
            @endif
        </div>
    </form>
</div>

<!-- Admin Table Wrapper -->
<div class="data-card" id="tableContainer">
    @include('admin.partials.table')
</div>

@push('scripts')
    @vite('resources/js/admin-list.js')
@endpush
@endsection
