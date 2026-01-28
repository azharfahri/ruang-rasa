<div class="card shadow-sm">
    <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cart3 me-2"></i>Keranjang</span>
        @if($order->exists)
            <span class="badge bg-primary">Order #{{ $order->id }}</span>
        @endif
    </div>

    <div class="card-body p-2" style="max-height: 450px; overflow-y: auto;">
        @forelse($order->items as $item)
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="fw-bold d-block text-dark">{{ $item->product->name }}</small>

                        {{-- LIST VARIANT --}}
                        @if ($item->details->count())
                            <ul class="ps-3 mb-1" style="list-style-type: none; font-size: 0.75rem;">
                                @foreach ($item->details as $detail)
                                    <li class="text-muted">
                                        <i class="bi bi-check2"></i> {{ $detail->variantOption->option_name }}
                                        @if ($detail->price_impact > 0)
                                            <span class="text-success">(+{{ number_format($detail->price_impact, 0, ',', '.') }})</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>

                            {{-- BUTTON EDIT VARIANT --}}
                            <button class="btn btn-link btn-sm p-0 text-decoration-none"
                                    style="font-size: 0.7rem;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editVariantModal-{{ $item->id }}">
                                <i class="bi bi-pencil-square"></i> Edit Varian
                            </button>
                        @endif
                    </div>
                    <div class="text-end">
                        <small class="fw-bold d-block">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</small>
                    </div>
                </div>

                {{-- QTY CONTROL --}}
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">
                        {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                    </small>

                    <form method="POST" action="{{ route('cashier.orders.item.minus', [$order, $item]) }}" class="ajax-cart-form">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-0" title="Kurangi Qty">
                            <i class="bi bi-dash">-</i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- MODAL EDIT VARIAN --}}
            <div class="modal fade" id="editVariantModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="{{ route('cashier.orders.items.update-variant', [$order, $item]) }}"
                        class="modal-content ajax-cart-form">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h6 class="modal-title">Edit Varian: {{ $item->product->name }}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-start">
                            @foreach ($item->product->variantTypes as $type)
                                <div class="mb-3">
                                    <label class="fw-bold small d-block mb-1 text-primary">{{ $type->name }}</label>
                                    @foreach ($type->options as $option)
                                        @php
                                            $checked = $item->details->where('variant_option_id', $option->id)->count();
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="{{ $type->input_type }}"
                                                   name="variants[{{ $type->id }}][]"
                                                   value="{{ $option->id }}"
                                                   id="editOpt{{ $item->id }}{{ $option->id }}"
                                                   {{ $checked ? 'checked' : '' }}
                                                   {{ $type->input_type == 'radio' ? 'required' : '' }}>
                                            <label class="form-check-label small" for="editOpt{{ $item->id }}{{ $option->id }}">
                                                {{ $option->option_name }}
                                                (+Rp {{ number_format($option->price_impact, 0, ',', '.') }})
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">Keranjang masih kosong</p>
            </div>
        @endforelse
    </div>

    <div class="card-footer bg-white border-top-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-bold text-muted">TOTAL TAGIHAN</span>
            <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}</h4>
        </div>

        @if ($order->exists && $order->items->count() > 0)
            <hr>
            {{-- FORM PEMBAYARAN --}}
            <form action="{{ route('cashier.orders.pay.cash', $order->id) }}" method="POST" id="formBayar">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold">Nama Customer</label>
                    <input type="text" name="customer_name" class="form-control form-control-sm"
                           required placeholder="Input nama..." autocomplete="off">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Uang Tunai Diterima</label>
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white">Rp</span>
                        <input type="number" name="cash_received" id="cash_received"
                               class="form-control form-control-lg fw-bold border-success text-success"
                               placeholder="0" required min="{{ $order->total }}">
                    </div>
                    <div id="money-error" class="text-danger small mt-1 d-none">
                        <i class="bi bi-exclamation-circle"></i> Uang tidak mencukupi!
                    </div>
                </div>

                <div class="alert alert-secondary p-2 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small>Kembalian:</small>
                        <span class="fw-bold fs-5" id="change_display">Rp 0</span>
                    </div>
                </div>

                <button type="submit" id="btn-bayar" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                    PROSES BAYAR & SELESAI
                </button>
            </form>
        @else
            <button class="btn btn-secondary w-100 py-2" disabled>
                <i class="bi bi-lock me-2"></i>Selesaikan Pesanan
            </button>
        @endif
    </div>
</div>
