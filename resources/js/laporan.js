document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('laporan-data');
    if (!container) return;
    const facilityPetugas = JSON.parse(container.dataset.petugas || '{}');
    const busyPetugasIds = JSON.parse(container.dataset.busyPetugas || '[]');

    document.querySelectorAll('.lihat-foto-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.foto;
            const nama = this.dataset.nama;
            if (!url) return;
            const dark = document.documentElement.getAttribute('data-theme') === 'dark';
            Swal.fire({
                title: 'Foto Bukti - ' + nama,
                html: '<img src="' + url + '" class="img-fluid rounded swal-img-maxh" alt="Foto">',
                background: dark ? '#1e293b' : '#fff',
                color: dark ? '#e2e8f0' : '#0f172a',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: { popup: 'rounded-4' }
            });
        });
    });

    document.querySelectorAll('.kirim-petugas-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const facilityId = this.dataset.facilityId;
            const nama = this.dataset.nama;
            const petugasTerpilih = this.dataset.petugasId;
            const dark = document.documentElement.getAttribute('data-theme') === 'dark';

            const petugasList = facilityPetugas[facilityId] || [];

            let optionsHtml = '<option value="">— Pilih Petugas —</option>';
            let hasReady = false;
            petugasList.forEach(p => {
                if (petugasTerpilih && String(p.id) === String(petugasTerpilih)) return;
                const isBusy = busyPetugasIds.includes(p.id);
                const statusLabel = isBusy ? 'Sibuk' : (p.status === 'ready' ? '✓ Ready' : 'Sibuk');
                const disabled = isBusy || p.status !== 'ready' ? 'disabled' : '';
                if (!isBusy && p.status === 'ready') hasReady = true;
                optionsHtml += `<option value="${p.id}" ${disabled}>${p.name} (${statusLabel})</option>`;
            });

            if (!hasReady) {
                optionsHtml = '<option value="">— Tidak ada petugas ready —</option>';
            }

            Swal.fire({
                title: 'Kirim ke Petugas - ' + nama,
                html: `
                    <form id="form-kirim-${id}" class="text-start">
                        <div class="mb-3">
                            <label class="swal-form-label">Pilih Petugas Penanggung Jawab Fasilitas</label>
                            <select class="swal-form-select" name="petugas_id" required>
                                ${optionsHtml}
                            </select>
                        </div>
                    </form>
                `,
                background: dark ? '#1e293b' : '#fff',
                color: dark ? '#e2e8f0' : '#0f172a',
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                confirmButtonColor: '#1a56db',
                cancelButtonText: 'Batal',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                customClass: { popup: 'rounded-4' },
                preConfirm: () => {
                    const select = document.querySelector('#form-kirim-' + id + ' select[name="petugas_id"]');
                    if (!select.value) {
                        Swal.showValidationMessage('Pilih petugas terlebih dahulu');
                        return false;
                    }
                    const formData = new FormData();
                    formData.append('_token', container.dataset.token);
                    formData.append('petugas_id', select.value);
                    return fetch('/laporan/' + id + '/kirim', {
                        method: 'POST',
                        body: new URLSearchParams(formData),
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    }).then(res => {
                        if (res.ok) window.location.reload();
                        else throw new Error('Gagal');
                    });
                }
            });
        });
    });
});
