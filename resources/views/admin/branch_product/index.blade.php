@extends('layouts.admin')

@section('content')
    <div class="mb-3">
        <a href="{{ route('branch-products.index') }}" class="btn btn-light">
            ← Kembali ke Cabang
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Inventory Cabang</h4>
                    <small class="text-muted">{{ $branch->name }}</small>
                </div>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    + Tambah Produk
                </button>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari produk cabang...">
                </div>
                <div class="col-md-3">
                    <select id="limitSelect" class="form-select">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                    </select>
                </div>
            </div>

            <table id="inventoryTable" class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Produk</th>
                        <th width="100">Stok</th>
                        <th width="150">Harga</th>
                        <th width="120">Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="product-name">{{ $item->product->name }}</td>
                            <td>{{ $item->stock }}</td>
                            <td>Rp {{ number_format($item->final_price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $item->isAvailable() ? 'success' : 'secondary' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('branch-products.edit', $item) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                                <form action="{{ route('branch-products.destroy', $item) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin hapus produk ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Belum ada produk di cabang ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between mt-3">
                <small class="text-muted" id="tableInfo"></small>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" id="prevBtn">‹</button>
                    <span id="pageInfo" class="mx-2"></span>
                    <button class="btn btn-sm btn-outline-secondary" id="nextBtn">›</button>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL TAMBAH PRODUK --}}
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form action="{{ route('branch-products.store', $branch->id) }}" method="POST">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Produk ke Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">Pilih</th>
                                    <th>Produk</th>
                                    <th width="120">Stok</th>
                                    <th width="180">Harga Override</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input product-checkbox"
                                                name="products[{{ $product->id }}][selected]">
                                        </td>
                                        {{-- REVISI: Tambahkan class product-name di sini agar JS tidak error --}}
                                        <td class="product-name">{{ $product->name }}</td>
                                        <td>
                                            <input type="number" class="form-control stock-input"
                                                name="products[{{ $product->id }}][stock]"
                                                min="0" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control price-input"
                                                name="products[{{ $product->id }}][price_override]"
                                                min="0">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/branch-product.js') }}"></script>
@endpush
