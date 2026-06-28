document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('history-data');
    const token = container ? container.dataset.token : '';

    function bindPagination() {
        const links = document.querySelectorAll('.ajax-pagination .pagination a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const dataCard = document.querySelector('.data-card');
                    if (dataCard) {
                        dataCard.innerHTML = html;
                        bindPagination();
                    }
                })
                .catch(err => console.error(err));
            });
        });
    }

    function isDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    document.querySelectorAll('.edit-inspeksi-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const dark = isDark();

            fetch('/inspections/' + id + '/edit')
                .then(res => res.json())
                .then(data => {
                    const kondisi = data.kondisi_kebersihan;
                    const air = data.ketersediaan_air;
                    const sabun = data.ketersediaan_sabun;
                    const bau = data.bau_tidak_sedap;
                    const catatan = data.catatan || '';
                    const tindakLanjut = data.status_tindak_lanjut;
                    const namaFasilitas = data.facility?.nama_fasilitas || 'Fasilitas';

                    Swal.fire({
                        title: 'Edit Inspeksi - ' + namaFasilitas,
                        width: '600px',
                        html: `
                            <form id="form-edit-${id}" class="text-start">
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="swal-form-label">Kondisi Kebersihan</label>
                                        <select class="swal-form-select" name="kondisi_kebersihan">
                                            <option value="baik"  ${kondisi === 'baik'  ? 'selected' : ''}>Baik</option>
                                            <option value="cukup" ${kondisi === 'cukup' ? 'selected' : ''}>Cukup</option>
                                            <option value="buruk" ${kondisi === 'buruk' ? 'selected' : ''}>Buruk</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="swal-form-label">Tindak Lanjut</label>
                                        <select class="swal-form-select" name="status_tindak_lanjut">
                                            <option value="aman"               ${tindakLanjut === 'aman'               ? 'selected' : ''}>Aman</option>
                                            <option value="perlu dibersihkan" ${tindakLanjut === 'perlu dibersihkan' ? 'selected' : ''}>Perlu Dibersihkan</option>
                                            <option value="perlu perbaikan"   ${tindakLanjut === 'perlu perbaikan'   ? 'selected' : ''}>Perlu Perbaikan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-4">
                                        <label class="swal-form-label">Air</label>
                                        <select class="swal-form-select" name="ketersediaan_air">
                                            <option value="tersedia" ${air === 'tersedia' ? 'selected' : ''}>Tersedia</option>
                                            <option value="tidak"   ${air === 'tidak'   ? 'selected' : ''}>Tidak</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="swal-form-label">Sabun</label>
                                        <select class="swal-form-select" name="ketersediaan_sabun">
                                            <option value="tersedia" ${sabun === 'tersedia' ? 'selected' : ''}>Tersedia</option>
                                            <option value="tidak"   ${sabun === 'tidak'   ? 'selected' : ''}>Tidak</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="swal-form-label">Bau Tidak Sedap</label>
                                        <select class="swal-form-select" name="bau_tidak_sedap">
                                            <option value="ya"   ${bau === 'ya'   ? 'selected' : ''}>Ya</option>
                                            <option value="tidak" ${bau === 'tidak' ? 'selected' : ''}>Tidak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="swal-form-label">Catatan</label>
                                    <textarea class="swal-form-textarea" name="catatan" rows="2">${catatan}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="swal-form-label">Foto Baru (opsional)</label>
                                    <input type="file" class="swal-form-input" name="foto" accept="image/*">
                                </div>
                            </form>
                        `,
                        background: dark ? '#1e293b' : '#fff',
                        color: dark ? '#e2e8f0' : '#0f172a',
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        confirmButtonColor: '#1a56db',
                        cancelButtonText: 'Batal',
                        cancelButtonColor: '#64748b',
                        reverseButtons: true,
                        customClass: { popup: 'rounded-4' },
                        preConfirm: () => {
                            const form = document.getElementById('form-edit-' + id);
                            const formData = new FormData(form);
                            formData.append('_method', 'PUT');
                            formData.append('_token', token);
                            return fetch('/inspections/' + id, {
                                method: 'POST',
                                body: formData,
                            }).then(res => {
                                if (res.ok) window.location.reload();
                                else return res.json().then(err => { throw new Error(err.message || 'Gagal'); });
                            });
                        }
                    });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat memuat data inspeksi.',
                        background: dark ? '#1e293b' : '#fff',
                        color: dark ? '#e2e8f0' : '#0f172a',
                    });
                });
        });
    });

    bindPagination();
});
