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

    // SVG donut offsets (circumference ~100)
    $offset1 = 0;
    $offset2 = -$compPercent;
    $offset3 = -($compPercent + $critPercent);
@endphp

<!-- ======================== STAT CARDS ======================== -->
<div class="row g-3 mb-4">

    <!-- Total Facilities -->
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#eff6ff;">
                    <span class="material-symbols-outlined" style="color:#1a56db;">domain</span>
                </div>
                <span class="stat-badge" style="background:#dcfce7;color:#166534;">
                    <span class="material-symbols-outlined" style="font-size:0.7rem;">trending_up</span> +4
                </span>
            </div>
            <div>
                <div class="stat-value">{{ $totalFacilities }}</div>
                <div class="stat-label">Total Fasilitas</div>
            </div>
        </div>
    </div>

    <!-- Inspections Today -->
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#fefce8;">
                    <span class="material-symbols-outlined" style="color:#ca8a04;">assignment_turned_in</span>
                </div>
                @php
                    $targetRate = 85;
                    $isTargetMet = $todayInspectionRate >= $targetRate;
                @endphp
                <span class="stat-badge" style="background:{{ $isTargetMet ? '#dcfce7' : '#fef3c7' }};color:{{ $isTargetMet ? '#166534' : '#b45309' }};white-space:nowrap;">
                    {{ $todayInspectionRate }}%<span class="d-none d-sm-inline"> / Target {{ $targetRate }}%</span>
                </span>
            </div>
            <div>
                <div class="stat-value">{{ $inspectionsToday }}</div>
                <div class="stat-label">Inspeksi Hari Ini</div>
            </div>
        </div>
    </div>

    <!-- Compliant -->
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#f0fdf4;">
                    <span class="material-symbols-outlined" style="color:#16a34a;">verified</span>
                </div>
                <span class="stat-badge" style="background:#dcfce7;color:#166534;">Layak</span>
            </div>
            <div>
                <div class="stat-value" style="color:#16a34a;">{{ $compliantCount }}</div>
                <div class="stat-label">Bersih & Aman</div>
            </div>
        </div>
    </div>

    <!-- Need Follow Up -->
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon" style="background:#fef2f2;">
                    <span class="material-symbols-outlined" style="color:#ef4444;">warning</span>
                </div>
                <span class="stat-badge" style="background:#fee2e2;color:#991b1b;">Prioritas<span class="d-none d-sm-inline"> Tinggi</span></span>
            </div>
            <div>
                <div class="stat-value" style="color:#ef4444;">{{ $needFollowUp }}</div>
                <div class="stat-label">Perlu Tindakan</div>
            </div>
        </div>
    </div>

</div>

