@extends('layouts.admin')

@section('content')
<a href="{{ route('category.index') }}" class="btn btn-light mb-3">← Kembali</a>

<div class="card">
    <div class="card-body">
        <h4 class="mb-4">Tambah Kategori</h4>

        <form action="{{ route('category.store') }}" method="POST" class="confirm-submit" data-type="save">
            @csrf

            <div class="mb-3">
                <label>Nama Kategori</label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label>Tipe</label>
                <select name="type"
                        class="form-select @error('type') is-invalid @enderror">
                    @foreach($categoryTypes as $type)
                        <option value="{{ $type }}"
                            {{ old('type')==$type?'selected':'' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
