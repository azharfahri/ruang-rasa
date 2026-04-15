@extends('layouts.admin')

@section('content')
<a href="{{ route('product.variant-types.index', $variantType->product) }}" class="btn btn-light mb-3">← Kembali</a>

<div class="card">
    <div class="card-body">
        <h4 class="mb-3">Edit Tipe Varian</h4>

        <form action="{{ route('product.variant-types.update', [$variantType->product, $variantType]) }}" method="POST" class="confirm-submit" data-type="update">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nama Varian</label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $variantType->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Tipe Input</label>
                <select name="input_type"
                        class="form-select @error('input_type') is-invalid @enderror"
                        required>
                    <option value="radio" {{ old('input_type', $variantType->input_type) == 'radio' ? 'selected' : '' }}>Radio</option>
                    <option value="checkbox" {{ old('input_type', $variantType->input_type) == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                </select>
                @error('input_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
