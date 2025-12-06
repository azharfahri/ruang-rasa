@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3 class="mb-4">Data Kategori</h3>
        <div class="card shadow-sm">
            <div class="card-body">
                {{-- ALERT SUCCESS & ERROR --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                {{-- END ALERT --}}

                <a href="{{ route ('admin.category.create') }}" type="button" class="btn btn-primary mb-3">
                    <i class="fas fa-plus me-1"></i> Tambah Kategori
                </a>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Kategori</th>
                                <th scope="col">Tipe</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- Mengganti $category menjadi $categories --}}
                            @forelse ($categories as $item)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        {{-- Menampilkan tipe dengan badge yang rapi --}}
                                        <span class="badge
                                            @if($item->type == 'beverage') bg-info
                                            @elseif($item->type == 'food') bg-warning text-dark
                                            @elseif($item->type == 'snack') bg-success
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->slug }}</td>
                                    <td>
                                        {{-- Gunakan div untuk menampung button agar lebih rapi --}}
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.category.edit', $item->id) }}" type="button"
                                                class="btn btn-sm btn-success" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            {{-- Tombol Hapus: Pemicu Modal --}}
                                            <button type="button" class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                title="Hapus">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td align="center" colspan="5">
                                        <h6 class="mt-3">Belum ada data Kategori.</h6>
                                        <p class="text-muted">Klik "Tambah Kategori" untuk mulai.</p>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus kategori: <strong id="categoryNamePlaceholder"></strong>?
                    <p class="text-danger mt-2">Tindakan ini tidak dapat dibatalkan dan akan gagal jika ada produk yang masih menggunakan kategori ini.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script untuk mengintegrasikan data ke dalam modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                // Tombol yang memicu modal
                var button = event.relatedTarget;
                var categoryId = button.getAttribute('data-id');
                var categoryName = button.getAttribute('data-name');

                // Mendapatkan elemen form dan placeholder
                var modalBodyInput = deleteModal.querySelector('#categoryNamePlaceholder');
                var deleteForm = deleteModal.querySelector('#deleteForm');

                // Mengisi placeholder nama kategori
                modalBodyInput.textContent = categoryName;

                // Mengatur action URL form hapus
                var actionUrl = "{{ route('admin.category.destroy', ':id') }}";
                actionUrl = actionUrl.replace(':id', categoryId);
                deleteForm.setAttribute('action', actionUrl);
            });
        });
    </script>
@endpush
