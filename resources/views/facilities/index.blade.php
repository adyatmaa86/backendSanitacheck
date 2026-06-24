@extends('layouts.app')

@section('title', 'Kelola Fasilitas')
@section('page-title', 'Kelola Fasilitas')
@section('page-subtitle', 'Manajemen fasilitas pelayanan, lokasi, dan pemantauan status kelayakan.')

@section('content')

<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#eff6ff;">
                    <span class="material-symbols-outlined" style="color:#1a56db;">domain</span>
                </div>
                <span class="stat-badge" style="background:#dcfce7;color:#166534;">
                    <span class="material-symbols-outlined" style="font-size:0.65rem;">trending_up</span> 12%
                </span>
            </div>
            <div>
                <div class="stat-value">{{ $totalFacilities }}</div>
                <div class="stat-label">Total Fasilitas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#fef2f2;">
                    <span class="material-symbols-outlined" style="color:#ef4444;">warning</span>
                </div>
                <span class="stat-badge" style="background:#fee2e2;color:#991b1b;">MENDESAK</span>
            </div>
            <div>
                <div class="stat-value" style="color:#ef4444;">{{ $criticalCount }}</div>
                <div class="stat-label">Status Tidak Layak</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#fefce8;">
                    <span class="material-symbols-outlined" style="color:#ca8a04;">schedule</span>
                </div>
                <span class="stat-badge" style="background:#fef9c3;color:#854d0e;">Tertunda</span>
            </div>
            <div>
                <div class="stat-value" style="color:#ca8a04;">{{ $pendingCount }}</div>
                <div class="stat-label">Inspeksi Menunggu</div>
            </div>
        </div>
    </div>
</div>

