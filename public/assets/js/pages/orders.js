document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', function (e) {
        // Deteksi apakah form yang disubmit adalah form tambah, minus, atau edit varian
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
                method: 'POST', // Laravel handle @method('PATCH') otomatis lewat FormData
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(async response => {
                const text = await response.text();

                if (response.ok) {
                    // 1. Update Keranjang
                    document.getElementById('cartContainer').innerHTML = text;

                    // 2. Jika form berada di dalam modal (seperti Edit Varian), tutup modalnya
                    const modalEl = form.closest('.modal');
                    if (modalEl) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }

                    // 3. Notifikasi Sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil diperbarui',
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
