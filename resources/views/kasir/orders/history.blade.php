@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0">Riwayat Transaksi</h4>
                        <p class="text-muted small mb-0">Cabang: {{ auth()->user()->branch->name ?? 'Pusat' }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary px-3 py-2">Total {{ $orders->total() }} Pesanan</span>
                    </div>
                </div>

                {{-- Filter Section --}}
                <form action="{{ route('cashier.history') }}" method="GET" id="filterForm">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" id="searchHistory" class="form-control border-start-0"
                                    placeholder="Cari ID Order atau Pelanggan..." value="{{ request('search') }}"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="small fw-bold text-muted d-block mb-1">Status Pesanan</label>
                            <select name="status" id="statusHistory" class="form-select shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>PENDING
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                    PROCESSING</option>
                                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>READY</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>COMPLETED
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="small fw-bold text-muted d-block mb-1">Tampilkan</label>
                            <select name="limit" id="limitHistory" class="form-select shadow-sm">
                                <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10 baris</option>
                                <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25 baris</option>
                                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50 baris</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <a href="{{ route('cashier.history') }}" class="btn btn-light border shadow-sm w-100">
                                <i class="bi bi-arrow-clockwise"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Table Section --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3" width="100">Order</th>
                                <th class="py-3">Pelanggan & Waktu</th>
                                <th class="py-3">Items</th>
                                <th class="py-3">Total Pembayaran</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark">#{{ $order->id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-capitalize">{{ $order->customer_name ?? 'Guest' }}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $order->created_at->format('d M Y, H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($order->items->take(2) as $item)
                                                <span class="badge bg-white text-dark border fw-normal">
                                                    {{ $item->quantity }}x {{ $item->product->name }}
                                                </span>
                                            @endforeach
                                            @if ($order->items->count() > 2)
                                                <span class="badge bg-light text-primary border fw-normal">
                                                    +{{ $order->items->count() - 2 }} lainnya
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            Rp {{ number_format($order->total, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'completed' => 'bg-success',
                                                'processing' => 'bg-warning text-dark',
                                                'pending' => 'bg-danger',
                                                'ready' => 'bg-info text-white',
                                            ];
                                            $class = $statusClasses[$order->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $class }} rounded-pill px-3" style="min-width: 90px;">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ auth()->user()->hasRole('admincabang')
                                                ? route('admincabang.orders.print', $order)
                                                : route('cashier.orders.print', $order) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary" title="Cetak Struk">
                                                <i class="fa fa-print"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-detail"
                                                data-bs-toggle="modal" data-bs-target="#orderDetailModal"
                                                data-id="{{ $order->id }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0 text-muted fw-bold">Tidak ada riwayat transaksi ditemukan.</p>
                                        <small class="text-muted">Coba ubah kata kunci atau filter status Anda.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Section --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $orders->firstItem() ?? 0 }}</strong> -
                        <strong>{{ $orders->lastItem() ?? 0 }}</strong> dari <strong>{{ $orders->total() }}</strong> data
                    </div>
                    <div>
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h6 class="modal-title fw-bold">Detail Pesanan <span id="modalOrderId"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="modalContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 small text-muted">Mengambil data...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const rolePrefix = "{{ auth()->user()->hasRole('admincabang') ? 'admincabang' : 'cashier' }}";
    </script>
    <script src="{{ asset('assets/js/pages/order-history.js') }}"></script>
@endpush
