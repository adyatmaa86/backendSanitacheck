<div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr>
                <th>Nama Petugas</th>
                <th>Email</th>
                <th>Fasilitas Sedang Dikerjakan</th>
                <th>Status Pengerjaan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($petugas as $p)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-xs">
                                {{ strtoupper(substr($p->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fs-8 fw-bold text-adm-text">{{ $p->name }}</div>
                                <div class="fs-13 text-adm-muted">Role: Petugas</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fs-9 fw-semibold text-adm-text d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined icon-sm text-adm-muted">mail</span>
                            {{ $p->email }}
                        </div>
                    </td>
                    <td>
                        @if($p->active_inspections && $p->active_inspections->isNotEmpty())
                            <div class="d-flex flex-column gap-2">
                                @foreach($p->active_inspections as $index => $task)
                                    <div class="p-2 rounded border-adm fs-10 bg-adm">
                                        <div class="fw-bold text-adm-text">
                                            #{{ $index + 1 }}. {{ $task->facility->nama_fasilitas }}
                                        </div>
                                        <div class="fs-13 text-adm-muted">
                                            Lokasi: {{ $task->facility->lokasi }} 
                                            <span class="badge bg-warning text-dark ms-1 text-capitalize fs-16 fw-bold">{{ $task->status_tindak_lanjut }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-status badge-review d-inline-flex align-items-center gap-1">
                            <span class="bg-warning rounded-circle nutup-badge"></span>
                            Sedang Bekerja ({{ $p->active_inspections->count() }} Tugas)
                        </span>
                    </td>
                    <td>
                        <span class="text-muted small italic">Hanya petugas yang bisa menyelesaikan</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-td">
                        <span class="material-symbols-outlined d-block mb-2 icon-3xl">engineering</span>
                        Tidak ada petugas yang sedang aktif bekerja.
                    </td>
                </tr>
            @endforelse
            @if(count($petugas) > 0)
                @for ($i = count($petugas); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="5" class="h-empty border-0">&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">
        @if ($petugas instanceof \Illuminate\Pagination\AbstractPaginator)
            Menampilkan {{ $petugas->firstItem() ?? 0 }}–{{ $petugas->lastItem() ?? 0 }} dari {{ $petugas->total() }} petugas aktif
        @else
            Total {{ count($petugas) }} petugas aktif
        @endif
    </span>
    <div class="ajax-pagination">
        @if ($petugas instanceof \Illuminate\Pagination\AbstractPaginator)
            {{ $petugas->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
