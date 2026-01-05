@extends('layouts.admin')

@section('content')
    <div class="row">

        {{-- PRODUK --}}
        <div class="col-md-8">
            <div class="row">


                @foreach ($products as $product)
                    @php
                        $branchProduct = $product->branchProducts->first();
                        $price = $branchProduct->price_override ?? $product->price;
                    @endphp

                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">

                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                    style="height:120px; object-fit:cover;">
                            @endif

                            <div class="card-body p-2">
                                <div class="text-center mb-2">
                                    <small class="fw-semibold d-block">
                                        {{ $product->name }}
                                    </small>
                                    <small class="text-muted">
                                        Rp {{ number_format($price, 0, ',', '.') }}
                                    </small>
                                </div>

                                <form method="POST" action="{{ route('cashier.orders.addItem', $order) }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    {{-- VARIAN --}}
                                    @foreach ($product->variantTypes as $type)
                                        <div class="mb-2 text-start">
                                            <small class="fw-semibold">{{ $type->name }}</small>

                                            @foreach ($type->options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="{{ $type->input_type === 'radio' ? 'radio' : 'checkbox' }}"
                                                        name="variants[{{ $type->id }}][]" value="{{ $option->id }}"
                                                        id="option{{ $product->id }}{{ $option->id }}">

                                                    <label class="form-check-label"
                                                        for="option{{ $product->id }}{{ $option->id }}">
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
                                            value="1" min="1">
                                    </div>

                                    <button class="btn btn-primary btn-sm w-100">
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
                        Total:
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </h5>

                    <form method="POST" action="{{ route('cashier.orders.pay.cash', $order) }}">
                        @csrf
                        <button class="btn btn-success w-100">
                            Bayar Cash
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
