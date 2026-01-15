@extends('layouts.admin')

@section('content')
    <a href="{{ route('product.variant-types.index', $variantType->product) }}" class="btn btn-light mb-3">
        ← Kembali ke Variant
    </a>

    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">Option Variant</h4>
                    <small class="text-muted">
                        {{ $variantType->name }} · {{ $variantType->product->name }}
                        · Total data: {{ $options->count() }}
                    </small>
                </div>

                <a href="{{ route('variant-types.options.create', $variantType) }}" class="btn btn-primary">
                    + Option
                </a>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Cari option...">
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option>10 data</option>
                        <option>25 data</option>
                        <option>50 data</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Nama</th>
                            <th>Harga Tambahan</th>
                            <th width="160" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($options as $opt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $opt->option_name }}</td>
                                <td>
                                    Rp {{ number_format($opt->price_impact, 0, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('variant-types.options.edit', [$variantType, $opt]) }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <form action="{{ route('variant-types.options.destroy', [$variantType, $opt]) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus Variant?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada option
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
