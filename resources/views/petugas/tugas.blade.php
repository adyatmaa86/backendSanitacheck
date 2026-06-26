@extends('layouts.app')

@section('title', 'Tugas Saya')
@section('page-title', 'Tugas Saya')
@section('page-subtitle', 'Manajemen status pengerjaan tugas inspeksi Anda.')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 rounded-4 shadow-sm inspection-card">
            <div class="card-body p-4">
                
                @if($activeInspections->isNotEmpty())
                    <!-- Status Aktif Bekerja -->
                    <div class="text-center mb-4">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle status-icon-wrap bg-amber-soft">
                                <span class="material-symbols-outlined text-warning icon-3xl">engineering</span>
                            </div>
                        </div>
                        <h3 class="h4 fw-bold mb-2">Status: Sedang Bekerja</h3>
                        <p class="text-muted small mb-0">
                            Anda memiliki <strong>{{ $activeInspections->count() }} tugas aktif</strong> yang sedang berjalan. Silakan selesaikan setiap tugas setelah pengerjaan selesai.
                        </p>
                    </div>

                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($activeInspections as $index => $task)
                            <div class="p-3 rounded-3 border position-relative bg-adm-border">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-primary text-white fw-bold">Tugas #{{ $index + 1 }}</span>
                                            <span class="badge bg-warning text-dark fw-bold text-capitalize">{{ $task->status_tindak_lanjut }}</span>
                                        </div>
                                        <h4 class="fw-bold mb-1 text-adm fs-1rem">{{ $task->facility->nama_fasilitas }}</h4>
                                        <p class="text-muted small mb-2 d-flex align-items-center gap-1">
                                            <span class="material-symbols-outlined icon-md">location_on</span>
                                            {{ $task->facility->lokasi }}
                                        </p>
                                        <div class="p-2 rounded fst-italic mt-2 fs-10 inspection-card">
                                            <span class="fw-bold d-block text-muted mb-1 fs-15">Catatan Inspeksi:</span>
                                            "{{ $task->catatan ?? 'Tidak ada catatan.' }}"
                                        </div>
                                    </div>
                                    
                                    <div class="w-100 mt-3">
                                        <form action="{{ route('petugas.complete-task', $task->id) }}" method="POST" enctype="multipart/form-data" class="m-0">
                                            @csrf

                                            {{-- Checklist Kondisi --}}
                                            <div class="mb-3">

                                                {{-- Water & Soap --}}
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <div class="toggle-row flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <div class="toggle-icon bg-blue-50">
                                                                    <span class="material-symbols-outlined text-blue-primary icon-lg">water_drop</span>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0">Ketersediaan Air</h6>
                                                                    <p class="mb-0 text-muted small">Periksa aliran air pada wastafel utama</p>
                                                                </div>
                                                            </div>
                                                            <div class="btn-group mt-2 mt-sm-0 align-self-end align-self-sm-center">
                                                                <input type="radio" class="btn-check" name="ketersediaan_air" id="water_yes_{{ $task->id }}" value="tersedia"
                                                                    {{ $task->ketersediaan_air === 'tersedia' ? 'checked' : '' }}>
                                                                <label class="btn btn-outline-primary btn-sm px-3 rounded-left-6" for="water_yes_{{ $task->id }}">Ya</label>
                                                                <input type="radio" class="btn-check" name="ketersediaan_air" id="water_no_{{ $task->id }}" value="tidak"
                                                                    {{ $task->ketersediaan_air === 'tidak' ? 'checked' : '' }}>
                                                                <label class="btn btn-outline-primary btn-sm px-3 rounded-right-6" for="water_no_{{ $task->id }}">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="toggle-row flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <div class="toggle-icon bg-green-50">
                                                                    <span class="material-symbols-outlined text-green-primary icon-lg">soap</span>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0">Ketersediaan Sabun</h6>
                                                                    <p class="mb-0 text-muted small">Dispenser sabun terisi dan berfungsi</p>
                                                                </div>
                                                            </div>
                                                            <div class="btn-group mt-2 mt-sm-0 align-self-end align-self-sm-center">
                                                                <input type="radio" class="btn-check" name="ketersediaan_sabun" id="soap_yes_{{ $task->id }}" value="tersedia"
                                                                    {{ $task->ketersediaan_sabun === 'tersedia' ? 'checked' : '' }}>
                                                                <label class="btn btn-outline-primary btn-sm px-3 rounded-left-6" for="soap_yes_{{ $task->id }}">Ya</label>
                                                                <input type="radio" class="btn-check" name="ketersediaan_sabun" id="soap_no_{{ $task->id }}" value="tidak"
                                                                    {{ $task->ketersediaan_sabun === 'tidak' ? 'checked' : '' }}>
                                                                <label class="btn btn-outline-primary btn-sm px-3 rounded-right-6" for="soap_no_{{ $task->id }}">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Odor Detection --}}
                                                <div class="toggle-row flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="toggle-icon bg-green-50">
                                                            <span class="material-symbols-outlined text-green-primary icon-lg">wind_power</span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Bau Tidak Sedap Terdeteksi?</h6>
                                                            <p class="mb-0 text-muted small">Laporkan adanya aroma zat kimia menyengat atau organik</p>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0 align-self-end align-self-sm-center">
                                                        <span class="fs-12 fw-bold text-slate-500">TIDAK</span>
                                                        <div class="form-check form-switch p-0 m-0">
                                                            <input class="form-check-input ms-0" type="checkbox"
                                                                id="odorSwitch_{{ $task->id }}"
                                                                {{ $task->bau_tidak_sedap === 'ya' ? 'checked' : '' }}/>
                                                        </div>
                                                        <span class="fs-12 fw-bold text-blue-primary">YA</span>
                                                    </div>
                                                    <input type="hidden" name="bau_tidak_sedap" id="bau_val_{{ $task->id }}"
                                                        value="{{ $task->bau_tidak_sedap ?? 'tidak' }}"/>
                                                </div>

                                            </div>


                                            {{-- Catatan Update --}}
                                            <div class="mb-3">
                                                <label class="form-label-admin mb-1 fs-11">Catatan (opsional)</label>
                                                <textarea name="catatan" rows="2" class="form-control form-control-sm form-textarea" placeholder="Tambahkan catatan hasil pengerjaan...">{{ $task->catatan }}</textarea>
                                            </div>

                                            {{-- Foto Bukti --}}
                                            <div class="mb-3">
                                                <label class="form-label-admin mb-1 fs-11">Foto Bukti Selesai (After) *</label>
                                                <input type="file" name="foto_after" required class="form-control form-control-sm fs-11 rounded-6" accept="image/*"/>
                                            </div>

                                            <button type="submit" class="btn btn-success btn-sm w-100 py-2 px-3 fw-bold d-flex align-items-center justify-content-center gap-1 rounded-8">
                                                <span class="material-symbols-outlined fs-105">check_circle</span>
                                                Selesaikan Tugas
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Status Ready / Standby -->
                    <div class="text-center py-4">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle status-icon-wrap bg-green-soft">
                                <span class="material-symbols-outlined text-success icon-3xl">check_circle</span>
                            </div>
                        </div>
                        
                        <h3 class="h4 fw-bold mb-2 text-emerald">Status: Ready / Kosong</h3>
                        <p class="text-muted small mb-4">
                            Anda saat ini sedang dalam status standby (siap menerima tugas). Nama Anda aktif di dropdown laporan fasilitas pada portal publik.
                        </p>
                        
                        <div class="p-3 mb-4 rounded-3 border text-start mx-auto bg-adm-border max-w-500">
                            <h4 class="fw-bold mb-2 fs-7 text-adm">Catatan Sistem:</h4>
                            <p class="mb-0 text-muted fs-8rem lh-15">
                                Status Anda akan menjadi <strong>"Sedang Bekerja"</strong> jika Anda memilih opsi <strong>"Perlu Dibersihkan"</strong> atau <strong>"Perlu Perbaikan"</strong> saat mengirim formulir inspeksi.
                            </p>
                        </div>

                        <a href="{{ route('inspections.index') }}" class="btn btn-primary py-2 px-4 fw-bold d-inline-flex align-items-center gap-2 rounded-10 text-decoration-none">
                            <span class="material-symbols-outlined">add</span>
                            Mulai Inspeksi Baru
                        </a>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/petugas-tugas.js')
@endpush
