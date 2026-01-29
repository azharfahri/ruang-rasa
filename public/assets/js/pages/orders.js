/**
 * Kasir Orders Page Logic
 * Filter Produk, Kembalian Real-time, AJAX Cart, Cash & Midtrans
 */

document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
     * 1. FILTER PRODUK
     * ===================================================== */
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');

    function filterMenu() {
        const search = searchInput?.value.toLowerCase() || '';
        const category = categoryFilter?.value || 'all';

        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.dataset.name;
            const cat = item.dataset.category;
            item.style.display =
                name.includes(search) &&
                (category === 'all' || cat === category)
                    ? 'block'
                    : 'none';
        });
    }

    searchInput?.addEventListener('input', filterMenu);
    categoryFilter?.addEventListener('change', filterMenu);

    document.addEventListener('keydown', e => {
        if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            searchInput?.focus();
        }
    });

    /* =====================================================
     * 2. KEMBALIAN CASH REAL-TIME
     * ===================================================== */
    document.addEventListener('input', function (e) {
        if (e.target.id === 'cash_received') {
            const total = parseInt(e.target.min) || 0;
            const received = parseInt(e.target.value) || 0;
            const change = received - total;

            const display = document.getElementById('change_display');
            const btn = document.getElementById('btn-bayar');
            const error = document.getElementById('money-error');

            if (received && change < 0) {
                display.innerText = 'Rp 0';
                display.classList.add('text-danger');
                error.classList.remove('d-none');
                btn.disabled = true;
            } else {
                display.innerText = 'Rp ' + Math.max(change, 0).toLocaleString('id-ID');
                display.classList.remove('text-danger');
                error.classList.add('d-none');
                btn.disabled = false;
            }
        }
    });

    /* =====================================================
     * 3. AJAX CART
     * ===================================================== */
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.add-item-form, .ajax-cart-form');
        if (!form) return;

        e.preventDefault();

        const btn = form.querySelector('button[type="submit"]');
        const original = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => res.text())
            .then(html => {
                document.getElementById('cartContainer').innerHTML = html;

                const modal = document.querySelector('.modal.show');
                if (modal) bootstrap.Modal.getInstance(modal)?.hide();

                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Keranjang diperbarui',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            })
            .catch(() => Swal.fire('Error', 'Gagal update keranjang', 'error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = original;
            });
    });

    /* =====================================================
     * 4. MIDTRANS PAYMENT (BANK + E-WALLET)
     * ===================================================== */
    const btnMidtrans = document.getElementById('btn-midtrans');
    const customerInput = document.getElementById('customer_name');

    if (btnMidtrans) {
        btnMidtrans.addEventListener('click', function () {
            const name = customerInput.value.trim();
            const url = this.dataset.url;

            if (!name) {
                Swal.fire('Oops', 'Nama customer wajib diisi', 'warning');
                return;
            }

            btnMidtrans.disabled = true;
            btnMidtrans.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ customer_name: name })
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.snap_token) {
                        Swal.fire('Gagal', 'Snap token tidak ditemukan', 'error');
                        return;
                    }

                    window.snap.pay(data.snap_token, {
                        onSuccess: function () {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil 🎉',
                                text: 'Pesanan sedang diproses'
                            }).then(() => {
                                window.location.href = '/cashier/orders';
                            });
                        },
                        onPending: function () {
                            Swal.fire(
                                'Menunggu Pembayaran',
                                'Silakan selesaikan pembayaran',
                                'info'
                            );
                        },
                        onError: function () {
                            Swal.fire('Gagal', 'Pembayaran gagal', 'error');
                        }
                    });
                })
                .catch(() => {
                    Swal.fire('Error', 'Tidak bisa terhubung ke server', 'error');
                })
                .finally(() => {
                    btnMidtrans.disabled = false;
                    btnMidtrans.innerHTML = '💳 BAYAR NON-TUNAI';
                });
        });
    }

    /* =====================================================
     * 5. SYNC NAMA CUSTOMER CASH & NON-CASH
     * ===================================================== */
    const cashCustomer = document.getElementById('cash_customer_name');
    if (customerInput && cashCustomer) {
        customerInput.addEventListener('input', () => {
            cashCustomer.value = customerInput.value;
        });
    }
});
