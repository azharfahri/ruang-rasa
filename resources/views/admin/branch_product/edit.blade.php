@extends('layouts.admin')

@section('content')
<a href="{{ route('branch-products.show', $branchProduct->branch_id) }}"
   class="btn btn-light mb-3">
    ← Kembali ke Inventory
</a>

<div class="card">
    <div class="card-body">
        <form action="{{ route('branch-products.update', $branchProduct) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="branch_id" value="{{ $branchProduct->branch_id }}">

            <div class="mb-3">
                <label>Produk</label>
                <select name="product_id" class="form-select">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            {{ $branchProduct->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stock" class="form-control"
                       value="{{ $branchProduct->stock }}">
            </div>

            <div class="mb-3">
                <label>Harga Override</label>
                <input type="number" name="price_override" class="form-control"
                       value="{{ $branchProduct->price_override }}">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="available" {{ $branchProduct->status=='available'?'selected':'' }}>
                        Available
                    </option>
                    <option value="soldout" {{ $branchProduct->status=='unavailable'?'selected':'' }}>
                        Unavailable
                    </option>
                </select>
            </div>

            <button class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