<!-- Controls Bar -->
<div class="controls-bar">
    <form id="filterForm" action="{{ route('facilities.index') }}" method="GET">
        <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center">
            <!-- Search -->
            <div class="search-input-wrap flex-grow-1">
                <span class="material-symbols-outlined">search</span>
                <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}" placeholder="Cari nama fasilitas..." autocomplete="off"/>
            </div>
            <!-- Filter Dropdown -->
            <div style="min-width: 180px;">
                <select name="jenis_fasilitas" id="filterJenis" class="form-select" style="border:1.5px solid #e2e8f0;background:#f8fafc;font-size:0.8rem;font-weight:600;color:#64748b;border-radius:8px;height:36px;cursor:pointer;">
                    <option value="">— Semua Jenis —</option>
                    @foreach($listJenis as $jf)
                        <option value="{{ $jf->slug }}" {{ ($jenis ?? '') === $jf->slug ? 'selected' : '' }}>{{ $jf->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Add Facility & Jenis Fasilitas Button for Admin -->
            @if(Auth::user()->role === 'admin')
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-3" data-bs-toggle="modal" data-bs-target="#jenis-fasilitas-modal"
                        style="font-size:0.8rem;font-weight:700;border-radius:8px;height:36px;">
                        <span class="material-symbols-outlined" style="font-size:0.95rem;">category</span> Jenis Fasilitas
                    </button>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-3" data-bs-toggle="modal" data-bs-target="#new-facility-modal"
                        style="font-size:0.8rem;font-weight:700;border-radius:8px;height:36px;">
                        <span class="material-symbols-outlined" style="font-size:0.95rem;">add</span> Fasilitas Baru
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>

@push('scripts')
    @vite('resources/js/facilities.js')
@endpush

<!-- Facilities Table Wrapper -->
<div class="data-card" id="tableContainer">
    @include('facilities.partials.table')
</div>

@if(Auth::user()->role === 'admin')

<!-- ===== NEW FACILITY MODAL ===== -->
<div class="modal fade" id="new-facility-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="material-symbols-outlined align-middle me-1" style="font-size:1rem;color:#1a56db;">add_circle</span>
                    Tambah Fasilitas Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('facilities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-admin">Nama Fasilitas</label>
                        <input type="text" name="nama_fasilitas" required class="form-control @error('nama_fasilitas') is-invalid @enderror" placeholder="Contoh: Toilet Lobby Utama" value="{{ old('nama_fasilitas') }}"/>
                        @error('nama_fasilitas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label-admin">Jenis Fasilitas</label>
                            <select name="jenis_fasilitas" required class="form-select @error('jenis_fasilitas') is-invalid @enderror">
                                @foreach($listJenis as $jf)
                                    <option value="{{ $jf->slug }}" {{ old('jenis_fasilitas') === $jf->slug ? 'selected' : '' }}>{{ $jf->nama_jenis }}</option>
                                @endforeach
                            </select>
                            @error('jenis_fasilitas')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label-admin">Petugas</label>
                            <select name="petugas_id" required class="form-select @error('petugas_id') is-invalid @enderror">
                                <option value="" disabled selected>Pilih Petugas</option>
                                @foreach($listPetugas as $p)
                                    <option value="{{ $p->id }}" {{ old('petugas_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('petugas_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-admin">Lokasi / Detail Tempat</label>
                        <input type="text" name="lokasi" required class="form-control @error('lokasi') is-invalid @enderror" placeholder="Contoh: Gedung A, Lantai 1" value="{{ old('lokasi') }}"/>
                        @error('lokasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label-admin">Foto Before</label>
                        <input type="file" name="foto_before" class="form-control @error('foto_before') is-invalid @enderror" accept="image/*"/>
                        @error('foto_before')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="status_aktif" value="1" id="status_aktif" checked class="form-check-input"/>
                        <label for="status_aktif" class="form-check-label" style="font-size:0.82rem;font-weight:600;">Aktif & Siap Inspeksi</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light px-4 fw-600" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-700">Simpan Fasilitas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== EDIT FACILITY MODAL ===== -->
<div class="modal fade" id="edit-facility-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="material-symbols-outlined align-middle me-1" style="font-size:1rem;color:#1a56db;">edit</span>
                    Ubah Data Fasilitas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit-facility-form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-admin">Nama Fasilitas</label>
                        <input type="text" name="nama_fasilitas" id="edit_nama" required class="form-control"/>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label-admin">Jenis Fasilitas</label>
                            <select name="jenis_fasilitas" id="edit_jenis" required class="form-select">
                                @foreach($listJenis as $jf)
                                    <option value="{{ $jf->slug }}">{{ $jf->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-admin">Petugas</label>
                            <select name="petugas_id" id="edit_petugas" required class="form-select">
                                <option value="" disabled>Pilih Petugas</option>
                                @foreach($listPetugas as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-admin">Lokasi / Detail Tempat</label>
                        <input type="text" name="lokasi" id="edit_lokasi" required class="form-control"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-admin">Foto Before</label>
                        <input type="file" name="foto_before" class="form-control" accept="image/*"/>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="status_aktif" value="1" id="edit_status" class="form-check-input"/>
                        <label for="edit_status" class="form-check-label" style="font-size:0.82rem;font-weight:600;">Aktif & Siap Inspeksi</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== JENIS FASILITAS MODAL (CRUD) ===== -->
<div class="modal fade" id="jenis-fasilitas-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="material-symbols-outlined align-middle me-1" style="font-size:1rem;color:#1a56db;">category</span>
                    Kelola Jenis Fasilitas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tambah Jenis Fasilitas Form -->
                <form action="{{ route('jenis-fasilitas.store') }}" method="POST" class="mb-4 p-3 border rounded-3" style="background: var(--adm-bg); border-color: var(--adm-border) !important;">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-sm-8">
                            <label class="form-label-admin">Nama Jenis Fasilitas Baru</label>
                            <input type="text" name="nama_jenis" required class="form-control form-control-sm @error('nama_jenis') is-invalid @enderror" placeholder="Contoh: Lab Komputer" style="background: var(--adm-surface); color: var(--adm-text);" value="{{ old('nama_jenis') }}"/>
                            @error('nama_jenis')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-700" style="height:34px;">Tambah Jenis</button>
                        </div>
                    </div>
                </form>

                <!-- List Tabel Jenis Fasilitas -->
                <div class="table-responsive">
                    <table class="table-admin" style="font-size:0.82rem; width: 100%;">
                        <thead>
                            <tr>
                                <th>Nama Jenis</th>
                                <th>Slug</th>
                                <th style="width:140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listJenis as $jf)
                                <tr>
                                    <td>
                                        <!-- Form Edit Inline -->
                                        <form id="form-edit-jenis-{{ $jf->id }}" action="{{ route('jenis-fasilitas.update', $jf->id) }}" method="POST" class="m-0 d-none">
                                            @csrf @method('PUT')
                                            <input type="text" name="nama_jenis" value="{{ $jf->nama_jenis }}" required class="form-control form-control-sm" style="background: var(--adm-surface); color: var(--adm-text);"/>
                                        </form>
                                        <span id="text-jenis-{{ $jf->id }}">{{ $jf->nama_jenis }}</span>
                                    </td>
                                    <td><code>{{ $jf->slug }}</code></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" id="btn-edit-jenis-{{ $jf->id }}" onclick="toggleEditJenis({{ $jf->id }})" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.72rem;">Ubah</button>
                                            <button type="button" id="btn-save-jenis-{{ $jf->id }}" onclick="document.getElementById('form-edit-jenis-{{ $jf->id }}').submit()" class="btn btn-sm btn-success py-0 px-2 d-none" style="font-size: 0.72rem;">Simpan</button>
                                            <button type="button" id="btn-cancel-jenis-{{ $jf->id }}" onclick="toggleEditJenis({{ $jf->id }}, true)" class="btn btn-sm btn-light py-0 px-2 d-none" style="font-size: 0.72rem;">Batal</button>
                                            
                                            <form action="{{ route('jenis-fasilitas.destroy', $jf->id) }}" method="POST" class="delete-form m-0" data-title="Hapus Jenis Fasilitas" data-text="Apakah Anda yakin ingin menghapus jenis fasilitas ini?">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-delete" style="font-size: 0.72rem;">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada jenis fasilitas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light px-4 fw-600" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



@endif
@endsection
