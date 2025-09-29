@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">
        <h4>Tambah Produk</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="product-form" action="{{ route('admin.product.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            {{-- BAGIAN 1: DETAIL PRODUK UTAMA --}}
            <div class="row">
                <h5 class="mt-4 mb-3">Informasi Dasar Produk</h5>

                {{-- Kategori --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <select class="form-select" name="category_id" required>
                            <option disabled selected>Pilih Kategori</option>
                            @foreach ($categories as $data)
                            <option value="{{ $data->id }}" {{ old('category_id') == $data->id ? 'selected' : '' }}>
                                {{ $data->name }}
                            </option>
                            @endforeach
                        </select>
                        <label for="tb-name">Nama Kategori</label>
                    </div>
                </div>

                {{-- Nama Produk --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        <label for="tb-name">Nama Produk</label>
                        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="description" value="{{ old('description') }}" required>
                        <label for="tb-name">Deskripsi</label>
                        @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- Harga --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="price" value="{{ old('price') }}" step="0.01" required>
                        <label for="tb-name">Harga Dasar</label>
                        @error('price')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- Stok --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="stock" value="{{ old('stock') }}" required>
                        <label for="tb-name">Stok</label>
                        @error('stock')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                {{-- Gambar --}}
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                        <label for="tb-name">Gambar</label>
                        @error('image')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
            </div>

            {{-- -------------------------------------------------------------------------------- --}}

            {{-- BAGIAN 2: PENGELOLAAN VARIAN PRODUK BARU --}}
            <div class="row">
                <div class="col-md-12 mt-4">
                    <h5 class="mb-3">Tambah Opsi Varian (Opsional)</h5>

                    {{-- Container tempat baris varian akan dimasukkan --}}
                    <div id="variants-container">
                        @if(old('variants'))
                            {{-- Jika ada error, ulangi input lama --}}
                            @foreach(old('variants') as $index => $variant)
                                <div class="variant-row p-3 mb-2 border rounded bg-light">
                                    <input type="hidden" name="variants[{{ $index }}][id]" value="0">
                                    <div class="row">
                                        <div class="col-4">
                                            <label class="form-label">Tipe Varian</label>
                                            <select class="form-select" name="variants[{{ $index }}][type]" required onchange="switchNameInput(this)">
                                                <option value="size" {{ $variant['type'] == 'size' ? 'selected' : '' }}>Ukuran (Size)</option>
                                                <option value="addon" {{ $variant['type'] == 'addon' ? 'selected' : '' }}>Tambahan (Addon)</option>
                                                <option value="milk" {{ $variant['type'] == 'milk' ? 'selected' : '' }}>Susu (Milk)</option>
                                                <option value="other" {{ $variant['type'] == 'other' ? 'selected' : '' }}>Lain-lain</option>
                                            </select>
                                        </div>
                                        <div class="col-5 name-variant-container">
                                            <label class="form-label">Nama Varian</label>
                                            @if ($variant['type'] == 'size')
                                                {{-- Jika tipe size, tampilkan select untuk old data --}}
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
                                                {{-- Jika tipe lain, tampilkan input text untuk old data --}}
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
                            @endforeach
                        @endif
                    </div>

                    <button type="button" id="add-variant-btn" class="btn btn-success mt-3">
                        + Tambah Opsi Varian Baru
                    </button>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="col-12 mt-4">
                <div class="d-md-flex align-items-center">
                    <div class="ms-auto mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TEMPLATE HTML LENGKAP UNTUK BARIS VARIAN BARU --}}
<template id="variant-row-template">
    <div class="variant-row p-3 mb-2 border rounded bg-light" data-new="true">
        <input type="hidden" name="variants[TEMP_INDEX][id]" value="0">
        <div class="row">
            <div class="col-4">
                <label class="form-label">Tipe Varian</label>
                {{-- Tambahkan onchange di sini --}}
                <select class="form-select variant-type-select" name="variants[TEMP_INDEX][type]" required>
                    <option value="size">Ukuran (Size)</option>
                    <option value="addon" selected>Tambahan (Addon)</option> {{-- Default ke Addon --}}
                    <option value="milk">Susu (Milk)</option>
                    <option value="other">Lain-lain</option>
                </select>
            </div>
            <div class="col-5 name-variant-container">
                <label class="form-label">Nama Varian</label>
                {{-- Input default adalah text (karena default tipe Addon) --}}
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

        // Tentukan index awal yang benar, termasuk jika ada old('variants')
        let variantIndex = {{ old('variants') ? count(old('variants')) : 0 }};

        // 1. Fungsi untuk mengganti Input Nama Varian berdasarkan Tipe
        function switchNameInput(selectElement) {
            const row = selectElement.closest('.variant-row');
            const nameContainer = row.querySelector('.name-variant-container');

            // Ambil index baris
            const currentIndex = selectElement.name.match(/\[(\d+)\]/)[1];

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

                // Coba pertahankan nilai lama jika ada di preset, atau set default ke Medium
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
            if (e.target.name && e.target.name.includes('[type]')) {
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

            // Inisialisasi input nama varian baru berdasarkan default (Addon/Text)
            // Karena template default sudah saya set ke addon (input text),
            // kita hanya perlu memastikan kontainer memiliki class yang benar.
            const typeSelect = newRow.querySelector('.variant-type-select');
            const nameContainer = newRow.querySelector('.name-variant-container');

            // Tambahkan class agar penanganan event change lebih mudah
            nameContainer.classList.add('name-variant-container');

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

        // 6. Inisialisasi awal (Khusus untuk old data saat error validasi)
        variantsContainer.querySelectorAll('[name*="[type]"]').forEach(select => {
            // Kita tidak perlu memanggil switchNameInput di sini, karena sudah di-render oleh Blade.
            // Kita hanya memastikan event listener siap.
        });
    });
</script>
@endpush
