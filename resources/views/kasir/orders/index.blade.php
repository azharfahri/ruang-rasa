@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">Order</h4>
                    <small class="text-muted">Total data: {{ count($orders) }}</small>
                </div>
                @if (auth()->user()->hasRole('cashier'))
                    <a href="{{ route('cashier.orders.create') }}" class="btn btn-primary">+ Order Baru</a>
                @endif
            </div>

            <div class="row mb-3 mt-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Cari ID order atau nama produk...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="statusSelect" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="PENDING">PENDING</option>
                        <option value="PROCESSING">PROCESSING</option>
                        <option value="READY">READY</option>
                        <option value="COMPLETED">COMPLETED</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="limitSelect" class="form-select">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="orderTable">
                    <thead class="table-light">
                        <tr>
                            <th width="120">Order</th>
                            <th>Nama Pelanggan</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                {{-- ID untuk Search --}}
                                <td>
                                    <div class="fw-semibold order-id">#{{ $order->id }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>

                                {{-- Nama Pelanggan --}}
                                <td class="customer-name">
                                    <div class="fw-bold">{{ $order->customer_name ?? 'Guest' }}</div>
                                    <small
                                        class="text-muted">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</small>
                                </td>

                                {{-- Item untuk Search --}}
                                <td class="order-items">
                                    @foreach ($order->items as $item)
                                        <div class="small">
                                            <span class="badge bg-light text-dark border">{{ $item->quantity }}x</span>
                                            {{ $item->product->name }}
                                        </div>
                                    @endforeach
                                </td>

                                <td class="fw-bold">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if (auth()->user()->hasRole('admincabang'))
                                        {{-- Tampilan Status untuk Admin Cabang (Payment Status) --}}
                                        @php
                                            $paymentBadgeColor =
                                                [
                                                    'pending' => 'warning',
                                                    'settlement' => 'success',
                                                    'expire' => 'danger',
                                                    'cancel' => 'secondary',
                                                    'failure' => 'danger',
                                                    'refund' => 'info',
                                                ][$order->payment_status] ?? 'secondary';
                                        @endphp

                                        <span class="badge bg-{{ $paymentBadgeColor }}">
                                            {{ strtoupper($order->payment_status) }}
                                        </span>
                                    @else
                                        {{-- Tampilan Status untuk Kasir (Order Status) --}}
                                        <span class="badge bg-{{ $order->status === 'processing' ? 'warning' : ($order->status === 'ready' ? 'info' : ($order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'secondary'))) }}">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if (auth()->user()->hasRole('cashier'))
                                            @if ($order->status === 'pending')
                                                <a href="{{ route('cashier.orders.create', $order->id) }}"
                                                    class="btn btn-sm btn-primary flex-grow-1">Lanjutkan</a>
                                                <form method="POST" action="{{ route('cashier.orders.destroy', $order) }}"
                                                    class="d-inline confirm-submit" data-type="delete">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($order->status === 'processing')
                                                <form method="POST" action="{{ route('cashier.orders.ready', $order) }}"
                                                    class="w-100">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-info w-100 text-white">Tandai
                                                        READY</button>
                                                </form>
                                            @endif

                                            @if ($order->status === 'ready')
                                                <form method="POST"
                                                    action="{{ route('cashier.orders.complete', $order) }}" class="w-100">
                                                    @csrf @method('PATCH')
                                                    <button
                                                        class="btn btn-sm btn-success w-100 text-white">Selesaikan</button>
                                                </form>
                                            @endif
                                        @endif
                                        @if (auth()->user()->hasRole('admincabang') &&
                                                $order->payment_status === 'settlement' &&
                                                $order->created_at->gt(now()->subMinutes(10)))
                                            <a href="{{ route('admincabang.refund.create', $order) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-arrow-counterclockwise"></i> Refund
                                            </a>
                                            <small class="text-muted d-block">
                                                Batas refund: {{ $order->created_at->addMinutes(10)->format('H:i') }}
                                            </small>
                                        @endif


                                        @if ($order->payment_status === 'settlement')
                                            <a href="{{ auth()->user()->hasRole('admincabang')
                                                ? route('admincabang.orders.print', $order)
                                                : route('cashier.orders.print', $order) }}"
                                                target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-printer"></i> Struk
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada order</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted" id="tableInfo"></small>
                <div id="paginationControls">
                    <button class="btn btn-sm btn-outline-secondary" id="prevBtn">‹</button>
                    <span id="pageInfo" class="mx-2 small fw-bold"></span>
                    <button class="btn btn-sm btn-outline-secondary" id="nextBtn">›</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/order-list.js') }}"></script>
@endpush
