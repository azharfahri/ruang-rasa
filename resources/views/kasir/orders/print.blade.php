<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->id }}</title>
    <style>
        :root {
            /* Default setting untuk 58mm */
            --ticket-width: 58mm;
            --print-width: 48mm;
            /* Area cetak bersih (dikurangi margin printer) */
            --font-main: 11px;
            --font-header: 14px;
        }

        /* Jika layar/printer terdeteksi 80mm atau user memilih class .size-80 */
        .size-80 {
            --ticket-width: 80mm;
            --print-width: 72mm;
            --font-main: 13px;
            --font-header: 18px;
        }

        @page {
            size: var(--ticket-width) auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: var(--print-width);
            margin: 0 auto;
            padding: 5px 0 20px 0;
            font-size: var(--font-main);
            color: #000;
            line-height: 1.3;
        }

        /* Pengaturan Visual di Browser (Non-Print) */
        @media screen {
            body {
                background-color: #f0f0f0;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-top: 50px;
                width: 100%;
            }

            .receipt-content {
                background: white;
                width: var(--print-width);
                padding: 15px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .no-print {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 9999;
                background: #333;
                padding: 10px;
                display: flex;
                justify-content: center;
                gap: 10px;
            }
        }

        /* Reset untuk Printer */
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: none;
                width: var(--print-width);
            }

            .receipt-content {
                width: 100%;
            }
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .header {
            margin-bottom: 10px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .item-row {
            margin-bottom: 8px;
        }

        .variant-list {
            margin-left: 10px;
            font-size: 0.9em;
            font-style: italic;
        }

        .total-section {
            margin-top: 5px;
        }

        .footer {
            margin-top: 15px;
            font-size: 0.9em;
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="changeSize('58')"
            style="padding: 8px; border-radius: 4px; border: none; background: #666; color: white; cursor: pointer;">Mode
            58mm</button>
        <button onclick="changeSize('80')"
            style="padding: 8px; border-radius: 4px; border: none; background: #666; color: white; cursor: pointer;">Mode
            80mm</button>
        <button onclick="window.print()"
            style="padding: 8px 15px; border-radius: 4px; border: none; background: #28a745; color: white; cursor: pointer; font-weight: bold;">PRINT</button>
        <button onclick="window.close()"
            style="padding: 8px 15px; border-radius: 4px; border: none; background: #dc3545; color: white; cursor: pointer;">TUTUP</button>
    </div>

    <div class="receipt-content">
        <div class="text-center header">
            <strong style="font-size: var(--font-header);">RUANG RASA</strong><br>
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
        <div class="flex text-nowrap">
            <span>Pelanggan: {{ Str::limit($order->customer_name ?? 'Guest', 20) }}</span>
        </div>

        <div class="line"></div>

        @foreach ($order->items as $item)
            @php
                $isRefunded = $item->refundItems->isNotEmpty();
            @endphp
            <div class="item-row" style="{{ $isRefunded ? 'text-decoration: line-through; opacity:0.6;' : '' }}">
                <div class="fw-bold">{{ strtoupper($item->product->name) }}</div>
                <div class="flex">
                    <span>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>

                @if ($item->details && $item->details->count() > 0)
                    <div class="variant-list">
                        @foreach ($item->details as $detail)
                            <div class="flex">
                                <span>- {{ $detail->variantOption->option_name }}</span>
                                @if ($detail->price_impact > 0)
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
            <div class="flex fw-bold" style="font-size: 1.2em;">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>

            @php $transaction = $order->transaction; @endphp

            @if ($transaction)
                <div class="flex">
                    <span>METODE</span>
                    <span>{{ strtoupper($transaction->payment_method) }}</span>
                </div>
                <div class="flex">
                    <span>BAYAR</span>
                    <span>Rp
                        {{ number_format($transaction->cash_received ?? $transaction->amount, 0, ',', '.') }}</span>
                </div>
                @if (($transaction->change_amount ?? 0) > 0)
                    <div class="flex">
                        <span>KEMBALIAN</span>
                        <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
            @else
                <div class="flex">
                    <span>STATUS</span>
                    <span class="fw-bold">BELUM BAYAR</span>
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
    </div>

    <script>
        function changeSize(size) {
            if (size === '80') {
                document.body.classList.add('size-80');
            } else {
                document.body.classList.remove('size-80');
            }
        }

        // Auto print (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>
