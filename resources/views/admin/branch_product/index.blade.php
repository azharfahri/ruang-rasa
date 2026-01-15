@extends('layouts.admin')

@section('content')
    <a href="{{ route('branch-products.index') }}" class="btn btn-light mb-3">
        ← Kembali ke Cabang
    </a>

    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h4 class="mb-0">Inventory Cabang</h4>
                    <small class="text-muted">{{ $branch->name }}</small>
                </div>

                <a href="{{ route('branch-products.create', $branch->id) }}" class="btn btn-primary">
                    + Tambah
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
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



            <table table id="inventoryTable" class="table table-hover align-middle">
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
                            <td>
                                Rp {{ number_format($item->final_price, 0, ',', '.') }}
                            </td>
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
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/branch-product.js') }}"></script>
@endpush
