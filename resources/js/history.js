// History Page AJAX pagination
document.addEventListener('DOMContentLoaded', function() {
    function bindPagination() {
        const links = document.querySelectorAll('.ajax-pagination .pagination a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetch(this.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
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

    bindPagination();
});
