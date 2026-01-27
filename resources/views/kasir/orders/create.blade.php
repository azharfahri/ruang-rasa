@extends('layouts.admin')

@section('content')
    <div class="row">
        @if ($errors->has('stock'))
            <div class="alert alert-danger mb-3">
                {{ $errors->first('stock') }}
            </div>
        @endif


        {{-- PRODUK --}}
        <div class="col-md-8">
            {{-- Tambahkan ID induk 'productAccordion' pada row pembungkus --}}
            <div class="row" id="productAccordion">

                @foreach ($products as $product)
                    @php
                        $branchProduct = $product->branchProducts->first();
                        $price = $branchProduct->price_override ?? $product->price;
                        $stock = $branchProduct->stock;
                    @endphp

                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">

                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                    style="height:120px; object-fit:cover;">
                            @endif

                            <div class="card-body p-2 text-center">
                                <small class="fw-semibold d-block">{{ $product->name }}</small>
                                <small class="text-muted d-block">Rp {{ number_format($price, 0, ',', '.') }}</small>
                                <small class="d-block {{ $stock <= 0 ? 'text-danger' : 'text-success' }}">Stok:
                                    {{ $stock }}</small>

                                {{-- Tombol Pilih --}}
                                <button class="btn btn-outline-primary btn-sm mt-2 w-100" data-bs-toggle="collapse"
                                    data-bs-target="#productForm{{ $product->id }}" aria-expanded="false">
                                    Pilih
                                </button>
                            </div>

                            {{-- COLLAPSE FORM --}}
                            {{-- Tambahkan data-bs-parent="#productAccordion" agar yang lain otomatis tertutup --}}
                            <div class="collapse border-top p-2" id="productForm{{ $product->id }}"
                                data-bs-parent="#productAccordion">

                                <form method="POST" action="{{ route('cashier.orders.addItem') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    {{-- VARIAN --}}
                                    @if ($product->variantTypes->count() > 0)
                                        @foreach ($product->variantTypes as $type)
                                            <div class="mb-2 text-start">
                                                <small class="fw-semibold">{{ $type->name }}</small>
                                                @foreach ($type->options as $option)
                                                    <div class="form-check">
                                                        <input required class="form-check-input" type="{{ $type->input_type }}"
                                                            name="variants[{{ $type->id }}][]"
                                                            value="{{ $option->id }}"
                                                            id="opt{{ $product->id }}{{ $option->id }}">
                                                        <label class="form-check-label"
                                                            for="opt{{ $product->id }}{{ $option->id }}">
                                                            {{ $option->option_name }}
                                                            @if ($option->price_impact > 0)
                                                                (+Rp
                                                                {{ number_format($option->price_impact, 0, ',', '.') }})
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="mb-2 text-start">
                                            <small class="text-muted italic">Tidak ada varian</small>
                                        </div>
                                    @endif

                                    {{-- QTY & BUTTON --}}
                                    <div class="mb-2">
                                        <input type="number" name="qty" class="form-control form-control-sm"
                                            value="1" min="1" max="{{ $stock }}">
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" {{ $stock <= 0 ? 'disabled' : '' }}>
                                        + Tambah
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- KERANJANG --}}
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    Keranjang
                </div>

                <div class="card-body p-2">
                    @forelse($order->items as $item)
                        <div class="border-bottom pb-2 mb-2">
                            <small class="fw-semibold d-block">
                                {{ $item->product->name }}
                            </small>

                            {{-- LIST VARIANT --}}
                            @if ($item->details->count())
                                <ul class="ps-3 mb-1">
                                    @foreach ($item->details as $detail)
                                        <li>
                                            <small class="text-muted">
                                                {{ $detail->variantOption->option_name }}
                                                @if ($detail->price_impact > 0)
                                                    (+Rp {{ number_format($detail->price_impact, 0, ',', '.') }})
                                                @endif
                                            </small>
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- BUTTON EDIT --}}
                                <button class="btn btn-link btn-sm p-0" data-bs-toggle="modal"
                                    data-bs-target="#editVariantModal-{{ $item->id }}">
                                    Edit Varian
                                </button>
                            @endif

                            {{-- QTY & SUBTOTAL --}}
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small>
                                    {{ $item->quantity }} x
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </small>

                                <div class="d-flex align-items-center gap-2">
                                    <form method="POST"
                                        action="{{ route('cashier.orders.item.minus', [$order, $item]) }}">
                                        @csrf
                                        <button class="btn btn-outline-danger btn-sm">−</button>
                                    </form>

                                    <small class="fw-bold">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL EDIT VARIAN --}}
                        <div class="modal fade" id="editVariantModal-{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <form method="POST"
                                    action="{{ route('cashier.orders.items.update-variant', [$order, $item]) }}"
                                    class="modal-content">
                                    @csrf
                                    @method('PATCH')

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Edit Varian - {{ $item->product->name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        @foreach ($item->product->variantTypes as $type)
                                            <div class="mb-3">
                                                <label class="fw-semibold d-block mb-1">
                                                    {{ $type->name }}
                                                </label>

                                                @foreach ($type->options as $option)
                                                    @php
                                                        $checked = $item->details
                                                            ->where('variant_option_id', $option->id)
                                                            ->count();
                                                    @endphp

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="{{ $type->input_type }}"
                                                            name="variants[{{ $type->id }}][]"
                                                            value="{{ $option->id }}" {{ $checked ? 'checked' : '' }}>

                                                        <label class="form-check-label">
                                                            {{ $option->option_name }}
                                                            @if ($option->price_impact > 0)
                                                                (+Rp
                                                                {{ number_format($option->price_impact, 0, ',', '.') }})
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Batal
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <p class="text-center text-muted mb-0">
                            Keranjang masih kosong
                        </p>
                    @endforelse
                </div>

                <div class="card-footer">
                    <h5 class="text-end mb-3">
                        Total: Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                    </h5>

                    {{-- Hanya munculkan tombol bayar jika order sudah ada di database (sudah ada isinya) --}}
                    @if ($order->exists)
                        <form method="POST" action="{{ route('cashier.orders.pay.cash', $order) }}">
                            @csrf
                            <button class="btn btn-success w-100">
                                Bayar Cash
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary w-100" disabled>
                            Keranjang Kosong
                        </button>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/pages/orders.js') }}"></script>
@endpush
