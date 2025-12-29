@extends('layouts.admin')

@section('content')
<div class="container-fluid">
<div class="row">

{{-- PRODUK --}}
<div class="col-lg-8">
    <div class="card">
        <div class="card-body">
            <h5 class="fw-semibold mb-3">Produk</h5>

            <form method="POST"
                  action="{{ route('cashier.orders.update',$order) }}">
                @csrf
                @method('PUT')

                <div class="row g-2">
                    <div class="col-md-8">
                        <select name="product_id" class="form-select" required>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} -
                                    Rp {{ number_format($product->price) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="number"
                               name="qty"
                               class="form-control"
                               min="1"
                               value="1"
                               required>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            Tambah
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- KERANJANG --}}
<div class="col-lg-4">
    <div class="card">
        <div class="card-body">
            <h5 class="fw-semibold mb-3">Keranjang</h5>

            @foreach($order->items as $item)
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <strong>{{ $item->product->name }}</strong><br>
                        <small>x{{ $item->quantity }}</small>
                    </div>
                    <span>
                        Rp {{ number_format($item->subtotal) }}
                    </span>
                </div>
            @endforeach

            <hr>

            <h5 class="fw-bold">
                Total: Rp {{ number_format($order->total_price) }}
            </h5>

            <button class="btn btn-success w-100 mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#payModal">
                Bayar Cash
            </button>
        </div>
    </div>
</div>

</div>
</div>

@include('kasir.orders.pay-cash')
@endsection
