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
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @php
                                $foto = $facility->foto_after ?? $facility->foto_before;
                            @endphp
                            @if ($foto)
                                <img src="{{ str_starts_with($foto, 'uploads/') ? asset($foto) : asset('storage/' . $foto) }}"
                                    alt="Foto" class="facility-icon-xs"
                                    style="object-fit: cover; border-radius: 9px; border: 1px solid var(--adm-border);">
                            @else
                                <div class="facility-icon-xs">
                                    <span class="material-symbols-outlined">medical_services</span>
                                </div>
                            @endif
                            <div>
                                <div style="font-size:0.84rem;font-weight:700;color:#0f172a;">
                                    {{ $facility->nama_fasilitas }}</div>
                                <div style="font-size:0.7rem;color:#94a3b8;">
                                    FAC-{{ str_pad($facility->id, 4, '0', STR_PAD_LEFT) }} · Petugas:
                                    {{ $facility->petugas ? $facility->petugas->name : '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:0.82rem;font-weight:600;color:#0f172a;">{{ $facility->lokasi }}</div>
                        <div style="font-size:0.7rem;color:#94a3b8;text-transform:capitalize;">
                            {{ $facility->jenis_fasilitas }}</div>
                    </td>
                    <td style="font-size:0.78rem;color:#64748b;white-space:nowrap;">
                        <span class="material-symbols-outlined align-middle me-1"
                            style="font-size:0.85rem;">calendar_month</span>{{ $dateStr }}
                    </td>
                    <td>
                        @if ($status === 'bersih')
                            <span class="badge-status badge-compliant">Layak</span>
                        @elseif($status === 'perlu dibersihkan')
                            <span class="badge-status badge-review">Perlu Dibersihkan</span>
                        @else
                            <span class="badge-status badge-critical">Tidak Layak</span>
                        @endif
                    </td>
                    @if (Auth::user()->role === 'admin')
                        <td>
                            <div class="dropdown">
                                <button class="btn-icon" data-bs-toggle="dropdown" title="Aksi">
                                    <span class="material-symbols-outlined">more_vert</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end rounded-3 border shadow-sm"
                                    style="min-width:160px;">
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center gap-2 py-2"
                                            style="font-size:0.82rem;" onclick='openEditModal({!! json_encode([
                                                'id' => $facility->id,
                                                'nama_fasilitas' => $facility->nama_fasilitas,
                                                'jenis_fasilitas' => $facility->jenis_fasilitas,
                                                'petugas_id' => $facility->penanggung_jawab,
                                                'lokasi' => $facility->lokasi,
                                                'status_aktif' => $facility->status_aktif,
                                            ]) !!})'>
                                            <span class="material-symbols-outlined"
                                                style="font-size:0.95rem;color:#64748b;">edit</span> Ubah Fasilitas
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
                                                class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger btn-delete"
                                                style="font-size:0.82rem;">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:0.95rem;">delete</span> Hapus
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
                        style="padding:48px;text-align:center;color:#94a3b8;font-size:0.84rem;">
                        <span class="material-symbols-outlined d-block mb-2" style="font-size:2rem;">domain</span>
                        Belum ada data fasilitas.
                    </td>
                </tr>
            @endforelse
            @if (count($facilities) > 0)
                @for ($i = count($facilities); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="{{ Auth::user()->role === 'admin' ? 5 : 4 }}" style="height: 69px; border: none;">
                            &nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">Menampilkan {{ $facilities->firstItem() ?? 0 }}–{{ $facilities->lastItem() ?? 0 }} dari
        {{ $facilities->total() }} fasilitas</span>
    <div class="ajax-pagination">{{ $facilities->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
</div>
