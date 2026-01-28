document.addEventListener('DOMContentLoaded', function () {

    // --- 1. LOGIKA FILTER (Mencari Produk) ---
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');

    function filterMenu() {
        const search = searchInput.value.toLowerCase();
        const category = categoryFilter.value;
        const items = document.querySelectorAll('.product-item');

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            const cat = item.getAttribute('data-category');
            const matchSearch = name.includes(search);
            const matchCategory = (category === 'all' || cat === category);
            item.style.display = (matchSearch && matchCategory) ? 'block' : 'none';
        });
    }

    if(searchInput) searchInput.addEventListener('input', filterMenu);
    if(categoryFilter) categoryFilter.addEventListener('change', filterMenu);

    // Shortcut tombol '/' untuk fokus ke search
    document.addEventListener('keydown', function(e) {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
            e.preventDefault();
            searchInput?.focus();
        }
    });

    // --- 2. LOGIKA KEMBALIAN REAL-TIME (Event Delegation) ---
    // Kita tempel di 'document' agar tidak mati saat keranjang di-refresh via Ajax
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'cash_received') {
            const inputUang = e.target;
            // Mengambil total tagihan dari atribut 'min' yang kita set di HTML
            const total = parseInt(inputUang.getAttribute('min')) || 0;
            const received = parseInt(inputUang.value) || 0;
            const change = received - total;

            const display = document.getElementById('change_display');
            const btn = document.getElementById('btn-bayar');
            const errorMsg = document.getElementById('money-error');

            if (received > 0 && change < 0) {
                if (display) { display.innerText = 'Rp 0'; display.classList.add('text-danger'); }
                if (errorMsg) errorMsg.classList.remove('d-none');
                if (btn) btn.disabled = true;
            } else {
                if (display) { display.innerText = 'Rp ' + (change >= 0 ? change.toLocaleString('id-ID') : 0); display.classList.remove('text-danger'); }
                if (errorMsg) errorMsg.classList.add('d-none');
                if (btn) btn.disabled = false;
            }
        }
    });

    // --- 3. LOGIKA AJAX CART & FORM SUBMIT ---
    document.addEventListener('submit', function (e) {
        // Cek apakah ini form add-item, update-cart, atau pay-cash
        const form = e.target.closest('.add-item-form') || e.target.closest('.ajax-cart-form');

        // Khusus form pembayaran, kita biarkan submit normal (refresh)
        // agar user diarahkan ke halaman struk, KECUALI kamu mau bayar pakai Ajax juga.
        // Di sini saya asumsikan form bayar TIDAK pakai ajax-cart-form agar bisa pindah halaman ke struk.
        if (form) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button:not([type="button"])');
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            const formData = new FormData(form);

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(async response => {
                const text = await response.text();

                if (response.ok) {
                    document.getElementById('cartContainer').innerHTML = text;

                    // Tutup modal varian jika ada yang terbuka
                    const openModal = document.querySelector('.modal.show');
                    if (openModal) {
                        const modalInstance = bootstrap.Modal.getInstance(openModal);
                        modalInstance.hide();
                    }

                    // Tutup collapse produk
                    if(form.classList.contains('add-item-form')) {
                        const collapseEl = form.closest('.collapse');
                        if (collapseEl) {
                            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl);
                            bsCollapse.hide();
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Keranjang diperbarui',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    const err = JSON.parse(text);
                    Swal.fire('Gagal!', err.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }
    });
});
