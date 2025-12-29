@extends('layouts.admin')

@section('content')
<a href="{{ route('branches.index') }}" class="btn btn-light mb-3">← Kembali</a>

<div class="card">
    <div class="card-body">
        <h4 class="mb-3">Tambah Cabang</h4>

        <form action="{{ route('branches.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Nama Cabang</label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="address"
                          class="form-control @error('address') is-invalid @enderror"
                          rows="3">{{ old('address') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Latitude</label>
                    <input type="text" name="latitude"
                           class="form-control @error('latitude') is-invalid @enderror"
                           value="{{ old('latitude') }}">
                    @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label>Longitude</label>
                    <input type="text" name="longitude"
                           class="form-control @error('longitude') is-invalid @enderror"
                           value="{{ old('longitude') }}">
                    @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Jam Buka</label>
                    <input type="time" name="open_time"
                           class="form-control @error('open_time') is-invalid @enderror"
                           value="{{ old('open_time') }}">
                    @error('open_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label>Jam Tutup</label>
                    <input type="time" name="close_time"
                           class="form-control @error('close_time') is-invalid @enderror"
                           value="{{ old('close_time') }}">
                    @error('close_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="text-end">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
