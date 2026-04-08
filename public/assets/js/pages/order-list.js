document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#orderTable tbody')
    const rows = [...tableBody.querySelectorAll('tr:not(.empty-row)')]

    // Elements
    const searchInput = document.getElementById('searchInput')
    const statusSelect = document.getElementById('statusSelect')
    const limitSelect = document.getElementById('limitSelect')
    const info = document.getElementById('tableInfo')
    const pageInfo = document.getElementById('pageInfo')
    const prevBtn = document.getElementById('prevBtn')
    const nextBtn = document.getElementById('nextBtn')
    const paginationControls = document.getElementById('paginationControls')

    let page = 1

    function render() {
        if (rows.length === 0) {
            info.innerText = 'Menampilkan 0 data';
            if (paginationControls) paginationControls.style.display = 'none';
            return;
        }

        const keyword = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusSelect.value.toUpperCase().trim();
        const limit = parseInt(limitSelect.value);

        // LOGIKA FILTER GANDA (Search & Status)
        const filtered = rows.filter(r => {
            // 1. Ambil teks dari kolom-kolom yang ingin dicari
            const orderIdText = r.querySelector('.order-id')?.innerText.toLowerCase() || "";
            const pickupCodeText = r.querySelector('.pickup-code')?.innerText.toLowerCase() || "";
            const customerName = r.querySelector('.customer-name')?.innerText.toLowerCase() || "";
            const itemsText = r.querySelector('.order-items')?.innerText.toLowerCase() || "";

            // 2. Ambil status (Kolom ke-5: Index ke-4 jika pakai eq() atau nth-child(5))
            // Kita gunakan querySelector pada td yang berisi badge status secara spesifik
            const statusBadge = r.querySelector('td:nth-child(5) .badge');
            const statusText = statusBadge ? statusBadge.innerText.toUpperCase().trim() : "";

            // 3. Cek kecocokan (Keyword mencari di ID, Nama Pelanggan, atau Nama Produk)
            const matchesSearch =
                orderIdText.includes(keyword) ||
                customerName.includes(keyword) ||
                itemsText.includes(keyword) ||
                pickupCodeText.includes(keyword);

            const matchesStatus = selectedStatus === "" || statusText === selectedStatus;

            return matchesSearch && matchesStatus;
        });

        // --- LOGIKA PAGINATION ---
        const totalPages = Math.max(1, Math.ceil(filtered.length / limit));
        if (page > totalPages) page = totalPages;
        if (page < 1) page = 1;

        // Sembunyikan semua dulu
        rows.forEach(r => r.style.display = 'none');

        const start = (page - 1) * limit;
        const end = start + limit;
        const paginatedItems = filtered.slice(start, end);

        // Tampilkan yang sesuai halaman
        paginatedItems.forEach(r => r.style.display = '');

        // Update Info UI
        info.innerText = `Menampilkan ${paginatedItems.length} dari ${filtered.length} data`;
        pageInfo.innerText = `${page} / ${totalPages}`;
        prevBtn.disabled = page === 1;
        nextBtn.disabled = page === totalPages;

        if (paginationControls) {
            paginationControls.style.display = filtered.length > 0 ? 'block' : 'none';
        }
    }
    // Event Listeners
    searchInput.addEventListener('input', () => { page = 1; render() })
    statusSelect.addEventListener('change', () => { page = 1; render() })
    limitSelect.addEventListener('change', () => { page = 1; render() })

    prevBtn.addEventListener('click', () => {
        if (page > 1) {
            page--
            render()
        }
    })

    nextBtn.addEventListener('click', () => {
        const limit = parseInt(limitSelect.value)
        const keyword = searchInput.value.toLowerCase().trim()
        const selectedStatus = statusSelect.value.toUpperCase().trim()

        // Hitung ulang filtered length untuk validasi next page
        const currentFilteredCount = rows.filter(r => {
            const o = r.querySelector('.order-id')?.innerText.toLowerCase() || ""
            const i = r.querySelector('.order-items')?.innerText.toLowerCase() || ""
            const s = r.querySelector('td:nth-child(4) .badge')?.innerText.toUpperCase().trim() || ""
            return (o.includes(keyword) || i.includes(keyword)) && (selectedStatus === "" || s === selectedStatus)
        }).length

        if (page < Math.ceil(currentFilteredCount / limit)) {
            page++
            render()
        }
    })

    // Jalankan render pertama kali
    render()
})
