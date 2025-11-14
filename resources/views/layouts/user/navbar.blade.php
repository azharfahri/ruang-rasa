

{{-- OFFCANVAS SEARCH (biarkan) --}}
<div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasSearch" aria-labelledby="Search">
    <div class="offcanvas-header justify-content-center">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Pencarian</span>
            </h4>
            <form role="search" action="#" method="GET" class="d-flex mt-3 gap-0">
                <input class="form-control rounded-start rounded-0 bg-light" type="text"
                    placeholder="Kamu nyari produk apa?" aria-label="Cari produk...">
                <button class="btn btn-dark rounded-end rounded-0" type="submit">Cari</button>
            </form>
        </div>
    </div>
</div>


<header>
    <div class="container-fluid">
        <div class="row py-3 border-bottom">

            {{-- LOGO --}}
            <div class="col-sm-4 col-lg-3 text-center text-sm-start">
                <div class="main-logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('user/images/logo.png') }}" alt="logo" class="img-fluid">
                    </a>
                </div>
            </div>

            {{-- SEARCH BAR DESKTOP --}}
            <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-5 d-none d-lg-block">
                <div class="search-bar row bg-light p-2 my-2 rounded-4">
                    <div class="col-11 col-md-11">
                        <form id="search-form" class="text-center" action="#" method="GET">
                            <input type="text" class="form-control border-0 bg-transparent"
                                placeholder="Cari lebih dari 20.000 produk" />
                        </form>
                    </div>
                    <div class="col-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- IKON & DROPDOWN --}}
            <div class="col-sm-8 col-lg-4 d-flex justify-content-end gap-5 align-items-center mt-4 mt-sm-0 justify-content-center justify-content-sm-end">

                {{-- SUPPORT NUMBER --}}
                <div class="support-box text-end d-none d-xl-block">
                    <span class="fs-6 text-muted">Butuh Bantuan?</span>
                    <h5 class="mb-0">+6289646328037</h5>
                </div>

                {{-- IKON --}}
                <ul class="d-flex justify-content-end list-unstyled m-0">

                    {{-- WISHLIST --}}
                    <li>
                        <a href="#" class="rounded-circle bg-light p-2 mx-1">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <use xlink:href="#heart"></use>
                            </svg>
                        </a>
                    </li>

                    {{-- USER DROPDOWN --}}
                    <li class="dropdown">
                        <a href="#" class="rounded-circle bg-light p-2 mx-1" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <use xlink:href="#user"></use>
                            </svg>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            @guest
                                <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                                <li><a class="dropdown-item" href="{{ route('register') }}">Register</a></li>
                            @else
                                <li>
                                    <a class="dropdown-item" data-bs-toggle="offcanvas"
                                        href="#offcanvasCart">Lihat Keranjang</a>
                                </li>
                                <li><a class="dropdown-item" href="#">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            @endguest
                        </ul>
                    </li>

                    {{-- CART ICON (MOBILE) --}}
                    <li class="d-lg-none">
                        <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasCart">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <use xlink:href="#cart"></use>
                            </svg>
                        </a>
                    </li>

                    {{-- SEARCH ICON (MOBILE) --}}
                    <li class="d-lg-none">
                        <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasSearch">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <use xlink:href="#search"></use>
                            </svg>
                        </a>
                    </li>
                </ul>

                {{-- CART BUTTON DESKTOP --}}
                @auth
                    <div class="cart text-end d-none d-lg-block dropdown">
                        <button class="border-0 bg-transparent d-flex flex-column gap-2 lh-1" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart">
                            <span class="fs-6 text-muted">Keranjang</span>
                            <span class="cart-total fs-5 fw-bold">
                                {{ isset($latestOrder) ? 'Rp '.number_format($latestOrder->total_harga, 0, ',', '.') : 'Rp 0' }}
                            </span>
                        </button>
                    </div>
                @endauth

            </div>
        </div>
    </div>
</header>
