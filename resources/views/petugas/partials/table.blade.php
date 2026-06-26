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
                    $facilitiesCount = \App\Models\Fasilitas::where('penanggung_jawab', $p->id)->count()
                        + \App\Models\Fasilitas::whereHas('petugasTambahan', fn($q) => $q->where('user_id', $p->id))->count();
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-xs">
                                {{ strtoupper(substr($p->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fs-8 fw-bold text-adm">{{ $p->name }}</div>
                                <div class="fs-13 text-adm-muted">Role: Petugas</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fs-9 fw-semibold text-adm d-flex align-items-center gap-5px">
                            <span class="material-symbols-outlined icon-sm text-adm-muted">mail</span>
                            {{ $p->email }}
                        </div>
                    </td>
                    <td class="fs-10 text-adm-muted text-nowrap">
                        <span class="material-symbols-outlined align-middle me-1 fs-85">calendar_month</span>
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
                <tr class="empty-table">
                    <td colspan="5">
                        <span class="material-symbols-outlined d-block mb-2 icon-2xl">badge</span>
                        Belum ada petugas terdaftar.
                    </td>
                </tr>
            @endforelse
            @if(count($petugas) > 0)
                @for ($i = count($petugas); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="5">&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">
        @if ($petugas instanceof \Illuminate\Pagination\AbstractPaginator)
            Menampilkan {{ $petugas->firstItem() ?? 0 }}–{{ $petugas->lastItem() ?? 0 }} dari {{ $petugas->total() }} petugas
        @else
            Total {{ count($petugas) }} petugas
        @endif
    </span>
    <div class="ajax-pagination">
        @if ($petugas instanceof \Illuminate\Pagination\AbstractPaginator)
            {{ $petugas->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
