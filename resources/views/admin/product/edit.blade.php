@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">
        {{-- JUDUL DIPERBAIKI: Menggunakan $product->name --}}
        <h4>Edit Produk: {{ $product->name }}</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM UTAMA --}}
        <form id="product-form" action="{{ route('admin.product.update', $product->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- BAGIAN 1: DETAIL PRODUK UTAMA (Data diisi dari $product) --}}
            <div class="row">
                <h5 class="mt-4 mb-3">Informasi Dasar Produk</h5>

                {{-- Kategori --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <select class="form-select" name="category_id">
                            <option disabled>Pilih Kategori</option>
                            @foreach ($categories as $data)
                            <option value="{{ $data->id }}" {{ old('category_id', $product->category_id) == $data->id ? 'selected' : ''}}>
                                {{ $data->name ?? $data->nama }}
                            </option>
                            @endforeach
                        </select>
                        <label for="tb-name">Nama Kategori</label>
                    </div>
                </div>

                {{-- Nama Produk --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="name" value="{{ old('name', $product->name ?? '') }}" required>
                        <label for="tb-name">Nama Produk</label>
                        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="description" value="{{ old('description', $product->description ?? '') }}" required>
                        <label for="tb-name">Deskripsi</label>
                        @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- Harga --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="price" value="{{ old('price', $product->price ?? '') }}" step="0.01" required>
                        <label for="tb-name">Harga Dasar</label>
                        @error('price')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- Stok --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="stock" value="{{ old('stock', $product->stock ?? '') }}" required>
                        <label for="tb-name">Stok</label>
                        @error('stock')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- Gambar --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" name="image" accept="image/*" >
                        <label for="tb-name">Gambar</label>
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                        @if ($product->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'. $product->image) }}" class="rounded-circle" width="100">
                        </div>
                        @endif
                        @error('image')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- -------------------------------------------------------------------------------- --}}

                {{-- BAGIAN 2: PENGELOLAAN VARIAN PRODUK --}}
                <div class="col-md-12 mt-4">
                    <h5 class="mb-3">Kelola Opsi Varian</h5>
                    <div id="variants-container">
                        {{-- Loop untuk menampilkan varian yang sudah ada --}}
                        @foreach($product->variants as $index => $variant)
                            {{-- Cek apakah ada old input dari validasi gagal --}}
                            @php
                                $oldVariantData = old('variants.'.$index, []);
                                $variantType = $oldVariantData['type'] ?? $variant->type;
                                $variantName = $oldVariantData['name'] ?? $variant->name;
                                $priceImpact = $oldVariantData['price_impact'] ?? $variant->price_impact;
                            @endphp

                            <div class="variant-row p-3 mb-2 border rounded" data-id="{{ $variant->id }}">
                                {{-- ID Varian: $variant->id untuk update, 0 jika baru dari old input --}}
                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                                <div class="row">
                                    <div class="col-4">
                                        <label class="form-label">Tipe Varian</label>
                                        <select class="form-select variant-type-select" name="variants[{{ $index }}][type]" required>
                                            <option value="size" {{ $variantType == 'size' ? 'selected' : '' }}>Ukuran (Size)</option>
                                            <option value="addon" {{ $variantType == 'addon' ? 'selected' : '' }}>Tambahan (Addon)</option>
                                            <option value="milk" {{ $variantType == 'milk' ? 'selected' : '' }}>Susu (Milk)</option>
                                            <option value="other" {{ $variantType == 'other' ? 'selected' : '' }}>Lain-lain</option>
                                        </select>
                                    </div>
                                    <div class="col-5 name-variant-container">
                                        <label class="form-label">Nama Varian</label>

                                        {{-- LOGIKA: Jika tipe == size, tampilkan SELECT. Jika tidak, tampilkan INPUT biasa. --}}
                                        @if ($variantType == 'size')
                                            <select class="form-select" name="variants[{{ $index }}][name]" required>
                                                <option value="Small" {{ $variantName == 'Small' ? 'selected' : '' }}>Small</option>
                                                <option value="Medium" {{ $variantName == 'Medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="Large" {{ $variantName == 'Large' ? 'selected' : '' }}>Large</option>
                                                <option value="Extra Large" {{ $variantName == 'Extra Large' ? 'selected' : '' }}>Extra Large</option>
                                                {{-- Jika nilai varian saat ini tidak ada di daftar, tambahkan sebagai option pertama --}}
                                                @if (!in_array($variantName, ['Small', 'Medium', 'Large', 'Extra Large']))
                                                    <option value="{{ $variantName }}" selected>{{ $variantName }}</option>
                                                @endif
                                            </select>
                                        @else
                                            <input type="text" class="form-control" name="variants[{{ $index }}][name]" value="{{ $variantName }}" required>
                                        @endif
                                    </div>
                                    <div class="col-2">
                                        <label class="form-label">Dampak Harga</label>
                                        <input type="number" class="form-control" name="variants[{{ $index }}][price_impact]" step="0.01" value="{{ $priceImpact }}" required>
                                    </div>
                                    <div class="col-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-variant-btn w-100">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Loop untuk menampilkan varian BARU yang gagal validasi (old input) --}}
                        @if(old('variants'))
                            @php $existingCount = $product->variants->count(); @endphp
                            @foreach(old('variants') as $index => $variant)
                                @if ($index >= $existingCount && $variant['id'] == 0)
                                    <div class="variant-row p-3 mb-2 border rounded bg-light" data-new="true">
                                        <input type="hidden" name="variants[{{ $index }}][id]" value="0">
                                        <div class="row">
                                            <div class="col-4">
                                                <label class="form-label">Tipe Varian</label>
                                                <select class="form-select variant-type-select" name="variants[{{ $index }}][type]" required>
                                                    <option value="size" {{ $variant['type'] == 'size' ? 'selected' : '' }}>Ukuran (Size)</option>
                                                    <option value="addon" {{ $variant['type'] == 'addon' ? 'selected' : '' }}>Tambahan (Addon)</option>
                                                    <option value="milk" {{ $variant['type'] == 'milk' ? 'selected' : '' }}>Susu (Milk)</option>
                                                    <option value="other" {{ $variant['type'] == 'other' ? 'selected' : '' }}>Lain-lain</option>
                                                </select>
                                            </div>
                                            <div class="col-5 name-variant-container">
                                                <label class="form-label">Nama Varian</label>
                                                @if ($variant['type'] == 'size')
                                                    <select class="form-select" name="variants[{{ $index }}][name]" required>
                                                        <option value="Small" {{ $variant['name'] == 'Small' ? 'selected' : '' }}>Small</option>
                                                        <option value="Medium" {{ $variant['name'] == 'Medium' ? 'selected' : '' }}>Medium</option>
                                                        <option value="Large" {{ $variant['name'] == 'Large' ? 'selected' : '' }}>Large</option>
                                                        <option value="Extra Large" {{ $variant['name'] == 'Extra Large' ? 'selected' : '' }}>Extra Large</option>
                                                        @if (!in_array($variant['name'], ['Small', 'Medium', 'Large', 'Extra Large']))
                                                            <option value="{{ $variant['name'] }}" selected>{{ $variant['name'] }}</option>
                                                        @endif
                                                    </select>
                                                @else
                                                    <input type="text" class="form-control" name="variants[{{ $index }}][name]" value="{{ $variant['name'] }}" required>
                                                @endif
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Dampak Harga</label>
                                                <input type="number" class="form-control" name="variants[{{ $index }}][price_impact]" step="0.01" value="{{ $variant['price_impact'] }}" required>
                                            </div>
                                            <div class="col-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger remove-variant-btn w-100">Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <button type="button" id="add-variant-btn" class="btn btn-success mt-3">
                        + Tambah Opsi Varian Baru
                    </button>
                </div>

                {{-- Tombol Submit --}}
                <div class="col-12 mt-4">
                    <div class="d-md-flex align-items-center">
                        <div class="ms-auto mt-3 mt-md-0">
                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TEMPLATE HTML UNTUK VARIAN BARU (Disembunyikan) --}}
<template id="variant-row-template">
    <div class="variant-row p-3 mb-2 border rounded bg-light" data-new="true">
        {{-- ID Varian diset ke 0, menandakan ini varian baru --}}
        <input type="hidden" name="variants[TEMP_INDEX][id]" value="0">

        <div class="row">
            <div class="col-4">
                <label class="form-label">Tipe Varian</label>
                <select class="form-select variant-type-select" name="variants[TEMP_INDEX][type]" required>
                    <option value="size">Ukuran (Size)</option>
                    <option value="addon" selected>Tambahan (Addon)</option>
                    <option value="milk">Susu (Milk)</option>
                    <option value="other">Lain-lain</option>
                </select>
            </div>
            <div class="col-5 name-variant-container">
                <label class="form-label">Nama Varian</label>
                <input type="text" class="form-control" name="variants[TEMP_INDEX][name]" required>
            </div>
            <div class="col-2">
                <label class="form-label">Dampak Harga</label>
                <input type="number" class="form-control" name="variants[TEMP_INDEX][price_impact]" step="0.01" value="0.00" required>
            </div>
            <div class="col-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-variant-btn w-100">Hapus</button>
            </div>
        </div>
    </div>
</template>

{{-- TEMPLATE UNTUK INPUT NAMA VARIAN (TEXT INPUT) --}}
<template id="name-input-template">
    <input type="text" class="form-control" name="variants[TEMP_INDEX][name]" value="" required>
</template>

{{-- TEMPLATE UNTUK SELECT NAMA VARIAN (DROPDOWN KHUSUS SIZE) --}}
<template id="name-select-template">
    <select class="form-select" name="variants[TEMP_INDEX][name]" required>
        <option value="Small">Small</option>
        <option value="Medium" selected>Medium</option>
        <option value="Large">Large</option>
        <option value="Extra Large">Extra Large</option>
    </select>
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const variantsContainer = document.getElementById('variants-container');
        const addVariantBtn = document.getElementById('add-variant-btn');
        const variantTemplate = document.getElementById('variant-row-template').content;
        const nameInputTemplate = document.getElementById('name-input-template').content;
        const nameSelectTemplate = document.getElementById('name-select-template').content;

        // Mulai indeks dari jumlah total baris yang ada saat ini (existing + old new)
        let variantIndex = variantsContainer.children.length;

        // 1. Fungsi untuk mengganti Input Nama Varian berdasarkan Tipe
        function switchNameInput(selectElement) {
            const row = selectElement.closest('.variant-row');
            const nameContainer = row.querySelector('.name-variant-container');

            // Ambil index baris
            const currentIndexMatch = selectElement.name.match(/\[(\d+)\]/);
            const currentIndex = currentIndexMatch ? currentIndexMatch[1] : 'TEMP_INDEX';

            // Simpan nilai input saat ini jika ada
            const currentNameElement = nameContainer.querySelector('[name*="[name]"]');
            const currentValue = currentNameElement ? (currentNameElement.tagName === 'INPUT' ? currentNameElement.value : currentNameElement.value) : '';

            // Kosongkan container dan buat label
            nameContainer.innerHTML = '';
            const label = document.createElement('label');
            label.className = 'form-label';
            label.textContent = 'Nama Varian';
            nameContainer.appendChild(label);

            if (selectElement.value === 'size') {
                // Tampilkan SELECT (Dropdown)
                const selectClone = nameSelectTemplate.cloneNode(true);
                const selectElementNew = selectClone.querySelector('select');
                selectElementNew.name = `variants[${currentIndex}][name]`;

                // Coba pertahankan nilai lama jika ada di preset
                if (['Small', 'Medium', 'Large', 'Extra Large'].includes(currentValue)) {
                    selectElementNew.value = currentValue;
                } else {
                    selectElementNew.value = 'Medium';
                }

                nameContainer.appendChild(selectElementNew);
            } else {
                // Tampilkan INPUT (Text)
                const inputClone = nameInputTemplate.cloneNode(true);
                const inputElementNew = inputClone.querySelector('input');

                inputElementNew.name = `variants[${currentIndex}][name]`;
                inputElementNew.value = currentValue; // Pertahankan nilai lama

                nameContainer.appendChild(inputElementNew);
            }
        }

        // 2. Event Listener untuk perubahan Tipe Varian
        variantsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('variant-type-select')) {
                switchNameInput(e.target);
            }
        });

        // 3. Fungsi untuk menambahkan baris varian baru
        function addVariantRow() {
            const newRowTemplate = variantTemplate.cloneNode(true);
            const newRow = newRowTemplate.firstElementChild;

            // Ganti placeholder TEMP_INDEX dengan index unik
            const elements = newRow.querySelectorAll('[name*="TEMP_INDEX"]');
            elements.forEach(element => {
                element.name = element.name.replace('TEMP_INDEX', variantIndex);
            });

            variantsContainer.appendChild(newRow);

            // PENTING: Inisialisasi agar input nama varian menyesuaikan
            const typeSelect = newRow.querySelector('.variant-type-select');

            // Kita harus memanggil switchNameInput agar input text default dari template
            // yang baru ditambahkan ditampilkan di DOM, menggantikan placeholder.
            switchNameInput(typeSelect);

            variantIndex++; // Tingkatkan index
        }

        // 4. Event Listener untuk tombol tambah
        addVariantBtn.addEventListener('click', addVariantRow);

        // 5. Event Delegation untuk tombol hapus
        variantsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant-btn')) {
                const row = e.target.closest('.variant-row');
                row.remove();
            }
        });

        // 6. Inisialisasi awal (memastikan varian yang sudah ada menggunakan SELECT jika tipenya 'size')
        // Ini dilakukan dengan meniru perubahan event 'change' pada setiap select tipe varian yang ada di halaman.
        variantsContainer.querySelectorAll('.variant-type-select').forEach(select => {
            if (select.value === 'size') {
                // Jika tipe sudah size, jalankan switchNameInput untuk memastikan ia menggunakan dropdown
                // Namun, karena logika Blade sudah menangani ini, kita hanya perlu menimpanya jika
                // ada data old() yang bertentangan dengan data lama.
                // Untuk amannya, kita panggil saja, tetapi Blade sudah menanganinya dengan baik di bagian loop.
                // Jika Anda ingin mengandalkan JavaScript 100%, hapus logika Blade di loop.

                // Saat ini, kita biarkan logika Blade yang menangani varian yang sudah ada.
                // JavaScript hanya perlu menangani varian BARU dan event 'change' setelah dimuat.
            }
        });
    });
</script>
@endpush
