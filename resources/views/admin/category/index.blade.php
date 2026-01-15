@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Kategori</h4>
                <small class="text-muted">Total data: {{ count($categories) }}</small>
            </div>
            <a href="{{ route('category.create') }}" class="btn btn-primary">
                + Tambah Kategori
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari kategori...">
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
            <table class="table table-hover align-middle" id="categoryTable">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Slug</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="category-name">{{ $category->name }}</td>
                        <td>{{ ucfirst($category->type) }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>
                            <a href="{{ route('category.edit', $category) }}"
                               class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('category.destroy', $category) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Hapus kategori?')"
                                        class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </form>
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
<script src="{{ asset('assets/js/pages/category.js') }}"></script>
@endpush
