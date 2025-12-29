@extends('layouts.admin')

@section('content')
<a href="{{ route('branch-products.index') }}" class="btn btn-light mb-3">← Kembali</a>

<div class="card">
    <div class="card-body">
        <form action="{{ route('branch-products.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Cabang</label>
                <select name="branch_id" class="form-select" required>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Produk</label>
                <select name="product_id" class="form-select" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stock" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Harga Override</label>
                <input type="number" name="price_override" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>

            <button class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
