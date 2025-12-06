@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4"><i class="fas fa-edit me-2"></i>Edit Kategori: {{ $category->name }}</h4>

                <hr>

                {{-- Tampilkan error (Menggunakan format alert yang lebih rapi) --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <strong>Gagal Menyimpan!</strong> Silakan periksa kembali input Anda.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Route update kategori Anda: 'admin.category.update' --}}
                <form action="{{ route('admin.category.update', $category->id) }}" method="post">
                    @csrf
                    {{-- Menggunakan method spoofing untuk PUT --}}
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">

                            {{-- Input Nama Kategori --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Kategori</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Dropdown Tipe Kategori (ENUM) --}}
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe Produk</label>
                                <select name="type" id="type"
                                    class="form-select @error('type') is-invalid @enderror" required>

                                    {{-- Looping melalui $categoryTypes dari Controller --}}
                                    @foreach ($categoryTypes as $type)
                                        @php
                                            // Tentukan apakah opsi ini harus dipilih
                                            $isSelected = old('type', $category->type) == $type;
                                        @endphp
                                        <option value="{{ $type }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.category.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
