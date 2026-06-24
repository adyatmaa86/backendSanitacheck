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
                                <div style="font-size:0.84rem;font-weight:700;color:var(--adm-text);">{{ $a->name }}</div>
                                <div style="font-size:0.7rem;color:var(--adm-muted);">Role: Admin</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:0.82rem;font-weight:600;color:var(--adm-text); display:flex; align-items:center; gap:5px;">
                            <span class="material-symbols-outlined" style="font-size:0.95rem; color:var(--adm-muted);">mail</span>
                            {{ $a->email }}
                        </div>
                    </td>
                    <td style="font-size:0.78rem;color:var(--adm-muted);white-space:nowrap;">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size:0.85rem;">calendar_month</span>
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
                    <td colspan="4" style="padding:48px;text-align:center;color:var(--adm-muted);font-size:0.84rem;">
                        <span class="material-symbols-outlined d-block mb-2" style="font-size:2rem;">admin_panel_settings</span>
                        Belum ada admin terdaftar.
                    </td>
                </tr>
            @endforelse
            @if(count($admins) > 0)
                @for ($i = count($admins); $i < 5; $i++)
                    <tr class="empty-row">
                        <td colspan="4" style="height: 69px; border: none;">&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="data-card-footer">
    <span class="page-info">Menampilkan {{ $admins->firstItem() ?? 0 }}–{{ $admins->lastItem() ?? 0 }} dari {{ $admins->total() }} admin</span>
    <div class="ajax-pagination">{{ $admins->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
</div>
