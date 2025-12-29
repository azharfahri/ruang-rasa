@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-body">
        <h4>{{ $order->order_code }}</h4>

        <ul class="list-group mb-3">
            <li class="list-group-item">Customer: {{ $order->customer_name }}</li>
            <li class="list-group-item">Total: Rp {{ number_format($order->total_price,0,',','.') }}</li>
            <li class="list-group-item">Status: {{ strtoupper($order->status) }}</li>
        </ul>

        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <form method="POST" action="{{ route('cashier.orders.status',$order) }}">
            @csrf
            @method('PUT')
            <div class="d-flex gap-2 w-50">
                <select name="status" class="form-select">
                    <option value="processing">Processing</option>
                    <option value="ready">Siap Diambil</option>
                    <option value="completed">Selesai</option>
                </select>
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