<!-- ======================== CHARTS ROW ======================== -->
<div class="row g-3 mb-4">

    <!-- Bar Chart: Inspection Trends -->
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-header">
                <h5 class="chart-title">Tren Inspeksi</h5>
                <select class="form-select form-select-sm" style="width:auto;font-size:0.78rem;border-color:#e2e8f0;background:#f8fafc;">
                    <option>7 Hari Terakhir</option>
                    <option>30 Hari Terakhir</option>
                </select>
            </div>

            <!-- Y-axis labels + bars -->
            <div class="d-flex gap-2" style="height:200px;">
                <!-- Y labels -->
                <div class="d-flex flex-column justify-content-between text-end pb-5" style="min-width:24px;">
                    <span style="font-size:0.6rem;color:#94a3b8;">{{ $maxInspectionCount }}</span>
                    <span style="font-size:0.6rem;color:#94a3b8;">{{ round($maxInspectionCount * 0.75) }}</span>
                    <span style="font-size:0.6rem;color:#94a3b8;">{{ round($maxInspectionCount * 0.5) }}</span>
                    <span style="font-size:0.6rem;color:#94a3b8;">{{ round($maxInspectionCount * 0.25) }}</span>
                    <span style="font-size:0.6rem;color:#94a3b8;">0</span>
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
                <div class="d-flex align-items-center gap-1" style="font-size:0.72rem;color:#64748b;">
                    <span style="width:10px;height:10px;border-radius:3px;background:#1a56db;display:inline-block;"></span> Tertinggi
                </div>
                <div class="d-flex align-items-center gap-1" style="font-size:0.72rem;color:#64748b;">
                    <span style="width:10px;height:10px;border-radius:3px;background:#bfdbfe;display:inline-block;"></span> Normal
                </div>
            </div>
        </div>
    </div>

    <!-- Donut: Status Distribution -->
    <div class="col-lg-4">
        <div class="chart-card h-100 d-flex flex-column">
            <h5 class="chart-title mb-4">Distribusi Status</h5>

            <div class="donut-wrap">
                <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);width:100%;height:100%;">
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
                </svg>
                <div class="donut-center">
                    <div class="val">{{ $totalFacilities }}</div>
                    <div class="lbl">Total</div>
                </div>
            </div>

            <div class="mt-auto d-flex flex-column gap-2">
                <div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot" style="background:#22c55e;"></span>
                        <span style="font-size:0.78rem;color:#64748b;font-weight:500;">Layak (Bersih)</span>
                    </div>
                    <span style="font-size:0.78rem;font-weight:700;">{{ $compliantCount }}</span>
                </div>
                <div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot" style="background:#ef4444;"></span>
                        <span style="font-size:0.78rem;color:#64748b;font-weight:500;">Perlu Tindakan (Kotor)</span>
                    </div>
                    <span style="font-size:0.78rem;font-weight:700;">{{ $criticalCount }}</span>
                </div>
                <div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot" style="background:#f59e0b;"></span>
                        <span style="font-size:0.78rem;color:#64748b;font-weight:500;">Menunggu Peninjauan</span>
                    </div>
                    <span style="font-size:0.78rem;font-weight:700;">{{ $pendingCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================== RECENT INSPECTIONS TABLE ======================== -->
<div class="data-card">
    <div class="data-card-header">
        <h5 class="data-card-title">
            <span class="material-symbols-outlined align-middle me-1" style="font-size:1rem;color:#1a56db;">assignment</span>
            Inspeksi Terbaru
        </h5>
        <a href="{{ route('inspections.index') }}" class="btn btn-sm btn-primary rounded-pill px-3" style="font-size:0.78rem;font-weight:700;">
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
                                    <img src="{{ str_starts_with($foto, 'uploads/') ? asset($foto) : asset('storage/' . $foto) }}" alt="Foto" class="facility-icon-xs" style="object-fit: cover; border-radius: 9px; border: 1px solid var(--adm-border);">
                                @else
                                    <div class="facility-icon-xs">
                                        <span class="material-symbols-outlined">domain</span>
                                    </div>
                                @endif
                                <div>
                                    <div style="font-size:0.84rem;font-weight:700;color:#0f172a;">{{ $ins->facility->nama_fasilitas }}</div>
                                    <div style="font-size:0.7rem;color:#94a3b8;">{{ $ins->facility->jenis_fasilitas }} · {{ $ins->facility->lokasi }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="avatar-chip">
                                <div class="avatar-xs">{{ strtoupper(substr($ins->officer->name, 0, 2)) }}</div>
                                <span style="font-size:0.82rem;color:#0f172a;">{{ $ins->officer->name }}</span>
                            </div>
                        </td>
                        <td style="font-size:0.78rem;color:#64748b;white-space:nowrap;">
                            {{ $ins->tanggal_inspeksi->diffForHumans() }}
                        </td>
                        <td>
                            @if($ins->kondisi_kebersihan === 'baik')
                                <span class="badge-status badge-compliant">Layak</span>
                            @elseif($ins->kondisi_kebersihan === 'cukup')
                                <span class="badge-status badge-review">Peninjauan</span>
                            @else
                                <span class="badge-status badge-critical">Tidak Layak</span>
                            @endif
                        </td>
                        <td style="font-size:0.84rem;font-weight:700;">
                            @php $insScore = $ins->score; @endphp
                            @if($insScore >= 80)
                                <span style="color:#16a34a;">{{ $insScore }}/100</span>
                            @elseif($insScore >= 50)
                                <span style="color:#ca8a04;">{{ $insScore }}/100</span>
                            @else
                                <span style="color:#dc2626;">{{ $insScore }}/100</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;font-size:0.84rem;">
                            <span class="material-symbols-outlined d-block mb-2" style="font-size:2rem;">assignment</span>
                            Belum ada inspeksi yang dicatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="data-card-footer">
        <span class="page-info">Menampilkan {{ count($recentInspections) }} inspeksi terbaru</span>
        <a href="{{ route('inspections.history') }}" style="font-size:0.78rem;font-weight:700;color:#1a56db;text-decoration:none;">Lihat Semua Riwayat →</a>
    </div>
</div>

@endsection
