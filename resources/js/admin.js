/* =============================================
   SanitaCheck Admin Panel — Custom JS
   Bootstrap 5 compatible (no Tailwind)
   ============================================= */

import Swal from 'sweetalert2';

const swalStyle = document.createElement('style');
swalStyle.textContent = '[data-theme="dark"] .swal2-cancel { color: #fff !important; }';
document.head.appendChild(swalStyle);

document.addEventListener('DOMContentLoaded', () => {

    /* ---- 1. SIDEBAR MOBILE TOGGLE ---- */
    const sidebar  = document.getElementById('adminSidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    function openSidebar() {
        if (sidebar)  sidebar.classList.add('open');
        if (overlay)  overlay.classList.add('active');
    }
    function closeSidebar() {
        if (sidebar)  sidebar.classList.remove('open');
        if (overlay)  overlay.classList.remove('active');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (overlay)   overlay.addEventListener('click', closeSidebar);


    /* ---- 2. SWEETALERT DELETE CONFIRMATION ---- */
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete');
        if (!btn) return;

        const form = btn.closest('.delete-form');
        if (!form) return;

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const title = form.dataset.title || 'Hapus Data';
        const text = form.dataset.text || 'Apakah Anda yakin ingin menghapus data ini?';

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            background: isDark ? '#1e293b' : '#fff',
            color: isDark ? '#e2e8f0' : '#0f172a',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    /* ---- 3. AUTO-DISMISS ALERTS ---- */
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });





    /* ---- 5. BOOTSTRAP MODAL HELPERS ---- */
    // openModal(id) — opens any Bootstrap modal by element id
    window.openModal = function(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        }
    };

    // closeModal(id) — hides any Bootstrap modal by element id
    window.closeModal = function(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            const modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        }
    };


    /* ---- 6. FACILITIES TABLE — DROPDOWN ACTION BUTTONS ---- */
    // Prevent action dropdown from closing the row accidentally
    document.querySelectorAll('.dropdown-toggle').forEach(btn => {
        btn.addEventListener('click', e => e.stopPropagation());
    });


    /* ---- 7. TABLE ROW — SUBTLE CLICK RIPPLE ---- */
    document.querySelectorAll('.table-admin tbody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('button, a, .dropdown, form')) return;
            this.style.background = '#f0f7ff';
            setTimeout(() => { this.style.background = ''; }, 300);
        });
    });


    /* ---- 8. TOPBAR SEARCH SHORTCUT (/) ---- */
    document.addEventListener('keydown', e => {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            const searchInput = document.querySelector('.topbar-search input');
            if (searchInput) searchInput.focus();
        }
    });


    /* ---- 9. STAT CARD — COUNT-UP ANIMATION ---- */
    document.querySelectorAll('.stat-value').forEach(el => {
        const target = parseInt(el.textContent.replace(/[^0-9]/g, ''), 10);
        if (isNaN(target) || target === 0) return;
        let current = 0;
        const step  = Math.max(1, Math.floor(target / 30));
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = current;
        }, 30);
    });


    /* ---- 10.5 NOTIFICATION DROPDOWN — DELETE BUTTON ---- */
    const notifCheckboxes = document.querySelectorAll('.notif-checkbox');
    const deleteNotifBtn = document.getElementById('deleteNotifBtn');
    const deleteNotifText = document.getElementById('deleteNotifText');
    const deleteNotifIcon = document.getElementById('deleteNotifIcon');

    function updateDeleteNotifButton() {
        const checked = document.querySelectorAll('.notif-checkbox:checked').length;
        if (deleteNotifText) {
            deleteNotifText.textContent = checked === 0 ? 'Hapus Semua' : `Hapus Terpilih (${checked})`;
        }
        if (deleteNotifIcon) {
            deleteNotifIcon.textContent = checked === 0 ? 'delete_sweep' : 'delete';
        }
    }

    notifCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteNotifButton);
    });

    if (deleteNotifBtn) {
        deleteNotifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const checked = document.querySelectorAll('.notif-checkbox:checked').length;
            const isHapusSemua = checked === 0;
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

            Swal.fire({
                title: isHapusSemua ? 'Hapus Semua Notifikasi?' : 'Hapus Notifikasi Terpilih?',
                text: isHapusSemua
                    ? 'Semua notifikasi akan dihapus permanen. Lanjutkan?'
                    : `${checked} notifikasi terpilih akan dihapus permanen. Lanjutkan?`,
                icon: 'warning',
                background: isDark ? '#1e293b' : '#fff',
                color: isDark ? '#e2e8f0' : '#0f172a',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('notifForm').submit();
                }
});

        });
    }

    /* ---- 11. DARK MODE THEME TOGGLE ---- */
    const themeToggle = document.getElementById('themeToggle');
    const currentTheme = localStorage.getItem('theme') || 'light';

    if (themeToggle) {
        updateThemeToggleIcon(currentTheme);

        themeToggle.addEventListener('click', () => {
            const theme = document.documentElement.getAttribute('data-theme');
            const newTheme = theme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeToggleIcon(newTheme);
        });
    }

    function updateThemeToggleIcon(theme) {
        if (!themeToggle) return;
        const icon = themeToggle.querySelector('.material-symbols-outlined');
        if (icon) {
            icon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
        }
    }

});

/* ---- 12. BF-CACHE PREVENTION ---- */
window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});

/* ---- 13. PANTAU PETUGAS — AJAX SEARCH & PAGINATION ---- */
window.initPantauFilter = function() {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const tableContainer = document.getElementById('tableContainer');
    
    let timeout = null;

    function fetchPetugas(page = '') {
        if (!form || !searchInput || !tableContainer) return;
        const searchVal = searchInput.value;
        let url = new URL(form.action);
        if (searchVal) url.searchParams.set('search', searchVal);
        if (page) url.searchParams.set('page', page);
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            bindPagination();
        })
        .catch(error => console.error('Error fetching petugas:', error));
    }

    function bindPagination() {
        if (!tableContainer) return;
        tableContainer.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const urlObj = new URL(this.href);
                fetchPetugas(urlObj.searchParams.get('page'));
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => fetchPetugas(), 300);
        });
    }
    bindPagination();
};
