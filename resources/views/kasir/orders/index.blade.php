@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>Order</h4>
        <a href="{{ route('cashier.orders.create') }}" class="btn btn-primary">
            + Order
        </a>
    </div>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Order</th>
                <th>Item</th>
                <th>Total</th>
                <th>Status</th>
                <th width="200">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    {{-- ID + waktu --}}
                    <td>
                        <div class="fw-semibold">#{{ $order->id }}</div>
                        <small class="text-muted">
                            {{ $order->created_at->format('H:i') }}
                        </small>
                    </td>

                    {{-- ITEM --}}
                    <td>
                        @foreach ($order->items as $item)
                            <div>
                                {{ $item->product->name }}
                                x{{ $item->quantity }}
                            </div>
                        @endforeach
                    </td>

                    {{-- TOTAL --}}
                    <td>
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </td>

                    {{-- STATUS --}}
                    <td>
                        <span
                            class="badge bg-{{ $order->status === 'processing'
                                ? 'warning'
                                : ($order->status === 'ready'
                                    ? 'info'
                                    : ($order->status === 'completed'
                                        ? 'success'
                                        : 'secondary')) }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </td>

                    {{-- AKSI --}}
                    <td>
                        {{-- JIKA PENDING (Baru ditambahkan/Belum bayar) --}}
                        @if ($order->status === 'pending')
                            <div class="d-flex gap-1">
                                {{-- Tombol Lanjutkan (Ke halaman Create/Edit) --}}
                                <a href="{{ route('cashier.orders.create', $order->id) }}"
                                    class="btn btn-sm btn-primary flex-grow-1">
                                    Lanjutkan
                                </a>

                                {{-- Tombol Hapus --}}
                                <form method="POST" action="{{ route('cashier.orders.destroy', $order) }}"
                                    class="d-inline confirm-submit" data-type="delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- PROCESSING -> READY --}}
                        @if ($order->status === 'processing')
                            <form method="POST" action="{{ route('cashier.orders.ready', $order) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-info w-100 text-white">
                                    Tandai READY
                                </button>
                            </form>
                        @endif

                        {{-- READY -> COMPLETED --}}
                        @if ($order->status === 'ready')
                            <form method="POST" action="{{ route('cashier.orders.complete', $order) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-success w-100">
                                    Selesaikan
                                </button>
                            </form>
                        @endif

                        @if ($order->payment_status === 'settlement')
                            <a href="{{ route('cashier.orders.print', $order) }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary w-100 mt-1">
                                Cetak Struk
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Belum ada order
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
