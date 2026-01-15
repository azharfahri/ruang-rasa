document.addEventListener('DOMContentLoaded', () => {

    /* ===============================
       INVENTORY TABLE LOGIC
    =============================== */
    const rows = [...document.querySelectorAll('#inventoryTable tbody tr')];
    const searchInput = document.getElementById('searchInput');
    const limitSelect = document.getElementById('limitSelect');
    const info = document.getElementById('tableInfo');
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    let page = 1;

    function renderTable() {
        // Cek jika tabel kosong (forelse @empty)
        if (!rows.length || rows[0].querySelector('td[colspan]')) return;

        const keyword = searchInput?.value.toLowerCase() || "";
        const limit = parseInt(limitSelect?.value) || 10;

        const filtered = rows.filter(row => {
            const productName = row.querySelector('.product-name');
            // FIX: Gunakan optional chaining (?.) agar tidak error jika .product-name tidak ditemukan
            return productName?.innerText.toLowerCase().includes(keyword);
        });

        const totalPages = Math.ceil(filtered.length / limit) || 1;
        page = Math.min(page, totalPages);

        rows.forEach(row => row.style.display = 'none');

        const start = (page - 1) * limit;
        const end = start + limit;

        filtered.slice(start, end).forEach(row => row.style.display = '');

        if (info) info.innerText = `Menampilkan ${filtered.length} dari ${rows.length} data`;
        if (pageInfo) pageInfo.innerText = `${page} / ${totalPages}`;

        if (prevBtn) prevBtn.disabled = page === 1;
        if (nextBtn) nextBtn.disabled = page === totalPages;
    }

    // Event Listeners dengan pengecekan elemen (null check)
    searchInput?.addEventListener('input', () => { page = 1; renderTable(); });
    limitSelect?.addEventListener('change', () => { page = 1; renderTable(); });
    prevBtn?.addEventListener('click', () => { page--; renderTable(); });
    nextBtn?.addEventListener('click', () => { page++; renderTable(); });

    renderTable();

    /* ===============================
       MODAL LOGIC
    =============================== */
    const modal = document.getElementById('addProductModal');
    if (!modal) return;

    const submitBtn = modal.querySelector('button[type="submit"]');

    // LOGIC CHECKBOX
    modal.addEventListener('change', function (e) {
        if (!e.target.classList.contains('product-checkbox')) return;

        const checkbox = e.target;
        const row = checkbox.closest('tr');
        const inputs = row.querySelectorAll('.stock-input, .price-input');

        if (checkbox.checked) {
            inputs.forEach(input => {
                input.disabled = false;

                // REVISI: Hanya stok yang wajib (required), harga tidak.
                if (input.classList.contains('stock-input')) {
                    input.required = true;
                } else {
                    input.required = false; // Memastikan harga tetap opsional
                }
            });
            row.querySelector('.stock-input')?.focus();
            row.classList.add('table-primary');
        } else {
            inputs.forEach(input => {
                input.disabled = true;
                input.required = false;
                input.value = '';
            });
            row.classList.remove('table-primary');
        }

        const anyChecked = modal.querySelectorAll('.product-checkbox:checked').length > 0;
        if (submitBtn) submitBtn.disabled = !anyChecked;
    });

    // RESET MODAL SAAT DITUTUP
    modal.addEventListener('hidden.bs.modal', function () {
        const form = modal.querySelector('form');
        if (form) form.reset();

        modal.querySelectorAll('.stock-input, .price-input').forEach(input => {
            input.disabled = true;
            input.required = false;
        });

        modal.querySelectorAll('tr').forEach(row => row.classList.remove('table-primary'));

        // Jangan reset 'page' ke 1 di sini jika sedang melihat tabel inventory utama
        // kecuali jika yang dipaginasi adalah tabel di dalam modal.
    });
});
