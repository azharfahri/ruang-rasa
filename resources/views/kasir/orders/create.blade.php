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
            <div class="row">
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
                                <small class="d-block {{ $stock <= 0 ? 'text-danger' : 'text-success' }}">
                                    Stok: {{ $stock }}
                                </small>

                                <button class="btn btn-outline-primary btn-sm mt-2 w-100" data-bs-toggle="collapse"
                                    data-bs-target="#productForm{{ $product->id }}">
                                    Pilih
                                </button>
                            </div>

                            {{-- COLLAPSE FORM --}}
                            <div class="collapse border-top p-2" id="productForm{{ $product->id }}">
                                {{-- PERUBAHAN DISINI: Action form dinamis --}}
                                <form method="POST" action="{{ isset($order) ? route('cashier.orders.addItem', $order->id) : route('cashier.orders.addItem') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    {{-- VARIAN --}}
                                    @foreach ($product->variantTypes as $type)
                                        <div class="mb-2 text-start">
                                            <small class="fw-semibold">{{ $type->name }}</small>
                                            @foreach ($type->options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="{{ $type->input_type }}"
                                                        name="variants[{{ $type->id }}][]" value="{{ $option->id }}"
                                                        id="opt{{ $product->id }}{{ $option->id }}">
                                                    <label class="form-check-label" for="opt{{ $product->id }}{{ $option->id }}">
                                                        {{ $option->option_name }}
                                                        @if ($option->price_impact > 0)
                                                            (+Rp {{ number_format($option->price_impact, 0, ',', '.') }})
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach

                                    {{-- QTY --}}
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
                    {{-- PERUBAHAN DISINI: Tambahkan pengecekan isset($order) --}}
                    @if(isset($order) && $order->items->count() > 0)
                        @foreach($order->items as $item)
                            <div class="border-bottom pb-2 mb-2">
                                <small class="fw-semibold d-block">{{ $item->product->name }}</small>

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
                                    <button class="btn btn-link btn-sm p-0" data-bs-toggle="modal"
                                        data-bs-target="#editVariantModal-{{ $item->id }}">
                                        Edit Varian
                                    </button>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                    <div class="d-flex align-items-center gap-2">
                                        <form method="POST" action="{{ route('cashier.orders.item.minus', [$order, $item]) }}">
                                            @csrf
                                            <button class="btn btn-outline-danger btn-sm">−</button>
                                        </form>
                                        <small class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL EDIT VARIAN (Logika tetap sama) --}}
                            {{-- ... (tetap sertakan modal edit varian di sini) ... --}}
                        @endforeach
                    @else
                        <p class="text-center text-muted mb-0 p-4">
                            Keranjang masih kosong
                        </p>
                    @endif
                </div>

                <div class="card-footer">
                    <h5 class="text-end mb-3">
                        Total: Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                    </h5>

                    {{-- Form bayar hanya muncul/aktif jika order sudah ada --}}
                    @if(isset($order) && $order->items->count() > 0)
                        <form method="POST" action="{{ route('cashier.orders.pay.cash', $order) }}">
                            @csrf
                            <button class="btn btn-success w-100">Bayar Cash</button>
                        </form>
                    @else
                        <button class="btn btn-secondary w-100" disabled>Bayar Cash</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
