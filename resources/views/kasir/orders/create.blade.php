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
                        <div class="card h-100 shadow-sm {{ $stock <= 0 ? 'opacity-50 bg-light' : '' }}">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                    style="height:120px; object-fit:cover; {{ $stock <= 0 ? 'filter: grayscale(1);' : '' }}">
                            @endif

                            <div class="card-body p-2 text-center">
                                <small class="fw-semibold d-block text-truncate">{{ $product->name }}</small>
                                <small class="text-muted d-block">Rp {{ number_format($price, 0, ',', '.') }}</small>

                                <small class="d-block {{ $stock <= 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ $stock <= 0 ? 'Stok Habis' : 'Stok: ' . $stock }}
                                </small>

                                {{-- Tombol Pilih --}}
                                <button class="btn btn-outline-primary btn-sm mt-2 w-100" data-bs-toggle="collapse"
                                    data-bs-target="#productForm{{ $product->id }}" {{ $stock <= 0 ? 'disabled' : '' }}>
                                    {{ $stock <= 0 ? 'Habis' : 'Pilih' }}
                                </button>
                            </div>

                            {{-- COLLAPSE FORM --}}
                            {{-- HAPUS data-bs-parent agar Americano tidak tertutup saat Kopsu diklik --}}
                            <div class="collapse border-top p-2" id="productForm{{ $product->id }}">
                                <form method="POST" action="{{ route('cashier.orders.addItem') }}" class="add-item-form">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    {{-- Bagian Varian (Tetap sama seperti kode kamu) --}}
                                    @if ($product->variantTypes->count() > 0)
                                        @foreach ($product->variantTypes as $type)
                                            <div class="mb-2 text-start">
                                                <small class="fw-semibold">{{ $type->name }}</small>
                                                @foreach ($type->options as $option)
                                                    <div class="form-check">
                                                        <input required class="form-check-input"
                                                            type="{{ $type->input_type }}"
                                                            name="variants[{{ $type->id }}][]"
                                                            value="{{ $option->id }}"
                                                            id="opt{{ $product->id }}{{ $option->id }}">
                                                        <label class="form-check-label"
                                                            for="opt{{ $product->id }}{{ $option->id }}">
                                                            <small>{{ $option->option_name }}
                                                                (+{{ number_format($option->price_impact, 0, ',', '.') }})</small>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @endif

                                    <div class="mb-2">
                                        <input type="number" name="qty"
                                            class="form-control form-control-sm text-center" value="1" min="1"
                                            max="{{ $stock }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
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
            <div id="cartContainer">
                @include('kasir.orders.partials.cart', ['order' => $order])
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/pages/orders.js') }}"></script>
@endpush
