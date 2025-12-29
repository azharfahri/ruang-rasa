@if(isset($order))
<div class="modal fade" id="payModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('orders.pay.cash', $order->id) }}"
          class="modal-content">

        @csrf

        <div class="modal-header">
            <h5 class="modal-title">Pembayaran Cash</h5>
        </div>

        <div class="modal-body">
            <p>Total:
                <strong>Rp {{ number_format($order->total_price,0,',','.') }}</strong>
            </p>

            <input type="number"
                   name="paid_amount"
                   class="form-control"
                   placeholder="Uang diterima"
                   required>
        </div>

        <div class="modal-footer">
            <button class="btn btn-success w-100">
                Konfirmasi Bayar
            </button>
        </div>

    </form>
  </div>
</div>
@endif
