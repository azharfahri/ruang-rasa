@extends('layouts.admin')

@section('content')
<a href="{{ route('variant-types.options.index', $variantType) }}" class="btn btn-light mb-3">← Kembali</a>

<div class="card">
    <div class="card-body">
        <h4 class="mb-3">Edit Variant Option</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('variant-types.options.update', [$variantType, $option]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nama Option</label>
                <input type="text" name="option_name"
                       class="form-control @error('option_name') is-invalid @enderror"
                       value="{{ old('option_name', $option->option_name) }}" required>
                @error('option_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Harga Tambahan</label>
                <input type="number" name="price_impact"
                       class="form-control @error('price_impact') is-invalid @enderror"
                       value="{{ old('price_impact', $option->price_impact) }}" required>
                @error('price_impact')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-end">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
