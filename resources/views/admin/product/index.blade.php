@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Product</h4>
                <small class="text-muted">Total data: {{ count($products) }}</small>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari produk...">
            </div>
            <div class="col-md-3">
                <select id="limitSelect" class="form-select">
                    <option value="10">10 data</option>
                    <option value="25">25 data</option>
                    <option value="50">50 data</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Foto</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>Rp {{ number_format($product->price,0,',','.') }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" width="60" class="rounded">
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('product.variant-types.index', $product) }}"
                               class="btn btn-sm btn-info">Variant</a>

                            <a href="{{ route('products.edit', $product) }}"
                               class="btn btn-sm btn-warning">Edit</a>

                            <form action="{{ route('products.destroy', $product) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Hapus produk?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <small class="text-muted" id="tableInfo"></small>
            <div>
                <button class="btn btn-sm btn-outline-secondary" id="prevBtn">‹</button>
                <span id="pageInfo" class="mx-2"></span>
                <button class="btn btn-sm btn-outline-secondary" id="nextBtn">›</button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/pages/product.js') }}"></script>
@endpush
