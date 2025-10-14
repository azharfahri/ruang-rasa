<!-- Offcanvas -->
@auth
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart" aria-labelledby="My Cart">
        <div class="offcanvas-header justify-content-center">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Keranjang Belanja</span>
                    @if(isset($latestOrder) && $latestOrder)
                        <span class="badge bg-primary rounded-pill">{{ $latestOrder->orderProduct->count() }}</span>
                    @else
                        <span class="badge bg-primary rounded-pill">0</span>
                    @endif
                </h4>
                <ul class="list-group mb-3">
                    @if(isset($latestOrder) && $latestOrder)
                        @foreach($latestOrder->orderProduct as $item)
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0">{{ $item->product->name }}</h6>
                                    <small class="text-body-secondary">{{ $item->quantity }}x</small>
                                    @if($item->product->description)
                                        <small class="text-body-secondary d-block">
                                            {{ \Illuminate\Support\Str::limit($item->product->description, 50) }}
                                        </small>
                                    @endif
                                </div>
                                <span class="text-body-secondary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total</span>
                            <strong>Rp {{ number_format($latestOrder->total, 0, ',', '.') }}</strong>
                        </li>
                    @else
                        <li class="list-group-item text-center py-3">
                            <p class="mb-0">Keranjang Anda kosong</p>
                        </li>
                    @endif
                </ul>

                @if(isset($latestOrder) && $latestOrder)
                    <a href="{{ route('orders.detail', $latestOrder->id) }}" class="w-100 btn btn-primary btn-lg">
                        Lihat Detail Pesanan
                    </a>
                @else
                    <a href="{{ route('home') }}" class="w-100 btn btn-outline-primary btn-lg">
                        Lanjutkan Belanja
                    </a>
                @endif
            </div>
        </div>
    </div>
@endauth
