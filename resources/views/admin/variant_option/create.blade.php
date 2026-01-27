@extends('layouts.admin')

@section('content')
<a href="{{ route('variant-types.options.index', $variantType) }}"
   class="btn btn-light mb-3">
    ← Kembali
</a>

<div class="card">
    <div class="card-body">
        <h4 class="mb-3">Tambah Option ({{ $variantType->name }})</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('variant-types.options.store', $variantType) }}" class="confirm-submit" data-type="save">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Option</label>
                <input type="text"
                       name="option_name"
                       class="form-control @error('option_name') is-invalid @enderror"
                       value="{{ old('option_name') }}"
                       required>
                @error('option_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Harga Tambahan</label>
                <input type="number"
                       name="price_impact"
                       class="form-control @error('price_impact') is-invalid @enderror"
                       value="{{ old('price_impact') }}"
                       required>
                @error('price_impact')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-end">
                <button class="btn btn-primary" type="submit">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
