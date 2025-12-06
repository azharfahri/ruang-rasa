@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">Tambah Kategori Baru</h4>

                {{-- Tampilkan error dari Controller, misalnya error validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal Menyimpan!</strong> Silakan periksa kembali input Anda.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.category.store') }}" method="post">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">

                            {{-- Input Nama Kategori --}}
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="tb-name" name="name" value="{{ old('name') }}" required
                                    placeholder="Contoh: Kopi Dingin">
                                <label for="tb-name">Nama Kategori</label>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Dropdown Jenis Kategori (Type) --}}
                            <div class="mb-3">
                                <label for="select-type" class="form-label">Jenis Kategori (Tipe)</label>
                                <select name="type" id="select-type"
                                    class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="" disabled selected>Pilih Tipe Kategori</option>

                                    {{-- Looping berdasarkan $categoryTypes dari Controller --}}
                                    @foreach ($categoryTypes as $type)
                                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                            {{-- Menampilkan nama yang lebih rapi untuk user --}}
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('admin.category.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
