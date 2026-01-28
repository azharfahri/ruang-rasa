@extends('layouts.admin')

@section('content')
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Dashboard Kasir</h2>
            <span class="badge bg-primary px-3 py-2">{{ now()->translatedFormat('d F Y') }}</span>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-info border-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info p-3 rounded text-white"><i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Total Order</p>
                            <h3 class="fw-bold mb-0 fs-5">{{ number_format($totalOrders) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-warning border-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning p-3 rounded text-white"><i
                                class="fas fa-calendar-check fa-2x"></i></div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Order Hari Ini</p>
                            <h3 class="fw-bold mb-0 fs-5">{{ number_format($todayOrders) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-success border-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success p-3 rounded text-white"><i class="fas fa-calculator fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Total Pendapatan</p>
                            <h3 class="fw-bold mb-0 fs-5 text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-primary border-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary p-3 rounded text-white"><i
                                class="fas fa-hand-holding-usd fa-2x"></i></div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Pendapatan Hari Ini</p>
                            <h3 class="fw-bold mb-0 fs-5 text-primary">Rp {{ number_format($todayIncome, 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Tren Penjualan</h5>
                        <select class="form-select form-select-sm border-primary w-auto chart-filter" data-type="sales">
                            <option value="day">Minggu Ini</option>
                            <option value="month">Bulan di Tahun Ini</option>
                            <option value="year">Semua Tahun</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div id="salesChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Status Pesanan</h5>
                        <select class="form-select form-select-sm border-primary w-auto chart-filter" data-type="status">
                            <option value="day">Hari Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="year">Tahun Ini</option>
                        </select>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div id="statusChart" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">Top 5 Produk Terlaris</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Total Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $index => $item)
                                        <tr>
                                            <td class="ps-4">{{ $index + 1 }}</td>
                                            <td class="fw-bold">{{ $item->product->name }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-light text-dark border p-2 px-3">{{ $item->total_qty }}
                                                    Unit</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">Tidak ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.dashboardData = {
            salesLabels: {!! json_encode($salesLabels) !!},
            salesValues: {!! json_encode($salesValues) !!},
            statusStats: {!! json_encode($statusStats) !!}
        };
    </script>
    <script src="{{ asset('assets/js/pages/dashboardCashier.js') }}"></script>
@endpush
