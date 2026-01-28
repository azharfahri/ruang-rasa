@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h4 class="fw-bold mb-4">Inventory Cabang</h4>

        {{-- Filter & Search --}}
        <form action="{{ route('cashier.penyimpanan.index') }}" method="GET" id="filterForm" class="mb-4">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="searchInventory" class="form-control border-start-0"
                               placeholder="Cari nama produk..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" id="statusFilter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="soldout" {{ request('status') == 'soldout' ? 'selected' : '' }}>Sold Out</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('cashier.penyimpanan.index') }}" class="btn btn-light border w-100">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Produk</th>
                        <th width="150">Stok</th>
                        <th width="120">Status</th>
                        <th width="200" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>
                            <td class="fw-bold">{{ $item->product->name }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                                    {{ $item->stock }} Unit
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $item->status == 'available' ? 'success' : 'danger' }} rounded-pill px-3">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('cashier.penyimpanan.stock', $item) }}" method="POST" class="d-flex gap-2 justify-content-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="stock" class="form-control form-control-sm"
                                           style="width: 80px" min="0" value="{{ $item->stock }}" required>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam d-block fs-1 mb-2"></i>
                                Produk tidak ditemukan dalam inventory cabang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInventory');
        const statusFilter = document.getElementById('statusFilter');

        // Submit saat status diubah
        statusFilter.addEventListener('change', () => filterForm.submit());

        // Debounce search
        let timer;
        searchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => filterForm.submit(), 800);
        });

        // Fokus ke akhir input search
        if(searchInput.value) {
            searchInput.focus();
            let val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }
    });
</script>
@endpush
