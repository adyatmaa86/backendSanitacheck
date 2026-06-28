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
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inspections as $ins)
                <tr>
                    <td>
                        <div class="facility-name">{{ $ins->facility->nama_fasilitas }}</div>
                        <div class="facility-meta">{{ $ins->facility->jenis_fasilitas }} · {{ $ins->facility->lokasi }}</div>
                    </td>
                    <td>
                        <div class="avatar-chip">
                            <div class="avatar-xs">{{ strtoupper(substr($ins->officer->name, 0, 2)) }}</div>
                            <span class="officer-name">{{ $ins->officer->name }}</span>
                        </div>
                    </td>
                    <td class="timestamp-text">
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
                        <div class="d-flex flex-column gap-1 fs-12">
                            <div class="d-flex align-items-center gap-1">
                                <span class="indicator-dot {{ $ins->ketersediaan_air === 'tersedia' ? 'indicator-dot-success' : 'indicator-dot-danger' }}"></span>
                                <span class="text-slate-500">Air: {{ ucfirst($ins->ketersediaan_air) }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="indicator-dot {{ $ins->ketersediaan_sabun === 'tersedia' ? 'indicator-dot-success' : 'indicator-dot-danger' }}"></span>
                                <span class="text-slate-500">Sabun: {{ ucfirst($ins->ketersediaan_sabun) }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="indicator-dot {{ $ins->bau_tidak_sedap === 'tidak' ? 'indicator-dot-success' : 'indicator-dot-danger' }}"></span>
                                <span class="text-slate-500">Bau Tidak Sedap: {{ $ins->bau_tidak_sedap === 'ya' ? 'Ya' : 'Tidak' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="fs-10 text-slate-500 text-truncate max-w-180" title="{{ $ins->catatan }}">
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
                                    <span class="material-symbols-outlined text-red-primary">delete</span>
                                </button>
                            </form>
                        </td>
                    @else
                        <td>
                            <button type="button" class="btn-icon warning edit-inspeksi-btn"
                                data-id="{{ $ins->id }}"
                                title="Edit">
                                <span class="material-symbols-outlined text-yellow-primary">edit</span>
                            </button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr class="empty-table">
                    <td colspan="8">
                        <span class="material-symbols-outlined d-block mb-2 icon-2xl">history</span>
                        Belum ada laporan inspeksi.
                    </td>
                </tr>
            @endforelse
            @if(count($inspections) > 0)
                @for ($i = count($inspections); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="8" class="border-0"> &nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">
        @if ($inspections instanceof \Illuminate\Pagination\AbstractPaginator)
            Menampilkan {{ $inspections->firstItem() ?? 0 }}–{{ $inspections->lastItem() ?? 0 }} dari {{ $inspections->total() }} laporan
        @else
            Total {{ count($inspections) }} laporan
        @endif
    </span>
    <div class="ajax-pagination">
        @if ($inspections instanceof \Illuminate\Pagination\AbstractPaginator)
            {{ $inspections->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
