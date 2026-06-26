<div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr>
                <th>Nama Admin</th>
                <th>Email</th>
                <th>Tanggal Terdaftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $a)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-xs">
                                {{ strtoupper(substr($a->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fs-8 fw-bold text-adm-text">{{ $a->name }}</div>
                                <div class="fs-13 text-adm-muted">Role: Admin</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fs-9 fw-semibold text-adm-text d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined icon-sm text-adm-muted">mail</span>
                            {{ $a->email }}
                        </div>
                    </td>
                    <td class="fs-10 text-adm-muted text-nowrap">
                        <span class="material-symbols-outlined align-middle me-1 fs-85"> calendar_month</span>
                        {{ $a->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <form action="{{ route('admin.destroy', $a->id) }}" method="POST" class="delete-form m-0" data-title="Hapus Admin" data-text="Apakah Anda yakin ingin menghapus admin ini?">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-icon danger btn-delete" title="Hapus Admin">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty-td">
                        <span class="material-symbols-outlined d-block mb-2 icon-3xl">admin_panel_settings</span>
                        Belum ada admin terdaftar.
                    </td>
                </tr>
            @endforelse
            @if(count($admins) > 0)
                @for ($i = count($admins); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="4" class="h-empty border-0">&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">
        @if ($admins instanceof \Illuminate\Pagination\AbstractPaginator)
            Menampilkan {{ $admins->firstItem() ?? 0 }}–{{ $admins->lastItem() ?? 0 }} dari {{ $admins->total() }} admin
        @else
            Total {{ count($admins) }} admin
        @endif
    </span>
    <div class="ajax-pagination">
        @if ($admins instanceof \Illuminate\Pagination\AbstractPaginator)
            {{ $admins->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
