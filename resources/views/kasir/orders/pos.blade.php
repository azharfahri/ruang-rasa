@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6>{{ $product->name }}</h6>
                        <p>Rp {{ number_format($product->price,0,',','.') }}</p>

                        <form method="POST"
                              action="{{ route('cashier.orders.addItem',$order) }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button class="btn btn-primary btn-sm w-100">
                                Tambah
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-md-4">
        <h5>Keranjang</h5>
        <ul class="list-group mb-3">
            @foreach($order->items as $item)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $item->product->name }}</span>
                <strong>Rp {{ number_format($item->subtotal,0,',','.') }}</strong>
            </li>
            @endforeach
        </ul>

        <h5>Total: Rp {{ number_format($order->total,0,',','.') }}</h5>

        <button class="btn btn-success w-100 mt-2"
                data-bs-toggle="modal"
                data-bs-target="#payModal">
            Bayar
        </button>
    </div>
</div>

<div class="modal fade" id="payModal">
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('cashier.orders.pay.cash',$order) }}"
          class="modal-content">
        @csrf
        <div class="modal-header">
            <h5>Pembayaran Cash</h5>
        </div>
        <div class="modal-body">
            <p>Total: Rp {{ number_format($order->total,0,',','.') }}</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success w-100">
                Konfirmasi
            </button>
        </div>
    </form>
  </div>
</div>
@endsection
