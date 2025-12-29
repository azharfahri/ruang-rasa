@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Order</h4>
    <a href="{{ route('cashier.orders.create') }}" class="btn btn-primary">
        + Order
    </a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>#{{ $order->id }}</td>
            <td>Rp {{ number_format($order->total,0,',','.') }}</td>
            <td>{{ strtoupper($order->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
