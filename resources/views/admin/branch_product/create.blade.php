@extends('layouts.admin')

@section('content')

<a href="{{ route('branch-products.show', $branch->id) }}"
   class="btn btn-light mb-3">
    ← Kembali
</a>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">

        <form action="{{ route('branch-products.store') }}" method="POST">
            @csrf

            <input type="hidden" name="branch_id" value="{{ $branch->id }}">

            <div class="mb-3">
                <label class="form-label">Produk</label>
                <select name="product_id" class="form-select" required>
                    <option value="">-- pilih produk --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stock"
                       class="form-control"
                       min="0"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga Override</label>
                <input type="number"
                       name="price_override"
                       class="form-control"
                       min="0">
                <small class="text-muted">
                    kosongkan jika pakai harga default
                </small>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary">
                    Simpan
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
