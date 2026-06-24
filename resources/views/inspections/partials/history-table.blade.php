<div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr>
                <th>Fasilitas</th>
                <th>Petugas</th>
                <th>Tanggal & Waktu</th>
                <th>Kondisi</th>
                <th>Indikator</th>
                <th>Catatan</th>
                <th>Tindak Lanjut</th>
                @if(Auth::user()->role === 'admin')
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($inspections as $ins)
                <tr>
                    <td>
                        <div style="font-size:0.84rem;font-weight:700;color:#0f172a;">{{ $ins->facility->nama_fasilitas }}</div>
                        <div style="font-size:0.7rem;color:#94a3b8;text-transform:capitalize;">{{ $ins->facility->jenis_fasilitas }} · {{ $ins->facility->lokasi }}</div>
                    </td>
                    <td>
                        <div class="avatar-chip">
                            <div class="avatar-xs">{{ strtoupper(substr($ins->officer->name, 0, 2)) }}</div>
                            <span style="font-size:0.82rem;color:#0f172a;">{{ $ins->officer->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:0.78rem;color:#64748b;white-space:nowrap;">
                        {{ $ins->tanggal_inspeksi->format('d M Y, H:i') }}
                    </td>
                    <td>
                        @if($ins->kondisi_kebersihan === 'baik')
                            <span class="badge-status badge-compliant">Baik</span>
                        @elseif($ins->kondisi_kebersihan === 'cukup')
                            <span class="badge-status badge-review">Cukup</span>
                        @else
                            <span class="badge-status badge-critical">Buruk</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1" style="font-size:0.72rem;">
                            <div class="d-flex align-items-center gap-1">
                                <span style="width:6px;height:6px;border-radius:50%;display:inline-block;flex-shrink:0;background:{{ $ins->ketersediaan_air === 'tersedia' ? '#22c55e' : '#ef4444' }};"></span>
                                <span style="color:#64748b;">Air: {{ ucfirst($ins->ketersediaan_air) }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span style="width:6px;height:6px;border-radius:50%;display:inline-block;flex-shrink:0;background:{{ $ins->ketersediaan_sabun === 'tersedia' ? '#22c55e' : '#ef4444' }};"></span>
                                <span style="color:#64748b;">Sabun: {{ ucfirst($ins->ketersediaan_sabun) }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span style="width:6px;height:6px;border-radius:50%;display:inline-block;flex-shrink:0;background:{{ $ins->bau_tidak_sedap === 'tidak' ? '#22c55e' : '#ef4444' }};"></span>
                                <span style="color:#64748b;">Bebas Bau: {{ $ins->bau_tidak_sedap === 'ya' ? 'Tidak' : 'Ya' }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:0.78rem;color:#64748b;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $ins->catatan }}">
                        {{ $ins->catatan ?? '—' }}
                    </td>
                    <td>
                        @if($ins->status_tindak_lanjut === 'aman')
                            <span class="badge-status badge-safe">Aman</span>
                        @elseif($ins->status_tindak_lanjut === 'perlu dibersihkan')
                            <span class="badge-status badge-clean">Perlu Dibersihkan</span>
                        @else
                            <span class="badge-status badge-repair">Perlu Perbaikan</span>
                        @endif
                    </td>
                    @if(Auth::user()->role === 'admin')
                        <td>
                            <form action="{{ route('inspections.destroy', $ins->id) }}" method="POST" class="delete-form m-0" data-title="Hapus Laporan" data-text="Apakah Anda yakin ingin menghapus laporan ini?">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger btn-delete" title="Hapus">
                                    <span class="material-symbols-outlined" style="color:#ef4444;">delete</span>
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ Auth::user()->role === 'admin' ? 8 : 7 }}"
                        style="padding:48px;text-align:center;color:#94a3b8;font-size:0.84rem;">
                        <span class="material-symbols-outlined d-block mb-2" style="font-size:2rem;">history</span>
                        Belum ada laporan inspeksi.
                    </td>
                </tr>
            @endforelse
            @if(count($inspections) > 0)
                @for ($i = count($inspections); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="{{ Auth::user()->role === 'admin' ? 8 : 7 }}" style="height: 69px; border: none;">&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">
        Menampilkan {{ $inspections->firstItem() ?? 0 }}–{{ $inspections->lastItem() ?? 0 }}
        dari {{ $inspections->total() }} laporan
    </span>
    <div class="ajax-pagination">{{ $inspections->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
</div>
