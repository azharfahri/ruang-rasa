<aside class="left-sidebar with-vertical">
    <div>
        {{-- Logo --}}
        <div class="brand-logo d-flex align-items-center justify-content-between px-3 py-3">
            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}" class="text-nowrap logo-img">
                @elseif(auth()->user()->hasRole('cashier'))
                    <a href="{{ route('cashier.dashboard') }}" class="text-nowrap logo-img">
            @endif
            <img src="{{ asset('assets/images/logos/dark-logo.svg') }}" class="dark-logo">
            <img src="{{ asset('assets/images/logos/light-logo.svg') }}" class="light-logo">
            </a>

            <a href="javascript:void(0)" class="sidebartoggler d-xl-none">
                <i class="ti ti-x"></i>
            </a>
        </div>

        {{-- Sidebar --}}
        <nav class="sidebar-nav scroll-sidebar">
            <ul id="sidebarnav">

                {{-- DASHBOARD --}}
                <li class="sidebar-item">
                    @if (auth()->user()->hasRole('admin'))
                        <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i class="ti ti-layout-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    @elseif(auth()->user()->hasRole('cashier'))
                        <a class="sidebar-link {{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}"
                            href="{{ route('cashier.dashboard') }}">
                            <i class="ti ti-layout-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    @endif
                </li>

                {{-- TRANSAKSI (CASHIER ONLY) --}}
                @if (auth()->user()->hasRole('cashier'))
                    <li class="nav-small-cap">TRANSAKSI</li>

                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('cashier.orders.*') ? 'active' : '' }}"
                            href="{{ route('cashier.orders.index') }}">
                            <i class="ti ti-receipt"></i>
                            <span>Order</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('cashier.orders.history') ? 'active' : '' }}"
                            href="{{ route('cashier.orders.history') }}">
                            <i class="ti ti-history"></i>
                            <span>Riwayat Order</span>
                        </a>
                    </li>
                @endif

                {{-- ADMIN ONLY --}}
                @if (auth()->user()->hasRole('admin'))
                    <li class="nav-small-cap">MANAJEMEN</li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('roles.index') }}">
                            <i class="ti ti-shield"></i>
                            <span>Role</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('users.index') }}">
                            <i class="ti ti-user"></i>
                            <span>User</span>
                        </a>
                    </li>

                    <li class="nav-small-cap">MASTER DATA</li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('branches.index') }}">
                            <i class="ti ti-building-store"></i>
                            <span>Cabang</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('category.index') }}">
                            <i class="ti ti-category"></i>
                            <span>Kategori</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('products.index') }}">
                            <i class="ti ti-package"></i>
                            <span>Produk</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('branch-products.index') }}">
                            <i class="ti ti-box"></i>
                            <span>Penyimpanan</span>
                        </a>


                    </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
