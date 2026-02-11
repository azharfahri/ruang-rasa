@extends('layouts.admin')

@section('content')
<div class="container">

    <h4 class="mb-3">Refund Order #{{ $order->id }}</h4>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
            <p><strong>Total Order:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admincabang.refund.store', $order) }}">
        @csrf

        <div class="card mb-3">
            <div class="card-body">

                <h6>Pilih Item Yang Direfund</h6>

                @foreach ($order->items as $item)
                    <div class="mb-3 border-bottom pb-2">
                        <div class="d-flex justify-content-between">
                            <span>
                                {{ $item->product->name }}
                                (x{{ $item->quantity }})
                            </span>
                            <span>
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <input type="number"
                               name="items[{{ $item->id }}]"
                               class="form-control mt-2"
                               min="0"
                               max="{{ $item->quantity }}"
                               placeholder="Qty refund">
                    </div>
                @endforeach

            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Alasan Refund</label>
            <textarea name="reason" class="form-control" required></textarea>
        </div>

        <button class="btn btn-danger">
            Proses Refund
        </button>

        <a href="{{ route('admincabang.orders.index') }}"
           class="btn btn-secondary">
            Batal
        </a>

    </form>
</div>
@endsection
