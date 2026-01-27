/**
 * alert.js - Universal CRUD Alert Handler
 * Digunakan untuk menangani konfirmasi form dan notifikasi SweetAlert2
 */

document.addEventListener('DOMContentLoaded', () => {

    // 1. KONFIGURASI DEFAULT UNTUK KONFIRMASI (SEBELUM SUBMIT)
    const config = {
        delete: {
            title: 'Hapus Data?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            confirmButtonText: 'Ya, Hapus!',
            confirmButtonColor: '#d33'
        },
        save: {
            title: 'Simpan Data?',
            text: 'Pastikan data yang diisi sudah benar.',
            icon: 'question',
            confirmButtonText: 'Ya, Simpan',
            confirmButtonColor: '#28a745'
        },
        update: {
            title: 'Simpan Perubahan?',
            text: 'Data lama akan diperbarui dengan data baru.',
            icon: 'info',
            confirmButtonText: 'Ya, Update',
            confirmButtonColor: '#3085d6'
        },
        default: {
            title: 'Apakah Anda yakin?',
            text: 'Aksi ini akan memproses data Anda.',
            icon: 'warning',
            confirmButtonText: 'Lanjutkan',
            confirmButtonColor: '#3085d6'
        }
    };

    // 2. EVENT LISTENER UNTUK FORM KONFIRMASI
    document.addEventListener('submit', function (e) {
        // Cari form yang memiliki class .confirm-submit
        const form = e.target.closest('.confirm-submit');
        if (!form) return;

        // Cegah form terkirim langsung
        e.preventDefault();

        // Ambil tipe aksi dari atribut data-type="..."
        const type = form.getAttribute('data-type') || 'default';
        const setting = config[type] || config.default;

        // Ambil custom message jika ada di atribut HTML
        const customTitle = form.getAttribute('data-title');
        const customText = form.getAttribute('data-text');

        // Eksekusi SweetAlert2
        Swal.fire({
            title: customTitle || setting.title,
            text: customText || setting.text,
            icon: setting.icon,
            showCancelButton: true,
            confirmButtonColor: setting.confirmButtonColor,
            cancelButtonColor: '#aaa',
            confirmButtonText: setting.confirmButtonText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Tambahkan loading state sederhana jika perlu
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';
                }

                // Kirim form secara manual
                form.submit();
            }
        });
    });

    // 3. FUNGSI GLOBAL UNTUK NOTIFIKASI (SESUDAH PROSES)
    // window.showAlert agar bisa dipanggil langsung dari Blade/inline script
    window.showAlert = (icon, title, message) => {
        if (icon === 'success') {
            // Notifikasi Sukses menggunakan Toast (Pojok Kanan Atas)
            Swal.fire({
                icon: 'Berhasil',
                title: title,
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Notifikasi Error/Gagal menggunakan Modal Tengah
            Swal.fire({
                icon: 'Gagal',
                title: title,
                text: message,
                confirmButtonColor: '#3085d6'
            });
        }
    };
});
