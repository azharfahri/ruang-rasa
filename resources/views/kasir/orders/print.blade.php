<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->id }}</title>
    <style>
        /* Pengaturan Kertas Thermal */
        @page {
            size: 58mm auto; /* Menyesuaikan printer thermal standar */
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 280px; /* Lebar area cetak */
            margin: auto;
            padding: 10px;
            font-size: 12px;
            color: #000;
            line-height: 1.2;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .header { margin-bottom: 10px; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .flex { display: flex; justify-content: space-between; align-items: flex-start; }

        .item-row { margin-bottom: 8px; }
        .variant-list { margin-left: 10px; font-size: 11px; }

        .total-section { margin-top: 5px; }
        .footer { margin-top: 15px; font-size: 11px; }

        /* Tombol Navigasi Saat Preview Browser */
        @media print {
            .no-print { display: none; }
            body { padding: 5px; margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: center; padding: 10px; background: #eee; margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 15px; cursor: pointer;">Cetak (Print)</button>
        <button onclick="window.close()" style="padding: 8px 15px; cursor: pointer;">Tutup Halaman</button>
    </div>

    <div class="text-center header">
        <strong style="font-size: 16px;">RUANG RASA</strong><br>
        {{ $order->branch->name ?? 'Cabang Ruang Rasa' }}<br>
        <small>{{ $order->branch->address ?? 'Alamat Cabang' }}</small>
    </div>

    <div class="line"></div>

    <div class="flex">
        <span>No: #{{ $order->id }}</span>
        <span>{{ $order->created_at->format('d/m/y H:i') }}</span>
    </div>
    <div class="flex">
        <span>Kasir: {{ $order->cashier->name ?? auth()->user()->name }}</span>
    </div>
    <div class="flex">
        <span>Pelanggan: {{ $order->customer_name ?? "Guest"}}</span>
    </div>

    <div class="line"></div>

    @foreach($order->items as $item)
        <div class="item-row">
            <div class="fw-bold">{{ strtoupper($item->product->name) }}</div>
            <div class="flex">
                <span>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>

            {{-- Detail Varian Produk --}}
            @if($item->details && $item->details->count() > 0)
                <div class="variant-list">
                    @foreach($item->details as $detail)
                        <div class="flex text-muted">
                            <span>- {{ $detail->variantOption->option_name }}</span>
                            @if($detail->price_impact > 0)
                                <span>+{{ number_format($detail->price_impact, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach

    <div class="line"></div>

    <div class="total-section">
        <div class="flex fw-bold" style="font-size: 14px;">
            <span>TOTAL</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>

        {{-- Logika Relasi Transaksi Tunggal (Berdasarkan Skema Kamu) --}}
        @if($order->transaction)
            <div class="flex">
                <span>METODE</span>
                <span>{{ strtoupper($order->transaction->payment_method) }}</span>
            </div>
            <div class="flex">
                <span>BAYAR</span>
                <span>Rp {{ number_format($order->transaction->amount, 0, ',', '.') }}</span>
            </div>
            {{-- Menghitung Kembalian Jika Ada Data --}}
            @if($order->transaction->amount > $order->total)
            <div class="flex">
                <span>KEMBALI</span>
                <span>Rp {{ number_format($order->transaction->amount - $order->total, 0, ',', '.') }}</span>
            </div>
            @endif
        @else
            <div class="flex">
                <span>METODE</span>
                <span>CASH</span>
            </div>
        @endif
    </div>

    <div class="line"></div>

    <div class="text-center footer">
        <strong>TERIMA KASIH</strong><br>
        Selamat menikmati hidangan kami.<br>
        Kritik & Saran: @ruangrasa.id<br>
        <br>
        *** {{ now()->format('d/m/Y H:i:s') }} ***
    </div>

</body>
</html>
