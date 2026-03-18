@extends('layouts.admin')

@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li style="font-size: 0.85rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <h4 class="mb-3">Refund Order #{{ $order->id }}</h4>

        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                <p><strong>Total Order:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admincabang.refund.store', $order) }}">
            @csrf

            <div class="card mb-3">
                <div class="card-body">

                    <h6>Pilih Item Yang Direfund</h6>

                    @foreach ($order->items as $item)
                        @php
                            $totalRefunded = $item->refundItems->sum('qty');
                            $remainingQty = $item->quantity - $totalRefunded;
                        @endphp

                        @if ($remainingQty > 0)
                            <div class="card mb-3 border-light shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $item->product->name }}</h6>

                                            {{-- MENAMPILKAN DETAIL DARI TABEL order_item_details --}}
                                            <div class="mb-2">
                                                @foreach ($item->details as $detail)
                                                    {{-- Di dalam loop foreach($item->details as $detail) --}}
                                                    <span
                                                        class="badge rounded-pill bg-secondary-subtle text-secondary border px-2 py-1 me-1">
                                                        <i class="bi bi-dot"></i>
                                                        {{ $detail->variantOption->option_name }}

                                                        @if ($detail->price_impact > 0)
                                                            <span
                                                                class="fw-bold">(+{{ number_format($detail->price_impact, 0, ',', '.') }})</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>

                                            <span class="badge bg-info-subtle text-info">Sisa beli:
                                                {{ $remainingQty }}</span>
                                        </div>
                                        <div class="text-end text-primary fw-bold">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    {{-- Form Input Qty & Type tetap sama seperti sebelumnya --}}
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="small fw-bold">Qty Refund</label>
                                            <input type="number" name="items[{{ $item->id }}][qty]"
                                                class="form-control" min="0" max="{{ $remainingQty }}"
                                                value="0">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="small fw-bold">Tipe Refund</label>
                                            <select name="items[{{ $item->id }}][type]" class="form-select">
                                                <option value="return">Kembali Uang</option>
                                                <option value="exchange">Tukar Barang Baru</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach


                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alasan Refund</label>
                <textarea name="reason" class="form-control" required></textarea>
            </div>

            <button class="btn btn-danger">
                Proses Refund
            </button>

            <a href="{{ route('admincabang.orders.index') }}" class="btn btn-secondary">
                Batal
            </a>

        </form>
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('.select-type').forEach(select => {
            select.addEventListener('change', function() {
                const container = this.closest('.row').querySelector('.exchange-container');
                if (this.value === 'exchange') {
                    container.classList.remove('d-none');
                } else {
                    container.classList.add('d-none');
                }
            });
        });
    </script>
@endpush
