@extends('layouts.admin')

@section('content')
<a href="{{ route('product.variant-types.index', $product) }}" class="btn btn-light mb-3">← Kembali</a>

<div class="card">
    <div class="card-body">
        <h4 class="mb-3">Tambah Variant Type</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('product.variant-types.store', $product) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Nama Variant</label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Tipe Input</label>
                <select name="input_type"
                        class="form-select @error('input_type') is-invalid @enderror"
                        required>
                    <option value="">-- Pilih --</option>
                    <option value="radio" {{ old('input_type') == 'radio' ? 'selected' : '' }}>Radio</option>
                    <option value="checkbox" {{ old('input_type') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                </select>
                @error('input_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-end">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
