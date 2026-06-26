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
    if (editPetugas) editPetugas.value = facility.penanggung_jawab || facility.petugas_id;
    if (editLokasi) editLokasi.value = facility.lokasi;
    if (editStatus) editStatus.checked = facility.status_aktif == 1;

    const modalEl = document.getElementById('edit-facility-modal');
    if (modalEl) {
        // Re-init petugas tambahan chips with existing data
        const tambahanIds = facility.petugas_tambahan_ids || [];
        // Destroy previous instance if exists
        if (modalEl._tambahanInited) {
            // just re-render
        }
        initPetugasTambahan('edit', tambahanIds);
        modalEl.dataset.tambahanInited = '1';
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

/* ---- PETUGAS TAMBAHAN (chips & dropdown) ---- */
window._tambahanState = {};

window.initPetugasTambahan = function(prefix, selectedIds = []) {
    const wrapper = document.getElementById(prefix + '-tambahan-wrapper');
    const chipsContainer = document.getElementById(prefix + '-tambahan-chips');
    const addBtn = document.getElementById(prefix + '-add-tambahan-btn');
    const dropdown = document.getElementById(prefix + '-tambahan-dropdown');
    const hiddenContainer = document.getElementById(prefix + '-tambahan-hidden-container');

    if (!wrapper) return;

    if (window._tambahanState[prefix]) {
        window._tambahanState[prefix].selected = [...selectedIds];
        window._tambahanState[prefix].renderChips();
        window._tambahanState[prefix].updateHidden();
        return;
    }

    let selected = [...selectedIds];

    function getPjId() {
        const pjSelect = prefix === 'create'
            ? document.querySelector('#new-facility-modal select[name="petugas_id"]')
            : document.getElementById('edit_petugas');
        return pjSelect ? parseInt(pjSelect.value) : null;
    }

    function renderChips() {
        chipsContainer.innerHTML = '';
        selected.forEach(function(id) {
            const petugas = (window._allPetugas || []).find(p => p.id === id);
            if (!petugas) return;
            const chip = document.createElement('span');
            chip.className = 'd-inline-flex align-items-center gap-1 px-2 py-1 rounded-2';
            chip.style.cssText = 'font-size:0.75rem;font-weight:600;background:var(--adm-primary);color:#fff;border:1px solid var(--adm-primary);';
            chip.innerHTML = `${petugas.name} <span class="material-symbols-outlined" style="font-size:0.85rem;cursor:pointer;color:rgba(255,255,255,0.8);">close</span>`;
            chip.querySelector('.material-symbols-outlined').addEventListener('click', function(e) {
                e.stopPropagation();
                selected = selected.filter(s => s !== id);
                renderChips();
                updateHidden();
                closeDropdown();
            });
            chipsContainer.appendChild(chip);
        });
        updateHidden();
    }

    function buildDropdownItems() {
        const pjId = getPjId();
        dropdown.innerHTML = '';
        const available = (window._allPetugas || []).filter(p => p.id !== pjId && !selected.includes(p.id));
        if (available.length === 0) {
            dropdown.innerHTML = '<div class="px-3 py-2 text-muted" style="font-size:0.78rem;">Semua petugas sudah ditambahkan</div>';
            return;
        }
        available.forEach(function(p) {
            const item = document.createElement('div');
            item.className = 'px-3 py-2';
            item.style.cssText = 'font-size:0.82rem;cursor:pointer;color:var(--adm-text);transition:background 0.15s;';
            item.textContent = p.name;
            item.addEventListener('mouseenter', function() { this.style.background = 'var(--adm-bg)'; });
            item.addEventListener('mouseleave', function() { this.style.background = 'transparent'; });
            item.addEventListener('click', function() {
                selected.push(p.id);
                renderChips();
                updateHidden();
                closeDropdown();
            });
            dropdown.appendChild(item);
        });
    }

    function openDropdown() {
        buildDropdownItems();
        dropdown.style.display = 'block';
    }

    function closeDropdown() {
        dropdown.style.display = 'none';
    }

    function updateHidden() {
        hiddenContainer.innerHTML = '';
        if (selected.length === 0) {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = '_clear_tambahan';
            inp.value = '1';
            hiddenContainer.appendChild(inp);
        } else {
            selected.forEach(function(id) {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'petugas_tambahan_ids[]';
                inp.value = id;
                hiddenContainer.appendChild(inp);
            });
        }
    }

    addBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (dropdown.style.display === 'block') {
            closeDropdown();
        } else {
            openDropdown();
        }
    });

    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            closeDropdown();
        }
    });

    const pjSelect = prefix === 'create'
        ? document.querySelector('#new-facility-modal select[name="petugas_id"]')
        : document.getElementById('edit_petugas');
    if (pjSelect) {
        pjSelect.addEventListener('change', function() {
            const newPjId = parseInt(this.value);
            selected = selected.filter(s => s !== newPjId);
            renderChips();
            updateHidden();
        });
    }

    renderChips();

    window._tambahanState[prefix] = {
        selected: selected,
        renderChips: renderChips,
        updateHidden: updateHidden,
        closeDropdown: closeDropdown,
    };
};

function initCreateTambahan() {
    const createModal = document.getElementById('new-facility-modal');
    if (createModal) {
        createModal.addEventListener('shown.bs.modal', function() {
            if (!createModal.dataset.tambahanInited) {
                initPetugasTambahan('create', []);
                createModal.dataset.tambahanInited = '1';
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCreateTambahan);
} else {
    initCreateTambahan();
}

