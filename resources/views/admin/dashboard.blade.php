@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col">
            <h4 class="fw-semibold">Admin Dashboard</h4>
            <p class="text-muted">Selamat datang, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <div class="owl-carousel counter-carousel owl-theme mb-4">

        <div class="item">
            <div class="card border-0 zoom-in bg-primary-subtle shadow-none">
                <div class="card-body text-center">
                    <i class="ti ti-users fs-7 text-primary mb-2"></i>
                    <p class="fw-semibold fs-3 text-primary mb-1">Pengguna</p>
                    <h5 class="fw-semibold text-primary mb-0">{{ $totalUsers }}</h5>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 zoom-in bg-success-subtle shadow-none">
                <div class="card-body text-center">
                    <i class="ti ti-package fs-7 text-success mb-2"></i>
                    <p class="fw-semibold fs-3 text-success mb-1">Produk</p>
                    <h5 class="fw-semibold text-success mb-0">{{ $totalProducts }}</h5>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 zoom-in bg-warning-subtle shadow-none">
                <div class="card-body text-center">
                    <i class="ti ti-category fs-7 text-warning mb-2"></i>
                    <p class="fw-semibold fs-3 text-warning mb-1">Kategori</p>
                    <h5 class="fw-semibold text-warning mb-0">{{ $totalCategories }}</h5>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 zoom-in bg-info-subtle shadow-none">
                <div class="card-body text-center">
                    <i class="ti ti-shopping-cart fs-7 text-info mb-2"></i>
                    <p class="fw-semibold fs-3 text-info mb-1">Pesanan</p>
                    <h5 class="fw-semibold text-info mb-0">{{ $totalOrders }}</h5>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 zoom-in bg-danger-subtle shadow-none">
                <div class="card-body text-center">
                    <i class="ti ti-check fs-7 text-danger mb-2"></i>
                    <p class="fw-semibold fs-3 text-danger mb-1">Selesai</p>
                    <h5 class="fw-semibold text-danger mb-0">{{ $completedOrder }}</h5>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 zoom-in bg-secondary-subtle shadow-none">
                <div class="card-body text-center">
                    <i class="ti ti-building-store fs-7 text-secondary mb-2"></i>
                    <p class="fw-semibold fs-3 text-secondary mb-1">Cabang</p>
                    <h5 class="fw-semibold text-secondary mb-0">{{ $totalBranches }}</h5>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-8 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Ringkasan Sistem</h5>
                    <p class="text-muted mb-4">
                        Monitoring data utama aplikasi Ruang Rasa
                    </p>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total User</span>
                            <strong>{{ $totalUsers }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total Produk</span>
                            <strong>{{ $totalProducts }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total Pesanan</span>
                            <strong>{{ $totalOrders }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Pesanan Selesai</span>
                            <strong>{{ $completedOrder }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total Cabang</span>
                            <strong>{{ $totalBranches }}</strong>
                        </li>
                    </ul>

                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex align-items-stretch">
            <div class="card w-100 text-bg-primary border-0">
                <div class="card-body">
                    <h5 class="fw-semibold text-white mb-2">Ruang Rasa</h5>
                    <p class="text-white-50 mb-4">
                        Admin Management System
                    </p>
                    <ul class="text-white fs-3">
                        <li>Manajemen Produk</li>
                        <li>Manajemen Order</li>
                        <li>Manajemen User & Role</li>
                        <li>Inventory Cabang</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Basic Bar Chart -->
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Basic Bar Chart</h4>
              <div id="chart-bar-basic"></div>
            </div>
          </div>
          <!-- End Basic Bar Chart -->

</div>
@endsection
