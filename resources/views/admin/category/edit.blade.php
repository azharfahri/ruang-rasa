@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-body">
        <h4>Edit Product</h4>

        {{-- tampilkan error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.product.update', $product->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                {{-- Kategori --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-select" name="category_id" required>
                        <option disabled>Pilih Kategori</option>
                        @foreach ($categories as $data)
                            <option value="{{ $data->id }}"
                                {{ $product->category_id == $data->id ? 'selected' : '' }}>
                                {{ $data->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nama Produk --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',$product->name) }}" required>
                </div>

                {{-- Deskripsi --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="description" rows="3" required>{{ old('description',$product->description) }}</textarea>
                </div>

                {{-- Harga --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" class="form-control" name="price"
                           value="{{ old('price',$product->price) }}" required>
                </div>

                {{-- Stok --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" class="form-control" name="stock"
                           value="{{ old('stock',$product->stock) }}" required>
                </div>

                {{-- Varian Tambahan (opsional) --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Varian Tambahan (opsional)</label>
                    <textarea class="form-control" name="variants"
                        placeholder="Contoh: Large, Extra Ice, dll">{{ old('variants', $product->variants ?? '') }}</textarea>
                    <small class="text-muted">Pisahkan dengan koma bila lebih dari satu.</small>
                </div>

                {{-- Gambar --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Gambar</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <small class="text-muted d-block">Kosongkan jika tidak ingin mengubah gambar.</small>
                    @if ($product->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$product->image) }}" alt="current image"
                                 class="rounded" width="120">
                        </div>
                    @endif
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
