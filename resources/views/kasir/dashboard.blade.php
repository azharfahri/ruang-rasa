@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6>Total Order</h6>
                <h3>{{ $totalOrders }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6>Order Hari Ini</h6>
                <h3>{{ $todayOrders }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6>Total Pendapatan</h6>
                <h3>Rp {{ number_format($totalIncome,0,',','.') }}</h3>
            </div>
        </div>
    </div>
</div>
@endsection
