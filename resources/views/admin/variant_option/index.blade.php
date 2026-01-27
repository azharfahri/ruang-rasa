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

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVariantOptionModal">
                    + Option
                </button>
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
                                <td>Rp {{ number_format($opt->price_impact, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('variant-types.options.edit', [$variantType, $opt]) }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <form action="{{ route('variant-types.options.destroy', [$variantType, $opt]) }}" method="POST"
                                        class="d-inline confirm-submit" data-type="delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"> Hapus </button>
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

    {{-- MODAL TAMBAH OPTION --}}
    <div class="modal fade" id="addVariantOptionModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form action="{{ route('variant-types.options.store', $variantType) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Tambah Option – {{ $variantType->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Nama Option</th>
                                    <th width="200">Harga Tambahan</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody id="optionRows">
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <input type="text" name="options[0][option_name]" class="form-control"
                                            placeholder="Masukan Nama Opsi" required>
                                    </td>
                                    <td>
                                        <input type="number" name="options[0][price_impact]" class="form-control"
                                            placeholder="Masukan Harga Tambahan" min="0" required>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-danger remove-row">
                                            ×
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="p-3 border-top">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addRow">
                                + Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/variant-option.js') }}"></script>
@endpush
