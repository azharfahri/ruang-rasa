@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3 class="mb-4">Data Produk</h3>
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

                <a href="{{ route ('admin.product.create') }}" type="button" class="btn btn-primary mb-3">
                    <i class="fas fa-plus me-1"></i> Tambah Produk
                </a>

                <div class="table-responsive">
                    {{-- Tambahkan kelas styling ke tabel --}}
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Gambar</th>
                                <th scope="col">Nama Produk</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Stok</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($product as $item)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>
                                        @if ($item->image)
                                            {{-- Gambar diset ukuran kecil dan kotak untuk kerapian --}}
                                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        @else
                                            <i class="fas fa-box text-muted fa-2x" title="Tanpa Gambar"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->category->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $item->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $item->stock }}
                                        </span>
                                    </td>
                                    <td>
                                        {{-- Aksi Edit dan Hapus (Menggunakan Flexbox dan Ikon) --}}
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.product.edit', $item->id) }}" type="button"
                                                class="btn btn-sm btn-success" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Tombol Hapus: Pemicu Modal --}}
                                            <button type="button" class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- colspan diatur menjadi 7 (sesuai jumlah kolom) --}}
                                    <td align="center" colspan="7">
                                        <h6 class="mt-3">Belum ada data Produk.</h6>
                                        <p class="text-muted">Klik "Tambah Produk" untuk mulai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS (Wajib menggunakan Modal, bukan alert confirm()) --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus produk: <strong id="productNamePlaceholder"></strong>?
                </div>
                <div class="modal-footer">
                    {{-- Form ini akan diisi action URL-nya melalui JavaScript --}}
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Logika untuk mengisi data ke Modal Hapus
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Tombol yang memicu modal
                var productId = button.getAttribute('data-id');
                var productName = button.getAttribute('data-name');

                var modalBodyInput = deleteModal.querySelector('#productNamePlaceholder');
                var deleteForm = deleteModal.querySelector('#deleteForm');

                // Menampilkan nama produk di body modal
                modalBodyInput.textContent = productName;

                // Mengganti action URL form DELETE
                // Gunakan placeholder :id dan replace dengan productId
                var actionUrl = "{{ route('admin.product.destroy', ':id') }}";
                actionUrl = actionUrl.replace(':id', productId);
                deleteForm.setAttribute('action', actionUrl);
            });
        });
    </script>
@endsection
