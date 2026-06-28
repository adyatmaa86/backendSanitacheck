@extends('layouts.app')

@section('title', 'Terima Laporan')
@section('page-title', 'Terima Laporan')
@section('page-subtitle', 'Daftar laporan sanitasi yang ditugaskan kepada Anda.')

@section('content')

<div class="data-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Pelapor</th>
                    <th>Fasilitas</th>
                    <th>Keluhan</th>
                    <th>Foto</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $lapor)
                    <tr>
                        <td>
                            <div class="facility-name">{{ $lapor->nama_pelapor }}</div>
                            <div class="facility-meta">{{ $lapor->no_telp }}</div>
                        </td>
                        <td>
                            <div class="facility-name">{{ $lapor->facility->nama_fasilitas }}</div>
                            <div class="facility-meta">{{ $lapor->facility->jenis_fasilitas }} · {{ $lapor->facility->lokasi }}</div>
                        </td>
                        <td class="fs-10 text-slate-500 text-truncate max-w-220" title="{{ $lapor->keluhan }}">
                            {{ $lapor->keluhan }}
                        </td>
                        <td>
                            @if($lapor->foto_bukti)
                                <button type="button" class="btn-icon primary lihat-foto-btn" data-foto="{{ asset('storage/' . $lapor->foto_bukti) }}" data-nama="{{ $lapor->nama_pelapor }}" title="Lihat Foto">
                                    <span class="material-symbols-outlined text-blue-primary">image</span>
                                </button>
                            @else
                                <span class="text-muted fs-10">—</span>
                            @endif
                        </td>
                        <td class="timestamp-text">
                            {{ $lapor->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary fw-bold rounded-8 fs-9 d-flex align-items-center gap-1 px-3 terima-btn"
                                data-id="{{ $lapor->id }}">
                                <span class="material-symbols-outlined fs-105">assignment_returned</span> Terima
                            </button>
                            <form id="terima-form-{{ $lapor->id }}" action="{{ route('petugas.terima-laporan.proses', $lapor->id) }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-table">
                        <td colspan="6">
                            <span class="material-symbols-outlined d-block mb-2 icon-2xl">inbox</span>
                            Tidak ada laporan yang ditugaskan kepada Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
    @vite('resources/js/petugas.js')
@endpush

@endsection
