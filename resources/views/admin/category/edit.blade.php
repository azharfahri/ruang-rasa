@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <h4><i class="fas fa-edit me-2"></i>Edit Kategori: {{ $category->name }}</h4>

            <hr>

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

            {{-- ASUMSI: Route update kategori Anda bernama 'admin.categories.update' --}}
            <form action="{{ route('admin.category.update', $category->id) }}" method="post">
                @csrf
                {{-- Menggunakan method spoofing untuk PUT --}}
                @method('PUT')

                <div class="row">

                    {{-- Nama Kategori --}}
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', $category->name) }}" required>
                    </div>

                    {{-- Tipe Kategori (Minuman/Makanan) --}}
                    {{-- ASUMSI: Kolom di database Anda bernama 'type' --}}
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Tipe Produk</label>
                        <select name="type" id="type" class="form-select" required>
                            {{--
                            ASUMSI: Nilai dari $category->type adalah 'minuman' atau 'makanan'.
                            Jika Anda menggunakan boolean (is_drink), ubah value dan pengecekan di bawah.
                        --}}
                            <option value="minuman" {{ old('type', $category->type) == 'minuman' ? 'selected' : '' }}>
                                Minuman
                            </option>
                            <option value="makanan" {{ old('type', $category->type) == 'makanan' ? 'selected' : '' }}>
                                Makanan/Snack
                            </option>
                        </select>
                    </div>

                    <div class="col-12 mt-3 text-end">
                        <a href="{{ route('admin.category.index') }}" class="btn btn-secondary me-2">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
