document.addEventListener('DOMContentLoaded', function() {
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
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => { tableContainer.innerHTML = html; bindPagination(); })
        .catch(error => console.error('Error fetching petugas:', error));
    }

    function bindPagination() {
        if (!tableContainer) return;
        tableContainer.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchPetugas(new URL(this.href).searchParams.get('page'));
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

    /* -- Terima Laporan page -- */
    function isDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    document.querySelectorAll('.lihat-foto-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.foto;
            const nama = this.dataset.nama;
            if (!url) return;
            const dark = isDark();
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

    document.querySelectorAll('.terima-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const dark = isDark();
            Swal.fire({
                title: 'Terima Laporan?',
                text: 'Laporan ini akan masuk ke Tugas Saya dan status Anda menjadi aktif.',
                icon: 'question',
                background: dark ? '#1e293b' : '#fff',
                color: dark ? '#e2e8f0' : '#0f172a',
                showCancelButton: true,
                confirmButtonColor: '#1a56db',
                confirmButtonText: 'Ya, Terima',
                cancelButtonColor: '#64748b',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('terima-form-' + id).submit();
                }
            });
        });
    });
});
