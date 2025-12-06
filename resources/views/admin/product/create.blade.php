@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-box-open me-2"></i> Tambah Produk Baru</h4>
        </div>

        <div class="card-body">
            {{-- Error Validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Ada Kesalahan Input!</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="product-form" action="{{ route('admin.product.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                {{-- BAGIAN 1: DETAIL PRODUK UTAMA --}}
                <div class="row g-3">
                    <h5 class="mt-4 mb-3 text-primary border-bottom pb-2">Informasi Dasar</h5>

                    {{-- Kategori --}}
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Pilih Kategori</option>
                                @foreach ($categories as $data)
                                    <option value="{{ $data->id }}" {{ old('category_id') == $data->id ? 'selected' : '' }}>
                                        {{ $data->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="category_id"><i class="fas fa-tags me-1"></i> Kategori Produk</label>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Nama Produk --}}
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Nama Produk" required>
                            <label for="name"><i class="fas fa-bookmark me-1"></i> Nama Produk</label>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Deskripsi Singkat" style="height: 80px" required>{{ old('description') }}</textarea>
                            <label for="description"><i class="fas fa-info-circle me-1"></i> Deskripsi Singkat</label>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" step="0.01" placeholder="Harga Dasar (Rp)" required>
                            <label for="price"><i class="fas fa-money-bill-wave me-1"></i> Harga Dasar (Rp)</label>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Stok --}}
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}" placeholder="Stok Awal" required>
                            <label for="stock"><i class="fas fa-cubes me-1"></i> Stok Awal</label>
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Gambar --}}
                    <div class="col-md-4">
                        <label for="image" class="form-label mb-2"><i class="fas fa-image me-1"></i> Gambar Produk</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- -------------------------------------------------------------------------------- --}}
                {{-- BAGIAN 2: PENGELOLAAN VARIAN PRODUK (NESTED: TYPE -> OPTIONS) --}}
                <div class="row g-3">
                    <div class="col-md-12">
                        <h5 class="mt-5 mb-3 text-primary border-bottom pb-2">Varian Produk (Tipe & Opsi)</h5>

                        <div id="variant-types-container">
                            {{-- Render old variant_types (jika ada) --}}
                            @php
                                $oldVariantTypes = old('variant_types', []);
                            @endphp

                            @foreach ($oldVariantTypes as $vtIndex => $vt)
                                <div class="variant-type card mb-3 p-3" data-index="{{ $vtIndex }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Jenis Varian</h6>
                                        <button type="button" class="btn btn-sm btn-danger remove-variant-type">Hapus Jenis</button>
                                    </div>

                                    <div class="row gy-2">
                                        <div class="col-md-5">
                                            <label class="form-label">Nama Jenis Varian</label>
                                            <input type="hidden" name="variant_types[{{ $vtIndex }}][id]" value="{{ $vt['id'] ?? '' }}">
                                            <input type="text" class="form-control" name="variant_types[{{ $vtIndex }}][name]" value="{{ $vt['name'] ?? '' }}" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Input Type</label>
                                            <select class="form-select" name="variant_types[{{ $vtIndex }}][input_type]" required>
                                                <option value="radio" {{ ( ($vt['input_type'] ?? '') === 'radio') ? 'selected' : '' }}>radio (pilih 1)</option>
                                                <option value="checkbox" {{ ( ($vt['input_type'] ?? '') === 'checkbox') ? 'selected' : '' }}>checkbox (banyak)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-success btn-sm add-option-btn w-100">Tambah Opsi</button>
                                        </div>
                                    </div>

                                    {{-- Opsi untuk jenis varian ini --}}
                                    <div class="options-list mt-3">
                                        @php $oldOptions = $vt['options'] ?? []; @endphp
                                        @foreach ($oldOptions as $optIndex => $opt)
                                            <div class="option-item row g-2 align-items-end mb-2" data-opt-index="{{ $optIndex }}">
                                                <div class="col-md-5">
                                                    <input type="hidden" name="variant_types[{{ $vtIndex }}][options][{{ $optIndex }}][id]" value="{{ $opt['id'] ?? '' }}">
                                                    <label class="form-label">Nama Opsi</label>
                                                    <input type="text" class="form-control" name="variant_types[{{ $vtIndex }}][options][{{ $optIndex }}][option_name]" value="{{ $opt['option_name'] ?? '' }}" required>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label">Dampak Harga (Rp)</label>
                                                    <input type="number" class="form-control" name="variant_types[{{ $vtIndex }}][options][{{ $optIndex }}][price_impact]" step="0.01" value="{{ $opt['price_impact'] ?? 0 }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-outline-danger remove-option-btn w-100">Hapus</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- tombol tambah jenis varian --}}
                        <button type="button" id="add-variant-type-btn" class="btn btn-outline-success mt-3 w-100">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Jenis Varian Baru
                        </button>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-save me-2"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TEMPLATES (untuk JS cloning) --}}
<template id="variant-type-template">
    <div class="variant-type card mb-3 p-3" data-index="__INDEX__">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Jenis Varian</h6>
            <button type="button" class="btn btn-sm btn-danger remove-variant-type">Hapus Jenis</button>
        </div>

        <div class="row gy-2">
            <div class="col-md-5">
                <label class="form-label">Nama Jenis Varian</label>
                <input type="hidden" name="variant_types[__INDEX__][id]" value="">
                <input type="text" class="form-control" name="variant_types[__INDEX__][name]" value="" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Input Type</label>
                <select class="form-select" name="variant_types[__INDEX__][input_type]" required>
                    <option value="radio">radio (pilih 1)</option>
                    <option value="checkbox">checkbox (banyak)</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-outline-success btn-sm add-option-btn w-100">Tambah Opsi</button>
            </div>
        </div>

        <div class="options-list mt-3">
            {{-- option items akan dimasukkan di sini --}}
        </div>
    </div>
