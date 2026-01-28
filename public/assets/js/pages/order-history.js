document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchHistory');
    const statusSelect = document.getElementById('statusHistory');
    const limitSelect = document.getElementById('limitHistory');

    const modalContent = document.getElementById('modalContent');
    const modalIdSpan = document.getElementById('modalOrderId');

    // --- 1. FILTER LOGIC ---
    if (statusSelect && limitSelect) {
        [statusSelect, limitSelect].forEach(element => {
            element.addEventListener('change', () => filterForm.submit());
        });
    }

    let typingTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                filterForm.submit();
            }, 700);
        });

        // Autofocus ke akhir teks
        if (searchInput.value.length > 0) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }
    }

    // --- 2. AJAX MODAL DETAIL (DENGAN EVENT DELEGATION) ---
    // Kita pasang event listener di document agar tombol di halaman 2, 3, dst tetap jalan
    document.addEventListener('click', function (e) {
        // Cek apakah yang diklik adalah tombol detail atau ikon di dalamnya
        const button = e.target.closest('.btn-detail');

        if (button) {
            const orderId = button.getAttribute('data-id');

            // Set Header Modal
            if (modalIdSpan) modalIdSpan.innerText = '#' + orderId;

            // Tampilkan Loading
            modalContent.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 small text-muted">Mengambil data detail...</p>
                </div>
            `;

            // Hit API
            fetch(`/cashier/history/${orderId}/detail`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(html => {
                    modalContent.innerHTML = html;
                })
                .catch(err => {
                    modalContent.innerHTML = `
                        <div class="alert alert-danger m-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Gagal memuat detail. Pastikan Route sudah benar.
                        </div>
                    `;
                    console.error('Fetch Error:', err);
                });
        }
    });
});
