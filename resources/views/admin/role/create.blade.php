@extends('layouts.admin')

@section('content')
    <a href="{{ route('roles.index') }}" class="btn btn-light mb-3">← Kembali</a>

    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">Tambah Peran</h4>

            <form action="{{ route('roles.store') }}" method="POST" class="confirm-submit" data-type="save">
                @csrf

                <div class="mb-3">
                    <label>Nama Peran</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
