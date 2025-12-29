@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <h4>Inventory Cabang</h4>
            <a href="{{ route('branch-products.create') }}" class="btn btn-primary">+ Tambah</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Cabang</th>
                    <th>Produk</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->branch->name }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->stock }}</td>
                    <td>Rp {{ number_format($item->final_price,0,',','.') }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                    <td>
                        <a href="{{ route('branch-products.edit',$item) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('branch-products.destroy',$item) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
