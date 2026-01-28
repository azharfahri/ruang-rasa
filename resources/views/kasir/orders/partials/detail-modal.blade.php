<div class="p-4">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <small class="text-muted d-block">Pelanggan</small>
            <span class="fw-bold">{{ $order->customer_name ?? 'Guest' }}</span>
        </div>
        <div class="text-end">
            <small class="text-muted d-block">Waktu Transaksi</small>
            <span class="fw-bold">{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <table class="table table-sm border-top">
        <thead class="bg-light">
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <div class="fw-bold">{{ $item->product->name }}</div>
                    @foreach($item->details as $detail)
                        <small class="text-muted d-block">
                            • {{ $detail->variantOption->option_name }}
                            @if ($detail->price_impact > 0)
                                <span>+{{ number_format($detail->price_impact, 0, ',', '.') }}</span>
                            @endif
                        </small>
                    @endforeach
                </td>
                <td class="text-center">{{ $item->quantity }}x</td>
                <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-light">
                <td colspan="2" class="fw-bold">TOTAL PEMBAYARAN</td>
                <td class="text-end fw-bold text-primary fs-5">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
