{{--
    Partial ini akan di-render dan disuntikkan ke dalam Offcanvas Keranjang
    melalui AJAX. Variabel yang tersedia: $latestOrder
    (Objek Order dengan relasi items.product yang sudah dimuat)
--}}

@if ($latestOrder && $latestOrder->items->count())
    {{-- LIST ITEM KERANJANG --}}
    <div class="p-3" style="max-height: 70vh; overflow-y: auto;">
        @foreach ($latestOrder->items as $item)
            {{-- Pastikan variant_details adalah array sebelum digunakan --}}
            @php
                $variants = is_string($item->variant_details) ? json_decode($item->variant_details, true) : $item->variant_details;
                // Pastikan $variants adalah array/Countable, atau array kosong jika decode gagal
                if (!is_array($variants)) {
                    $variants = [];
                }
            @endphp

            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">

                {{-- Kuantitas & Nama Produk --}}
                <div class="flex-grow-1 me-3">
                    <p class="mb-0 fw-bold">
                        {{ $item->quantity }}x {{ $item->product->nama ?? $item->product->name ?? 'Produk Tidak Dikenal' }}
                    </p>

                    {{-- Detail Varian/Customisasi --}}
                    <small class="text-muted">
                        @if ($item->temperature)
                            {{ $item->temperature }} |
                        @endif
                        @if ($item->sugar_level)
                            Gula: {{ $item->sugar_level }} |
                        @endif
                        @if ($item->ice_level)
                            Es: {{ $item->ice_level }}
                        @endif

                        {{-- Menampilkan Varian Tambahan dari JSON --}}
                        @if (count($variants) > 0)
                            <br>
                            Tambahan:
                            @foreach ($variants as $variant)
                                <span>{{ $variant['name'] }} (+{{ number_format($variant['impact']) }})</span>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        @endif
                    </small>
                </div>

                {{-- Harga Item Total --}}
                <div class="text-end">
                    <p class="mb-0 fw-bold text-success">
                        Rp{{ number_format($item->price) }}
                    </p>
                    {{-- Tombol Hapus (Jika Anda mengimplementasikannya) --}}
                    <form action="{{ route('orders.removeItem') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="order_product_id" value="{{ $item->id }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-0 mt-1" title="Hapus Item">
                            <i class="bi bi-x-circle"></i> Hapus
                        </button>
                    </form>
                </div>

            </div>
        @endforeach
    </div>

    {{-- FOOTER KERANJANG & TOTAL --}}
    <div class="p-3 border-top bg-light">
        <div class="d-flex justify-content-between fw-bold mb-2">
            <span>Total Pesanan:</span>
            <span>Rp{{ number_format($latestOrder->total) }}</span>
        </div>

        <form action="{{ route('orders.checkOut') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $latestOrder->id }}">
            <button type="submit" class="btn btn-success w-100 py-2">
                <i class="bi bi-cart-check-fill"></i> Checkout
            </button>
        </form>

        <small class="d-block text-center mt-2 text-muted">
            {{-- Sesuaikan route ini jika ada halaman detail order --}}
            <a href="{{ route('orders.detail', $latestOrder->id) }}" class="text-decoration-none">Lihat Detail Keranjang</a>
        </small>
    </div>

@else
    {{-- JIKA KERANJANG KOSONG --}}
    <div class="p-4 text-center">
        <i class="bi bi-cart-x display-4 text-muted"></i>
        <p class="mt-3 text-muted">Keranjang Anda masih kosong.</p>
        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="offcanvas" aria-label="Close">
            Mulai Belanja Sekarang
        </button>
    </div>
@endif
