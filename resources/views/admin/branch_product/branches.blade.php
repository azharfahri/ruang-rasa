@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success mb-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">Penyimpanan Per Cabang</h4>
                    <small class="text-muted">Total data: {{ count($branches) }}</small>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama cabang...">
                </div>
                <div class="col-md-3">
                    <select id="limitSelect" class="form-select">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="branchTable">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Nama Cabang</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branches as $branch)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="branch-name">{{ $branch->name }}</td>
                                <td>
                                    <a href="{{ route('branch-products.show', $branch->id) }}"
                                        class="btn btn-primary btn-sm">
                                        Kelola Produk
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted" id="tableInfo"></small>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" id="prevBtn">‹</button>
                    <span id="pageInfo" class="mx-2"></span>
                    <button class="btn btn-sm btn-outline-secondary" id="nextBtn">›</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/branch.js') }}"></script>
@endpush