</template>

<template id="option-template">
    <div class="option-item row g-2 align-items-end mb-2" data-opt-index="__OPT_INDEX__">
        <div class="col-md-5">
            <input type="hidden" name="variant_types[__VT_INDEX__][options][__OPT_INDEX__][id]" value="">
            <label class="form-label">Nama Opsi</label>
            <input type="text" class="form-control" name="variant_types[__VT_INDEX__][options][__OPT_INDEX__][option_name]" value="" required>
        </div>
        <div class="col-md-5">
            <label class="form-label">Dampak Harga (Rp)</label>
            <input type="number" class="form-control" name="variant_types[__VT_INDEX__][options][__OPT_INDEX__][price_impact]" step="0.01" value="0" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger remove-option-btn w-100">Hapus</button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    (function () {
        const vtContainer = document.getElementById('variant-types-container');
        const addVtBtn = document.getElementById('add-variant-type-btn');

        // Mulai index dari jumlah variant_types lama (jika ada)
        let vtIndex = {{ count(old('variant_types', [])) }};

        // Helper: buat element variant type baru (mengganti placeholder)
        function makeVariantTypeElement(index) {
            const tpl = document.getElementById('variant-type-template').content.cloneNode(true);
            const el = tpl.querySelector('.variant-type');

            // ganti placeholders
            el.setAttribute('data-index', index);
            el.innerHTML = el.innerHTML.replace(/__INDEX__/g, index);

            return el;
        }

        // Helper: buat option untuk variant type tertentu
        function makeOptionElement(vtIdx, optIdx) {
            const tpl = document.getElementById('option-template').content.cloneNode(true);
            let html = tpl.firstElementChild.outerHTML;
            html = html.replace(/__VT_INDEX__/g, vtIdx).replace(/__OPT_INDEX__/g, optIdx);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            return wrapper.firstElementChild;
        }

        // Tambah variant type
        addVtBtn.addEventListener('click', function () {
            const newTypeEl = makeVariantTypeElement(vtIndex);
            vtContainer.appendChild(newTypeEl);
            vtIndex++;
        });

        // Event delegation untuk tombol remove type, add option, remove option
        vtContainer.addEventListener('click', function (e) {
            // Hapus variant type
            if (e.target.closest('.remove-variant-type')) {
                const vtEl = e.target.closest('.variant-type');
                vtEl.remove();
                return;
            }

            // Tambah option di dalam variant type
            if (e.target.closest('.add-option-btn')) {
                const vtEl = e.target.closest('.variant-type');
                const vtIdx = vtEl.getAttribute('data-index');

                // hitung opsi sekarang di vt ini
                const currentOpts = vtEl.querySelectorAll('.option-item').length;
                const newOpt = makeOptionElement(vtIdx, currentOpts);
                vtEl.querySelector('.options-list').appendChild(newOpt);
                return;
            }

            // Hapus option
            if (e.target.closest('.remove-option-btn')) {
                const optEl = e.target.closest('.option-item');
                if (optEl) optEl.remove();
                return;
            }
        });

        // Inisialisasi: jika ada variant_types lama (server-side old rendering), tambahkan tombol event listeners untuk add-option
        // (Note: old ones already rendered by Blade with correct names)
        // Untuk semua variant-type yang di-render dari old(), pastikan tombol "Tambah Opsi" menambahkan option dengan index yang benar
        document.querySelectorAll('#variant-types-container .variant-type').forEach(vtEl => {
            // vtEl sudah memiliki data-index dan beberapa option mungkin sudah ada
            // nothing to do here because add-option handler menggunakan vtEl.getAttribute('data-index')
        });

        // Optional: sebelum submit, tidak perlu menyesuaikan nama karena saat membuat element kita sudah menyisipkan nama yang benar.
        // Namun jika kamu ingin memastikan indeks rapi (0..n-1) saat submit, bisa lakukan reindexing di sini.
        document.getElementById('product-form').addEventListener('submit', function (ev) {
            // Reindex variant_types dan options agar server menerima indeks berurutan (0..n-1)
            const allVt = Array.from(document.querySelectorAll('#variant-types-container .variant-type'));
            allVt.forEach((vtEl, i) => {
                vtEl.setAttribute('data-index', i);

                // reindex hidden id and name & input_type name attributes
                const idInput = vtEl.querySelector('input[type="hidden"][name*="[id]"]');
                if (idInput) idInput.name = `variant_types[${i}][id]`;

                const nameInput = vtEl.querySelector('input[name*="[name]"]');
                if (nameInput) nameInput.name = `variant_types[${i}][name]`;

                const inputType = vtEl.querySelector('select[name*="[input_type]"]');
                if (inputType) inputType.name = `variant_types[${i}][input_type]`;

                // options
                const opts = Array.from(vtEl.querySelectorAll('.option-item'));
                opts.forEach((optEl, j) => {
                    const optHidden = optEl.querySelector('input[type="hidden"][name*="[id]"]');
                    if (optHidden) optHidden.name = `variant_types[${i}][options][${j}][id]`;

                    const optName = optEl.querySelector('input[name*="[option_name]"]');
                    if (optName) optName.name = `variant_types[${i}][options][${j}][option_name]`;

                    const optPrice = optEl.querySelector('input[name*="[price_impact]"]');
                    if (optPrice) optPrice.name = `variant_types[${i}][options][${j}][price_impact]`;
                });
            });

            // After reindexing, form will submit normally
        });
    })();
</script>
@endpush
