<div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr>
                <th>Nama Fasilitas</th>
                <th>Lokasi</th>
                <th>Inspeksi Terakhir</th>
                <th>Status</th>
                @if (Auth::user()->role === 'admin')
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($facilities as $facility)
                @php
                    $latestInspection = $facility->latestInspection;
                    $dateStr = $latestInspection
                        ? $latestInspection->tanggal_inspeksi->format('d M Y')
                        : 'Belum pernah';
                    $status = $facility->cleanliness_status;
                    $_editPayload = [
                        'id' => $facility->id,
                        'nama_fasilitas' => $facility->nama_fasilitas,
                        'jenis_fasilitas' => $facility->jenis_fasilitas,
                        'penanggung_jawab' => $facility->penanggung_jawab,
                        'petugas_id' => $facility->penanggung_jawab,
                        'petugas_tambahan_ids' => $facility->petugasTambahan->pluck('id'),
                        'lokasi' => $facility->lokasi,
                        'status_aktif' => $facility->status_aktif,
                    ];
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @php
                                $foto = $facility->foto_before ?? $facility->foto_after;
                            @endphp
                            @if ($foto)
                                <img src="{{ str_starts_with($foto, 'uploads/') ? asset($foto) : asset('storage/' . $foto) }}"
                                    alt="Foto" class="facility-icon-xs object-fit-cover rounded-9 border-adm">
                            @else
                                <div class="facility-icon-xs">
                                    <span class="material-symbols-outlined">medical_services</span>
                                </div>
                            @endif
                            <div>
                                <div class="facility-name">
                                    {{ $facility->nama_fasilitas }}</div>
                                <div class="facility-meta">
                                    FAC-{{ str_pad($facility->id, 4, '0', STR_PAD_LEFT) }} · PJ:
                                    {{ $facility->petugas ? $facility->petugas->name : '-' }}
                                    @if($facility->petugasTambahan->isNotEmpty())
                                        <br><span class="fs-15">+{{ $facility->petugasTambahan->pluck('name')->implode(', ') }}</span>
                                    @endif
                                    </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="officer-name fw-semibold">{{ $facility->lokasi }}</div>
                        <div class="facility-meta text-capitalize">
                            {{ $facility->jenis_fasilitas }}</div>
                    </td>
                    <td class="timestamp-text">
                        <span class="material-symbols-outlined align-middle me-1 fs-85">calendar_month</span>{{ $dateStr }}
                    </td>
                    <td>
                        @if ($status === 'bersih')
                            <span class="badge-status badge-compliant">
                                Bersih & Aman</span>
                        @elseif($status === 'perlu dibersihkan')
                            <span class="badge-status badge-review">Perlu Dibersihkan</span>
                        @elseif($status === 'belum_inspeksi')
                            <span class="badge-status bg-adm text-slate-500">Belum Inspeksi</span>
                        @else
                            <span class="badge-status badge-critical">Perlu Diperbaiki</span>
                        @endif
                    </td>
                    @if (Auth::user()->role === 'admin')
                        <td>
                            <div class="dropdown">
                                <button class="btn-icon" data-bs-toggle="dropdown" title="Aksi">
                                    <span class="material-symbols-outlined">more_vert</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end rounded-3 border shadow-sm min-w-160">
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center gap-2 py-2 fs-9 btn-edit-facility"
                                            data-facility='@json($_editPayload)'>
                                            <span class="material-symbols-outlined icon-sm text-slate-500">edit</span> Ubah Fasilitas
                                        </button>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('facilities.destroy', $facility->id) }}" method="POST"
                                            class="delete-form m-0" data-title="Hapus Fasilitas"
                                            data-text="Apakah Anda yakin ingin menghapus fasilitas ini?">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger btn-delete fs-9">
                                                <span class="material-symbols-outlined icon-sm">delete</span> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ Auth::user()->role === 'admin' ? 5 : 4 }}"
                        class="p-5 text-center text-slate-400 fs-8">
                        <span class="material-symbols-outlined d-block mb-2 icon-2xl">domain</span>
                        Belum ada data fasilitas.
                    </td>
                </tr>
            @endforelse
            @if (count($facilities) > 0)
                @for ($i = count($facilities); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="{{ Auth::user()->role === 'admin' ? 5 : 4 }}">
                            &nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">
        @if ($facilities instanceof \Illuminate\Pagination\AbstractPaginator)
            Menampilkan {{ $facilities->firstItem() ?? 0 }}–{{ $facilities->lastItem() ?? 0 }} dari {{ $facilities->total() }} fasilitas
        @else
            Total {{ count($facilities) }} fasilitas
        @endif
    </span>
    <div class="ajax-pagination">
        @if ($facilities instanceof \Illuminate\Pagination\AbstractPaginator)
            {{ $facilities->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
