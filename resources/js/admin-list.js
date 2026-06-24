// Admin List Page Javascript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const tableContainer = document.getElementById('tableContainer');
    
    let timeout = null;

    function fetchAdmins(page = '') {
        if (!form || !searchInput || !tableContainer) return;
        const searchVal = searchInput.value;
        
        let url = new URL(form.action);
        if (searchVal) url.searchParams.set('search', searchVal);
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
        .catch(error => console.error('Error fetching admins:', error));
    }

    function bindPagination() {
        if (!tableContainer) return;
        const paginationLinks = tableContainer.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const urlObj = new URL(this.href);
                const page = urlObj.searchParams.get('page');
                fetchAdmins(page);
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetchAdmins();
            }, 300);
        });
    }

    bindPagination();
});
