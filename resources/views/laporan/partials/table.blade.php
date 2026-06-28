<div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr>
                <th>Pelapor</th>
                <th>Fasilitas</th>
                <th>Keluhan</th>
                <th>Foto</th>
                <th>Petugas</th>
                <th>Tanggal</th>
                <th>Status</th>
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
                    <td class="fs-10 text-slate-500 text-truncate max-w-180" title="{{ $lapor->keluhan }}">
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
                    <td>
                        @if($lapor->petugas)
                            <div class="avatar-chip">
                                <div class="avatar-xs">{{ strtoupper(substr($lapor->petugas->name, 0, 2)) }}</div>
                                <span class="officer-name">{{ $lapor->petugas->name }}</span>
                            </div>
                        @else
                            <span class="text-muted fs-10">—</span>
                        @endif
                    </td>
                    <td class="timestamp-text">
                        {{ $lapor->created_at->format('d M Y, H:i') }}
                    </td>
                    <td>
                        @if($lapor->status === 'pending')
                            <span class="badge-status badge-review">Pending</span>
                        @elseif($lapor->status === 'diproses')
                            <span class="badge-status badge-clean">Diproses</span>
                        @elseif($lapor->status === 'selesai')
                            <span class="badge-status badge-compliant">Selesai</span>
                        @else
                            <span class="badge-status badge-critical">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($lapor->status === 'pending')
                                <button type="button" class="btn-icon primary kirim-petugas-btn"
                                    data-id="{{ $lapor->id }}"
                                    data-facility-id="{{ $lapor->fasilitas_id }}"
                                    data-nama="{{ $lapor->nama_pelapor }}"
                                    data-petugas-id="{{ $lapor->petugas_id }}"
                                    title="Kirim ke Petugas">
                                    <span class="material-symbols-outlined text-blue-primary">send</span>
                                </button>
                            @endif
                            <form action="{{ route('laporan.destroy', $lapor->id) }}" method="POST" class="delete-form m-0" data-title="Hapus Laporan" data-text="Apakah Anda yakin ingin menghapus laporan ini?">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger btn-delete" title="Hapus">
                                    <span class="material-symbols-outlined text-red-primary">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="empty-table">
                    <td colspan="8">
                        <span class="material-symbols-outlined d-block mb-2 icon-2xl">inbox</span>
                        Belum ada laporan masuk.
                    </td>
                </tr>
            @endforelse
            @if(count($laporans) > 0)
                @for ($i = count($laporans); $i < 5; $i++)
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
        @if ($laporans instanceof \Illuminate\Pagination\AbstractPaginator)
            Menampilkan {{ $laporans->firstItem() ?? 0 }}–{{ $laporans->lastItem() ?? 0 }} dari {{ $laporans->total() }} laporan
        @else
            Total {{ count($laporans) }} laporan
        @endif
    </span>
    <div class="ajax-pagination">
        @if ($laporans instanceof \Illuminate\Pagination\AbstractPaginator)
            {{ $laporans->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
