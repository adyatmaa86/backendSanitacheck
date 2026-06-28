@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Ikhtisar Sistem')
@section('page-subtitle', 'Selamat datang kembali, ' . Auth::user()->name . '. Analisis real-time sanitasi fasilitas kesehatan.')

@section('content')
@php
    $total      = $totalFacilities > 0 ? $totalFacilities : 1;
    $compPercent = round(($compliantCount / $total) * 100);
    $critPercent = round(($criticalCount  / $total) * 100);
    $pendPercent = round(($pendingCount   / $total) * 100);
    $uninspPercent = round(($uninspectedCount / $total) * 100);

    // SVG donut offsets (circumference ~100)
    $offset1 = 0;
    $offset2 = -$compPercent;
    $offset3 = -($compPercent + $critPercent);
    $offset4 = -($compPercent + $critPercent + $pendPercent);
@endphp

<!-- ======================== STAT CARDS ======================== -->
<div class="row g-3 mb-4">

    <!-- Total Facilities -->
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-blue-50">
                    <span class="material-symbols-outlined text-blue-primary">domain</span>
                </div>
                <span class="stat-badge bg-green-100 text-green-darker">
                    <span class="material-symbols-outlined fs-13">trending_up</span> +4
                </span>
            </div>
            <div>
                <div class="stat-value">{{ $totalFacilities }}</div>
                <div class="stat-label">Total Fasilitas</div>
            </div>
        </div>
    </div>

    <!-- Compliant -->
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-green-50">
                    <span class="material-symbols-outlined text-green-primary">verified</span>
                </div>
                <span class="stat-badge bg-green-100 text-green-darker">Layak</span>
            </div>
            <div>
                <div class="stat-value text-green-primary">{{ $compliantCount }}</div>
                <div class="stat-label">Bersih & Aman</div>
            </div>
        </div>
    </div>

    <!-- Perlu Dibersihkan -->
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-yellow-50">
                    <span class="material-symbols-outlined text-yellow-primary">cleaning_services</span>
                </div>
                <span class="stat-badge bg-yellow-100 text-yellow-darker">Cukup Baik</span>
            </div>
            <div>
                <div class="stat-value text-yellow-primary">{{ $pendingCount }}</div>
                <div class="stat-label">Perlu Dibersihkan</div>
            </div>
        </div>
    </div>

    <!-- Need Follow Up -->
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-red-50">
                    <span class="material-symbols-outlined text-red-primary">warning</span>
                </div>
                <span class="stat-badge bg-red-100 text-red-darkest">Tidak Layak</span>
            </div>
            <div>
                <div class="stat-value text-red-primary">{{ $needFollowUp }}</div>
                <div class="stat-label">Perlu Diperbaiki</div>
            </div>
        </div>
    </div>

</div>

