// Facilities Management Page JS
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const filterJenis = document.getElementById('filterJenis');
    const tableContainer = document.getElementById('tableContainer');
    
    let timeout = null;

    function fetchFacilities(page = '') {
        if (!form || !searchInput || !filterJenis || !tableContainer) return;
        const searchVal = searchInput.value;
        const jenisVal = filterJenis.value;
        
        let url = new URL(form.action);
        if (searchVal) url.searchParams.set('search', searchVal);
        if (jenisVal) url.searchParams.set('jenis_fasilitas', jenisVal);
        if (page) url.searchParams.set('page', page);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            bindPagination();
        })
        .catch(error => console.error('Error fetching facilities:', error));
    }

    function bindPagination() {
        if (!tableContainer) return;
        const paginationLinks = tableContainer.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const urlObj = new URL(this.href);
                const page = urlObj.searchParams.get('page');
                fetchFacilities(page);
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetchFacilities();
            }, 300);
        });
    }

    if (filterJenis) {
        filterJenis.addEventListener('change', function() {
            fetchFacilities();
        });
    }

    bindPagination();
});

// Global functions for inline click handlers
window.openEditModal = function(facility) {
    const formEl = document.getElementById('edit-facility-form');
    const editNama = document.getElementById('edit_nama');
    const editJenis = document.getElementById('edit_jenis');
    const editPetugas = document.getElementById('edit_petugas');
    const editLokasi = document.getElementById('edit_lokasi');
    const editStatus = document.getElementById('edit_status');

    if (formEl) formEl.action = `/facilities/${facility.id}`;
    if (editNama) editNama.value = facility.nama_fasilitas;
    if (editJenis) editJenis.value = facility.jenis_fasilitas;
    if (editPetugas) editPetugas.value = facility.penanggung_jawab || facility.petugas_id; // penanggung_jawab in table is the petugas_id
    if (editLokasi) editLokasi.value = facility.lokasi;
    if (editStatus) editStatus.checked = facility.status_aktif == 1;

    const modalEl = document.getElementById('edit-facility-modal');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
};

window.toggleEditJenis = function(id, cancel = false) {
    const form = document.getElementById(`form-edit-jenis-${id}`);
    const text = document.getElementById(`text-jenis-${id}`);
    const btnEdit = document.getElementById(`btn-edit-jenis-${id}`);
    const btnSave = document.getElementById(`btn-save-jenis-${id}`);
    const btnCancel = document.getElementById(`btn-cancel-jenis-${id}`);

    if (!form || !text || !btnEdit || !btnSave || !btnCancel) return;

    if (form.classList.contains('d-none') && !cancel) {
        form.classList.remove('d-none');
        text.classList.add('d-none');
        btnEdit.classList.add('d-none');
        btnSave.classList.remove('d-none');
        btnCancel.classList.remove('d-none');
    } else {
        form.classList.add('d-none');
        text.classList.remove('d-none');
        btnEdit.classList.remove('d-none');
        btnSave.classList.add('d-none');
        btnCancel.classList.add('d-none');
    }
};
