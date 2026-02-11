@extends('layouts.admin')

@section('content')
    <div class="container">

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
                        <div class="mb-3 border-bottom pb-3">
                            <div class="d-flex justify-content-between">
                                <span><strong>{{ $item->product->name }}</strong> (x{{ $item->quantity }})</span>
                                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label class="small">Qty Refund</label>
                                    <input type="number" name="items[{{ $item->id }}][qty]" class="form-control"
                                        min="0" max="{{ $item->quantity }}" placeholder="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Tipe Refund</label>
                                    <select name="items[{{ $item->id }}][type]" class="form-select select-type">
                                        <option value="return">Kembali Uang</option>
                                        <option value="exchange">Tukar Barang</option>
                                    </select>
                                </div>
                                <div class="col-md-5 d-none exchange-container">
                                    <label class="small">Produk Pengganti</label>
                                    <select name="items[{{ $item->id }}][exchange_product_id]" class="form-select">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($branchProducts as $bp)
                                            <option value="{{ $bp->product->id }}">{{ $bp->product->name }} (Stok:
                                                {{ $bp->stock }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
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