<!-- ======================== CHARTS ROW ======================== -->
<div class="row g-3 mb-4">

    <!-- Bar Chart: Inspection Trends -->
    <div class="col-lg-8">
        <div class="chart-card h-100 d-flex flex-column">
            <h5 class="chart-title mb-5">Tren Inspeksi (7 Hari Terakhir)</h5>

            <!-- Y-axis labels + bars -->
            <div class="chart-area d-flex gap-2 flex-grow-1">
                <!-- Y labels -->
                <div class="d-flex flex-column justify-content-between text-end min-w-24 pb-20">
                    <span class="fs-16 text-slate-400">{{ $maxInspectionCount }}</span>
                    <span class="fs-16 text-slate-400">{{ round($maxInspectionCount * 0.75) }}</span>
                    <span class="fs-16 text-slate-400">{{ round($maxInspectionCount * 0.5) }}</span>
                    <span class="fs-16 text-slate-400">{{ round($maxInspectionCount * 0.25) }}</span>
                    <span class="fs-16 text-slate-400">0</span>
                </div>
                <!-- Bars -->
                <div class="bar-chart-wrap flex-grow-1">
                    @foreach($inspectionTrends as $trend)
                        <div class="bar-col">
                            <span class="bar-tip">{{ $trend['count'] }} Inspeksi</span>
                            <div class="bar {{ $trend['is_today'] ? 'active' : '' }}" style="height:{{ $trend['percentage'] }}%;"></div>
                            <span class="bar-label">{{ $trend['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Legend -->
            <div class="d-flex gap-3 mt-2">
                <div class="d-flex align-items-center gap-1 fs-12 text-slate-500">
                    <span class="legend-dot-box dot-blue"></span> Hari Ini
                </div>
                <div class="d-flex align-items-center gap-1 fs-12 text-slate-500">
                    <span class="legend-dot-box bg-blue-100"></span> 6 Hari Sebelumnya
                </div>
            </div>
        </div>
    </div>

    <!-- Donut: Status Distribution -->
    <div class="col-lg-4">
        <div class="chart-card h-100 d-flex flex-column">
            <h5 class="chart-title mb-4">Distribusi Status</h5>

            <div class="donut-wrap">
                <svg viewBox="0 0 36 36" class="donut-svg">
                    <circle cx="18" cy="18" r="15.9" fill="transparent" stroke="#f1f5f9" stroke-width="3.5"/>
                    <circle cx="18" cy="18" r="15.9" fill="transparent" stroke="#22c55e"
                        stroke-dasharray="{{ $compPercent }} 100"
                        stroke-dashoffset="{{ $offset1 }}" stroke-width="3.5"/>
                    <circle cx="18" cy="18" r="15.9" fill="transparent" stroke="#ef4444"
                        stroke-dasharray="{{ $critPercent }} 100"
                        stroke-dashoffset="{{ $offset2 }}" stroke-width="3.5"/>
                    <circle cx="18" cy="18" r="15.9" fill="transparent" stroke="#f59e0b"
                        stroke-dasharray="{{ $pendPercent }} 100"
                        stroke-dashoffset="{{ $offset3 }}" stroke-width="3.5"/>
                    <circle cx="18" cy="18" r="15.9" fill="transparent" stroke="#cbd5e1"
                        stroke-dasharray="{{ $uninspPercent }} 100"
                        stroke-dashoffset="{{ $offset4 }}" stroke-width="3.5"/>
                </svg>
                <div class="donut-center">
                    <div class="val">{{ $totalFacilities }}</div>
                    <div class="lbl">Total</div>
                </div>
            </div>

            <div class="mt-auto d-flex flex-column gap-2">
                <div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot dot-green"></span>
                        <span class="legend-text">Bersih & Aman</span>
                    </div>
                    <span class="legend-value">{{ $compliantCount }}</span>
                </div>
                <div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot dot-yellow"></span>
                        <span class="legend-text">Perlu Dibersihkan</span>
                    </div>
                    <span class="legend-value">{{ $pendingCount }}</span>
                </div>
                <div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot dot-red"></span>
                        <span class="legend-text">Perlu Diperbaiki</span>
                    </div>
                    <span class="legend-value">{{ $criticalCount }}</span>
                </div>
                <div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot dot-gray"></span>
                        <span class="legend-text">Belum Inspeksi</span>
                    </div>
                    <span class="legend-value">{{ $uninspectedCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================== RECENT INSPECTIONS TABLE ======================== -->
<div class="data-card">
    <div class="data-card-header">
        <h5 class="data-card-title">
            <span class="material-symbols-outlined align-middle me-1 icon-md text-blue-primary">assignment</span>
            Inspeksi Terbaru
        </h5>
        <a href="{{ route('inspections.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 fs-10 fw-bold">
            + Inspeksi Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Nama Fasilitas</th>
                    <th>Petugas</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Skor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentInspections as $ins)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php $foto = $ins->facility->foto_after ?? $ins->facility->foto_before; @endphp
                                @if($foto)
                                    <img src="{{ str_starts_with($foto, 'uploads/') ? asset($foto) : asset('storage/' . $foto) }}" alt="Foto" class="facility-icon-xs object-fit-cover rounded-9 border-adm">
                                @else
                                    <div class="facility-icon-xs">
                                        <span class="material-symbols-outlined">domain</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="facility-name">{{ $ins->facility->nama_fasilitas }}</div>
                                    <div class="facility-meta">{{ $ins->facility->jenis_fasilitas }} · {{ $ins->facility->lokasi }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="avatar-chip">
                                <div class="avatar-xs">{{ strtoupper(substr($ins->officer->name, 0, 2)) }}</div>
                                <span class="officer-name">{{ $ins->officer->name }}</span>
                            </div>
                        </td>
                        <td class="timestamp-text">
                            {{ $ins->tanggal_inspeksi->diffForHumans() }}
                        </td>
                        <td>
                            @if($ins->kondisi_kebersihan === 'baik')
                                <span class="badge-status badge-compliant">Bersih & Aman</span>
                            @elseif($ins->kondisi_kebersihan === 'cukup')
                                <span class="badge-status badge-review">Perlu Dibersihkan</span>
                            @else
                                <span class="badge-status badge-critical">Perlu Diperbaiki</span>
                            @endif
                        </td>
                        <td class="score-text">
                            @php $insScore = $ins->score; @endphp
                            @if($insScore >= 80)
                                <span class="score-high">{{ $insScore }}/100</span>
                            @elseif($insScore >= 50)
                                <span class="score-mid">{{ $insScore }}/100</span>
                            @else
                                <span class="score-low">{{ $insScore }}/100</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            <span class="material-symbols-outlined empty-icon">assignment</span>
                            Belum ada inspeksi yang dicatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="data-card-footer">
        <span class="page-info">Menampilkan {{ count($recentInspections) }} inspeksi terbaru</span>
        <a href="{{ route('inspections.history') }}" class="fs-10 fw-bold text-blue-primary text-decoration-none">Lihat Semua Riwayat →</a>
    </div>
</div>

@endsection
