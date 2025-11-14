@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <h4>Tambah Kategori</h4>
            <form action="{{ route('admin.category.store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            <label for="tb-name">Nama Kategori</label>
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <label class="form-label">Jenis Kategori</label>
                            <select name="type" class="form-select">
                                <option value="Makanan">Makanan</option>
                                <option value="Minuman">Minuman</option>
                            </select>
                            @error('type')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-md-flex align-items-center">
                            <div class="ms-auto mt-3 mt-md-0">
                                <button type="submit" class="btn btn-primary">
                                    <i></i>
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
