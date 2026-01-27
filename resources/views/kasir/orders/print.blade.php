<!DOCTYPE html>
<html>
<head>
    <title>Struk #{{ $order->id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: auto; padding: 10px; }
        .text-center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 10px 0; }
        .flex { display: flex; justify-content: space-between; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="text-center">
        <strong>RUANG RASA</strong><br>
        {{ auth()->user()->branch->name ?? 'Cabang Utama' }}<br>
        <small>{{ now()->format('d/m/Y H:i') }}</small>
    </div>

    <div class="line"></div>
    <small>No: #{{ $order->id }} | Kasir: {{ auth()->user()->name }}</small>
    <div class="line"></div>

    @foreach($order->items as $item)
        <div class="flex">
            <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
            <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @foreach($item->details as $detail)
            <small style="display: block; margin-left: 10px;">
                - {{ $detail->variantOption->option_name }}
            </small>
        @endforeach
    @endforeach

    <div class="line"></div>
    <div class="flex">
        <strong>TOTAL</strong>
        <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
    </div>

    <div class="line"></div>
    <div class="text-center">
        <small>Terima Kasih Atas Kunjungan Anda</small>
    </div>
</body>
</html>
