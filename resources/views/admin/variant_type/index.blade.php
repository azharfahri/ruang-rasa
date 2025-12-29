@extends('layouts.admin')

@section('content')
<a href="{{ route('products.index') }}" class="btn btn-light mb-3">
    ← Kembali ke Product
</a>

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Variant Produk</h4>
                <small class="text-muted">
                    {{ $product->name }} · Total data: {{ $variantTypes->count() }}
                </small>
            </div>

            <a href="{{ route('product.variant-types.create', $product) }}"
               class="btn btn-primary">
                + Variant
            </a>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Cari variant...">
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
                        <th>Input</th>
                        <th>Option</th>
                        <th width="160" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variantTypes as $vt)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $vt->name }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($vt->input_type) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('variant-types.options.index', $vt) }}"
                                   class="btn btn-sm btn-info">
                                    Option ({{ $vt->options_count }})
                                </a>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('product.variant-types.edit', [$product, $vt]) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada variant
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
