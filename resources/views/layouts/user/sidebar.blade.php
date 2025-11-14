<!-- Offcanvas -->
@auth
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart" aria-labelledby="My Cart">
        <div class="offcanvas-header justify-content-center">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        {{-- BERI ID PADA offcanvas-body AGAR BISA DI-TARGET OLEH JAVASCRIPT --}}
        <div class="offcanvas-body" id="dynamic-cart-content">
            {{-- SERTAKAN FILE PARTIAL BARU DI SINI --}}
            @include('partials.offcanvas-cart-content', ['latestOrder' => $latestOrder ?? null])
        </div>
    </div>
@endauth
