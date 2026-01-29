/**
 * Kasir Orders Page Logic
 * Menangani: Filter Produk, Pintasan Keyboard, Kembalian Real-time, dan AJAX Keranjang
 */

document.addEventListener('DOMContentLoaded', function () {

    // --- 1. LOGIKA FILTER (Mencari Produk) ---
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');

    function filterMenu() {
        const search = searchInput?.value.toLowerCase() || '';
        const category = categoryFilter?.value || 'all';
        const items = document.querySelectorAll('.product-item');

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            const cat = item.getAttribute('data-category');
            const matchSearch = name.includes(search);
            const matchCategory = (category === 'all' || cat === category);

            item.style.display = (matchSearch && matchCategory) ? 'block' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterMenu);
    if (categoryFilter) categoryFilter.addEventListener('change', filterMenu);

    // Shortcut tombol '/' untuk fokus ke search
    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            searchInput?.focus();
        }
    });

    // --- 2. LOGIKA KEMBALIAN REAL-TIME (Event Delegation) ---
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'cash_received') {
            const inputUang = e.target;
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
                if (display) {
                    display.innerText = 'Rp ' + (change >= 0 ? change.toLocaleString('id-ID') : 0);
                    display.classList.remove('text-danger');
                }
                if (errorMsg) errorMsg.classList.add('d-none');
                if (btn) btn.disabled = false;
            }
        }
    });

    // --- 3. LOGIKA AJAX CART & FORM SUBMIT ---
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.add-item-form') || e.target.closest('.ajax-cart-form');

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
                    // A. TUTUP MODAL & BERSIHKAN BACKDROP (Solusi Layar Hitam)
                    const openModal = document.querySelector('.modal.show');
                    if (openModal) {
                        const modalInstance = bootstrap.Modal.getInstance(openModal);
                        if (modalInstance) modalInstance.hide();
                    }

                    // Paksa hapus sisa backdrop & reset body style
                    setTimeout(() => {
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(b => b.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }, 100);

                    // B. UPDATE KONTEN KERANJANG
                    const cartContainer = document.getElementById('cartContainer');
                    if (cartContainer) cartContainer.innerHTML = text;

                    // C. TUTUP COLLAPSE PRODUK (Jika ada)
                    if (form.classList.contains('add-item-form')) {
                        const collapseEl = form.closest('.collapse');
                        if (collapseEl) {
                            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl);
                            bsCollapse.hide();
                        }
                    }

                    // D. NOTIFIKASI SUKSES
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Keranjang telah diperbarui',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } else {
                    let errorMessage = 'Gagal memproses permintaan';
                    try {
                        const err = JSON.parse(text);
                        errorMessage = err.message || errorMessage;
                    } catch (e) {}
                    Swal.fire('Gagal!', errorMessage, 'error');
                }
            })
            .catch(err => {
                console.error('Fetch Error:', err);
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
