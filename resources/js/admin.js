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


    /* ---- 4. BAR CHART — HOVER TOOLTIP ---- */
    document.querySelectorAll('.bar-col').forEach(col => {
        const bar  = col.querySelector('.bar');
        const lbl  = col.querySelector('.bar-label');
        if (!bar) return;

        const height = parseInt(bar.style.height || '0');

        bar.addEventListener('mouseenter', () => {
            let tip = col.querySelector('.bar-tip');
            if (!tip) {
                tip = document.createElement('div');
                tip.className = 'bar-tip';
                col.appendChild(tip);
            }
            tip.textContent = height + '%';
            tip.style.opacity = '1';
        });
        bar.addEventListener('mouseleave', () => {
            const tip = col.querySelector('.bar-tip');
            if (tip) tip.style.opacity = '0';
        });
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


    /* ---- 10. DARK MODE THEME TOGGLE ---- */
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
