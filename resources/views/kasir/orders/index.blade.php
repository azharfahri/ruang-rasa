@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">Pesanan</h4>
                    <small class="text-muted">Total data: {{ count($orders) }}</small>
                </div>
                @if (auth()->user()->hasRole('cashier'))
                    <a href="{{ route('cashier.orders.create') }}" class="btn btn-primary">+ Tambah Pesanan Baru</a>
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
                            <th width="120">Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="align-middle">
                                {{-- ID & Waktu: Dibuat lebih compact --}}
                                <td>
                                    <div class="fw-bold text-dark mb-0">#{{ $order->id }}</div>
                                    {{-- PICKUP CODE: Muncul dengan gaya Badge Tiket --}}
                                    @if ($order->pickup_code)
                                        <div class="my-1">
                                            <span class="badge border border-warning text-dark fw-bold pickup-code"
                                                style="background-color: #F9BC60; font-size: 0.75rem; letter-spacing: 0.5px;">
                                                <i class="bi bi-ticket-perforated"></i> {{ $order->pickup_code }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock small"></i> {{ $order->created_at->format('H:i') }}
                                    </div>
                                </td>

                                {{-- Nama Pelanggan: Fokus pada nama, tipe order diperhalus --}}
                                <td>
                                    <div class="fw-bold text-capitalize">{{ $order->customer_name ?? 'Guest' }}</div>
                                    <span class="badge rounded-pill bg-light text-secondary border-0 p-0"
                                        style="font-size: 0.7rem;">
                                        {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                                    </span>
                                </td>

                                {{-- Order Items: Penataan item agar tidak berantakan --}}
                                {{-- KOLOM ITEM --}}
                                <td class="order-items">
                                    @foreach ($order->items as $item)
                                        @php
                                            $returnQty = $item->refundItems->where('type', 'return')->sum('qty');
                                            $exchangeQty = $item->refundItems->where('type', 'exchange')->sum('qty');

                                            $isFullyRefunded = $returnQty >= $item->quantity;
                                            $isFullyExchanged = $exchangeQty >= $item->quantity;

                                            $textClass = 'text-dark';

                                            if ($isFullyRefunded) {
                                                $textClass = 'text-decoration-line-through text-muted';
                                            } elseif ($isFullyExchanged) {
                                                $textClass = 'text-warning';
                                            }
                                        @endphp

                                        <div class="d-flex align-items-center mb-1 gap-2">

                                            {{-- Badge Qty --}}
                                            <span
                                                class="badge {{ $isFullyRefunded ? 'bg-secondary-subtle text-muted' : 'bg-primary-subtle text-primary' }} rounded-2"
                                                style="width: 28px;">
                                                {{ $item->quantity }}x
                                            </span>

                                            {{-- Nama Produk --}}
                                            <div class="{{ $textClass }}" style="font-size: 0.875rem;">
                                                <span class="fw-medium">{{ $item->product->name }}</span>

                                                @if ($item->details->count() > 0)
                                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                        {{ $item->details->map(fn($d) => $d->variantOption->option_name)->join(', ') }}
                                                    </small>
                                                @endif
                                            </div>

                                            {{-- Badge kanan --}}
                                            <div class="ms-auto d-flex gap-1">
                                                @if ($returnQty > 0)
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                        REFUND: {{ $returnQty }}
                                                    </span>
                                                @endif

                                                @if ($exchangeQty > 0)
                                                    <span
                                                        class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                        EXCHANGE: {{ $exchangeQty }}
                                                    </span>
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach
                                </td>

                                {{-- KOLOM TOTAL --}}
                                <td class="text-nowrap align-middle">
                                    <div class="fw-bold text-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                </td>

                                {{-- KOLOM STATUS: Sekarang dijamin tidak akan ikut dicoret --}}
                                <td class="align-middle">
                                    <div class="d-flex flex-column justify-content-center align-items-center h-100 gap-1">
                                        @php
                                            $hasRefund = $order->refunds()->exists();
                                        @endphp

                                        @if ($hasRefund)
                                            <span class="badge bg-info w-100" style="font-size: 0.7rem; padding: 5px 0;">
                                                PARTIAL REFUND
                                            </span>
                                        @else
                                            @php
                                                $paymentBadgeColor =
                                                    [
                                                        'pending' => 'warning',
                                                        'settlement' => 'success',
                                                        'expire' => 'danger',
                                                        'cancel' => 'secondary',
                                                        'failure' => 'danger',
                                                        'refund' => 'primary',
                                                    ][$order->payment_status] ?? 'secondary';
                                            @endphp

                                            <span class="badge bg-{{ $paymentBadgeColor }} w-100"
                                                style="font-size: 0.7rem; padding: 5px 0;">
                                                {{ strtoupper($order->payment_status) }}
                                            </span>
                                        @endif

                                    </div>
                                </td>

                                {{-- Aksi: Tombol dirapikan ukurannya --}}
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if (auth()->user()->hasRole('cashier'))
                                            @if ($order->status === 'pending')
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('cashier.orders.create', $order->id) }}"
                                                        class="btn btn-sm btn-primary shadow-sm">
                                                        Lanjutkan
                                                    </a>

                                                    <form method="POST"
                                                        action="{{ route('cashier.orders.destroy', $order) }}"
                                                        class="confirm-submit" data-type="delete"
                                                        data-title="Batalkan Pesanan?"
                                                        data-text="Pesanan yang dibatalkan tidak bisa dikembalikan!">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                                                            Batalkan
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif ($order->status === 'processing')
                                                <form method="POST" action="{{ route('cashier.orders.ready', $order) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-info text-white w-100">READY</button>
                                                </form>
                                            @elseif ($order->status === 'ready')
                                                <form method="POST"
                                                    action="{{ route('cashier.orders.complete', $order) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-success w-100">SELESAI</button>
                                                </form>
                                            @endif
                                        @endif

                                        @if (auth()->user()->hasRole('admincabang') &&
                                                $order->payment_status === 'settlement' &&
                                                $order->created_at->gt(now()->subMinutes(10)))
                                            <a href="{{ route('admincabang.refund.create', $order) }}"
                                                class="btn btn-sm btn-warning fw-bold">
                                                <i class="bi bi-arrow-counterclockwise"></i> Refund
                                            </a>
                                            <div class="text-center" style="font-size: 0.65rem; color: #dc3545;">
                                                {{ $order->created_at->addMinutes(10)->format('H:i') }}
                                            </div>
                                        @endif

                                        @if ($order->payment_status === 'settlement')
                                            <a href="{{ auth()->user()->hasRole('admincabang') ? route('admincabang.orders.print', $order) : route('cashier.orders.print', $order) }}"
                                                target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-printer"></i> Struk
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="50"
                                        class="opacity-25 mb-2">
                                    <p class="text-muted small">Belum ada order hari ini</p>
                                </td>
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
