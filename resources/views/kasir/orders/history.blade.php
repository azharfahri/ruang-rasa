@extends('layouts.admin')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="fw-semibold mb-3">Riwayat Order</h4>

        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>Rp {{ number_format($order->total,0,',','.') }}</td>
                    <td>{{ strtoupper($order->status) }}</td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
