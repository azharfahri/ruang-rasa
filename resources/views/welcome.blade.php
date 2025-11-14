@extends('layouts.app')
@section('content')
    <style>
        body {
            background-color: #f9f5ef;
            /* krem lembut */
            color: #3a2e25;
        }

        /* KATEGORI SECTION */
        .kategori-section {
            background-color: #f3ede2;
            border-radius: 12px;
            padding: 18px 24px;
            margin-bottom: 40px;
            box-shadow: 0 3px 10px rgba(80, 60, 30, 0.1);
        }

        .kategori-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: #4a3c2f;
            margin-bottom: 14px;
        }

        .kategori-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .kategori-item {
            background-color: #e8dfd1;
            color: #3a2e25;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
        }

        .kategori-item:hover {
            background-color: #d6c8b4;
        }

        .kategori-item.active {
            background-color: #4c6b44;
            color: #fffaf2;
        }

        /* Card produk */
        .card {
            border: none;
            border-radius: 14px;
            background-color: #fffdf8;
            transition: all 0.3s ease;
            height: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(80, 50, 20, 0.15);
        }

        .card-img-top {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        /* Tombol utama */
        .btn-success {
            background-color: #4c6b44;
            border: none;
        }

        .btn-success:hover {
            background-color: #3c5736;
        }

        /* Tombol sekunder */
        .btn-secondary {
            background-color: #b48a58;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #9c7648;
        }

        /* Modal tampilan */
        .modal-content {
            background-color: #fffaf2;
            border-radius: 16px;
            border: 1px solid #e0d5c3;
            box-shadow: 0 8px 25px rgba(50, 30, 10, 0.2);
        }

        .modal-header {
            background-color: #4c6b44;
            color: #fffaf2;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            border-bottom: none;
        }

        .modal-body {
            background-color: #fffaf5;
            padding: 24px 28px;
        }

        .modal-footer {
            background-color: #f4ede3;
            border-top: none;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        /* Label dan teks */
        .form-label {
            font-weight: 600;
            color: #4a3c2f;
        }

        /* Checkbox & varian style */
        .form-check {
            margin-bottom: 8px;
            display: inline-block;
            margin-right: 15px;
        }

        .form-check-input {
            width: 1.1em;
            height: 1.1em;
            border: 2px solid #a18a6b;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #4c6b44;
            border-color: #4c6b44;
        }

        .form-check-label {
            margin-left: 6px;
            color: #3a2e25;
            font-weight: 500;
        }

        /* Total harga */
        #modalTotalPrice {
            color: #4c6b44;
            font-weight: 700;
        }
    </style>

    <section class="py-4">
        <div class="container">

            <!-- Alert -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- KATEGORI -->
            <div class="kategori-section">
                <div class="kategori-title">Kategori Menu</div>
                <div class="kategori-list">
                    <div class="kategori-item active">Semua</div>
                    @foreach ($category as $data)
                        <div class="kategori-item">{{ $data->name }}</div>
                    @endforeach
                </div>
            </div>

            <!-- PRODUK -->
            <h3 class="mb-4 fw-bold" style="color:#4a6741;">Produk Kami</h3>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                @foreach ($product as $item)
                    <div class="col">
                        <div class="card">
                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top"
                                alt="{{ $item->name }}">
                            <div class="card-body">
                                <h5 class="card-title text-dark">{{ $item->name }}</h5>
                                <p class="text-muted mb-1">{{ $item->category->name }}</p>
                                <p class="text-muted mb-1">Stok: {{ $item->stock }}</p>
                                <p class="fw-bold text-success">Rp {{ number_format($item->price, 0, ',', '.') }}</p>

                                <button class="btn btn-success w-100 mt-2" data-bs-toggle="modal"
                                    data-bs-target="#productModal" data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}" data-price="{{ $item->price }}">
                                    Masukkan Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Modal Produk -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="addToCartForm" method="POST" action="{{ route('order.create') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Tambah ke Keranjang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="modalProductId">
                        <p class="fw-bold fs-5 mb-2" id="modalProductName"></p>
                        <p>Harga dasar: Rp<span id="modalBasePrice">0</span></p>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="quantity" class="form-control" id="modalQuantity" min="1"
                                    value="1">
                            </div>
                            <div class="col">
                                <label class="form-label">Suhu</label>
                                <select name="temperature" class="form-select">
                                    <option value="Hot">Hot</option>
                                    <option value="Iced">Iced</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label">Gula</label>
                                <select name="sugar_level" class="form-select">
                                    <option value="Normal">Normal</option>
                                    <option value="Less Sugar">Less Sugar</option>
                                    <option value="No Sugar">No Sugar</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label">Es</label>
                                <select name="ice_level" class="form-select">
                                    <option value="Normal">Normal</option>
                                    <option value="Less Ice">Less Ice</option>
                                    <option value="No Ice">No Ice</option>
                                </select>
                            </div>
                        </div>

                        <div id="variantContainer" class="mt-3">
                            <p class="text-muted">Memuat varian...</p>
                        </div>

                        <hr>
                        <p class="fw-bold">Total Harga: Rp<span id="modalTotalPrice">0</span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tambah ke Keranjang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('productModal');
            let basePrice = 0;

            // Pastikan elemen offcanvas cart ada, kita akan menggunakannya untuk update UI
            const offcanvasContent = document.getElementById('dynamic-cart-content');
            const cartCountBadge = document.getElementById('cartCountBadge');

            // Fungsi untuk memperbarui UI keranjang (Offcanvas dan Badge)
            function updateCartUI(data) {
                if (data.cartHtml && offcanvasContent) {
                    offcanvasContent.innerHTML = data.cartHtml;
                }
                if (data.cartCount !== undefined && cartCountBadge) {
                    cartCountBadge.textContent = data.cartCount;
                }
            }

            // Fungsi untuk menampilkan pesan (Anda bisa mengganti ini dengan SweetAlert/Toastr)
            function showMessage(status, message) {
                if (status === 'success') {
                    console.log('✅ Sukses: ' + message);
                    // TODO: Ganti dengan toast/notifikasi visual
                } else {
                    console.error('❌ Gagal: ' + message);
                    // TODO: Ganti dengan modal/alert error
                }
            }


            // =================================================================
            // 1. HANDLER MODAL (Kode Anda yang sudah ada)
            // =================================================================
            if (modal) {
                modal.addEventListener('show.bs.modal', async (event) => {
                    const button = event.relatedTarget;
                    const productId = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    basePrice = parseFloat(button.getAttribute('data-price'));

                    document.getElementById('modalProductId').value = productId;
                    document.getElementById('modalProductName').textContent = name;
                    document.getElementById('modalBasePrice').textContent = basePrice.toLocaleString();
                    document.getElementById('modalTotalPrice').textContent = basePrice.toLocaleString();

                    const variantContainer = document.getElementById('variantContainer');
                    variantContainer.innerHTML = '<p class="text-muted">Memuat varian...</p>';

                    try {
                        const response = await fetch(`/product/${productId}/variants`);
                        const data = await response.json();

                        variantContainer.innerHTML = data.html ||
                            '<p class="text-muted">Tidak ada varian untuk produk ini.</p>';
                        updateTotal();
                    } catch (err) {
                        variantContainer.innerHTML = '<p class="text-danger">Gagal memuat varian.</p>';
                    }
                });
            }

            document.addEventListener('change', (e) => {
                if (e.target.classList.contains('variant-checkbox') || e.target.id === 'modalQuantity') {
                    updateTotal();
                }
            });

            function updateTotal() {
                let total = basePrice;
                const quantity = parseInt(document.getElementById('modalQuantity')?.value) || 1;

                document.querySelectorAll('.variant-checkbox:checked').forEach(opt => {
                    total += parseFloat(opt.getAttribute('data-impact'));
                });

                total *= quantity;
                document.getElementById('modalTotalPrice').textContent = total.toLocaleString();
            }

            // kategori interaksi
            const kategoriItems = document.querySelectorAll('.kategori-item');
            kategoriItems.forEach(item => {
                item.addEventListener('click', () => {
                    kategoriItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                });
            });


            // =================================================================
            // 2. HANDLER AJAX UNTUK SEMUA FORM (Penambahan, Penghapusan, Checkout)
            // =================================================================

            // Kita menggunakan event delegation pada document.body karena form remove & checkout
            // dimuat ulang di dalam offcanvas melalui AJAX, sehingga event listener harus diletakkan
            // pada elemen yang statis (tidak berubah).

            document.body.addEventListener('submit', function(e) {
                const form = e.target;
                const isCartForm = form.id === 'addToCartForm' || form.action.includes('/order/');

                if (isCartForm) {
                    e.preventDefault(); // Hentikan submit form standar! (INI PENTING)

                    const isCheckout = form.action.includes('/checkout');
                    const formData = new FormData(form);
                    const url = form.action;

                    // Tampilkan loading state atau disable tombol
                    const submitButton = form.querySelector('button[type="submit"]');
                    const originalButtonHtml = submitButton ? submitButton.innerHTML : 'Submit';

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML =
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...';
                    }


                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => {
                            // Untuk Checkout, jika Laravel merespons dengan Redirect, kita biarkan browser mengikutinya.
                            // Jika itu form addToCart atau removeItem, kita harapkan JSON.
                            if (isCheckout && response.redirected) {
                                window.location.href = response.url;
                                return; // Hentikan pemrosesan JSON
                            }

                            // Cek apakah respons adalah JSON (biasanya untuk addToCart/removeItem)
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json();
                            } else {
                                // Jika bukan JSON (misalnya, ada error HTML dari Laravel), kita log
                                return response.text().then(text => {
                                    throw new Error(text);
                                });
                            }
                        })
                        .then(data => {
                            // Hanya jalankan jika respons adalah JSON
                            if (data && data.status) {
                                // Sembunyikan loading state
                                if (submitButton) {
                                    submitButton.disabled = false;
                                    submitButton.innerHTML = originalButtonHtml;
                                }

                                if (data.status === 'success') {
                                    // Jika berhasil, update keranjang
                                    updateCartUI(data);
                                    showMessage('success', data.message);

                                    // Tutup modal setelah penambahan keranjang (jika ini form di modal)
                                    if (form.id === 'addToCartForm' && modal) {
                                        const bsModal = bootstrap.Modal.getInstance(modal);
                                        if (bsModal) bsModal.hide();
                                    }
                                } else {
                                    showMessage('error', data.message || 'Gagal memproses permintaan.');
                                }
                            }
                        })
                        .catch(error => {
                            // Tangani kesalahan jaringan atau error server yang tidak terduga
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalButtonHtml;
                            }
                            showMessage('error', 'Terjadi kesalahan server/jaringan: ' + (error
                                .message || 'Tidak diketahui'));
                            console.error('Error AJAX:', error);
                        });
                }
            });

        });
    </script>
@endsection
