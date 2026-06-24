<div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr>
                <th>Nama Petugas</th>
                <th>Email</th>
                <th>Tanggal Terdaftar</th>
                <th>Fasilitas Dikelola</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($petugas as $p)
                @php
                    $facilitiesCount = \App\Models\Fasilitas::where('penanggung_jawab', $p->id)->count();
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-xs">
                                {{ strtoupper(substr($p->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:0.84rem;font-weight:700;color:var(--adm-text);">{{ $p->name }}</div>
                                <div style="font-size:0.7rem;color:var(--adm-muted);">Role: Petugas</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:0.82rem;font-weight:600;color:var(--adm-text); display:flex; align-items:center; gap:5px;">
                            <span class="material-symbols-outlined" style="font-size:0.95rem; color:var(--adm-muted);">mail</span>
                            {{ $p->email }}
                        </div>
                    </td>
                    <td style="font-size:0.78rem;color:var(--adm-muted);white-space:nowrap;">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size:0.85rem;">calendar_month</span>
                        {{ $p->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <span class="badge-status {{ $facilitiesCount > 0 ? 'badge-compliant' : 'badge-review' }}">
                            {{ $facilitiesCount }} Fasilitas
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('petugas.destroy', $p->id) }}" method="POST" class="delete-form m-0" data-title="Hapus Petugas" data-text="Apakah Anda yakin ingin menghapus petugas ini? Semua penanggung jawab fasilitas yang dikelola oleh petugas ini akan dikosongkan.">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-icon danger btn-delete" title="Hapus Petugas">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:48px;text-align:center;color:var(--adm-muted);font-size:0.84rem;">
                        <span class="material-symbols-outlined d-block mb-2" style="font-size:2rem;">badge</span>
                        Belum ada petugas terdaftar.
                    </td>
                </tr>
            @endforelse
            @if(count($petugas) > 0)
                @for ($i = count($petugas); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="5" style="height: 69px; border: none;">&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">Menampilkan {{ $petugas->firstItem() ?? 0 }}–{{ $petugas->lastItem() ?? 0 }} dari {{ $petugas->total() }} petugas</span>
    <div class="ajax-pagination">{{ $petugas->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
</div>
