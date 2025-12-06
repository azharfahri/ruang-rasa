@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Produk: {{ $product->name }}</h4>
        </div>
        <div class="card-body">

            {{-- Menampilkan Error Validasi --}}
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

            {{-- FORM EDIT PRODUK --}}
            {{-- Pastikan action diarahkan ke route update yang benar --}}
            <form id="product-form" action="{{ route('admin.product.update', $product->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- BAGIAN 1: DETAIL PRODUK UTAMA --}}
                <div class="row g-3">
                    <h5 class="mt-4 mb-3 text-warning border-bottom pb-2">Informasi Dasar</h5>

                    {{-- Kategori --}}
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="" disabled>Pilih Kategori</option>
                                @foreach ($categories as $data)
                                <option value="{{ $data->id }}" {{ old('category_id', $product->category_id) == $data->id ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" placeholder="Nama Produk" required>
                            <label for="name"><i class="fas fa-bookmark me-1"></i> Nama Produk</label>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Deskripsi Singkat" style="height: 80px" required>{{ old('description', $product->description) }}</textarea>
                            <label for="description"><i class="fas fa-info-circle me-1"></i> Deskripsi Singkat</label>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" placeholder="Harga Dasar (Rp)" required>
                            <label for="price"><i class="fas fa-money-bill-wave me-1"></i> Harga Dasar (Rp)</label>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Stok --}}
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" placeholder="Stok Awal" required>
                            <label for="stock"><i class="fas fa-cubes me-1"></i> Stok Awal</label>
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Gambar --}}
                    <div class="col-md-4">
                        <label for="image" class="form-label mb-2"><i class="fas fa-image me-1"></i> Gambar Produk (Kosongkan jika tidak diubah)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($product->image)
                            <small class="text-muted">Gambar saat ini: <a href="{{ asset('storage/' . $product->image) }}" target="_blank">Lihat</a></small>
                        @endif
                    </div>
                </div>

                {{-- -------------------------------------------------------------------------------- --}}

                {{-- BAGIAN 2: PENGELOLAAN VARIAN PRODUK --}}
                <div class="row g-3">
                    <div class="col-md-12">
                        <h5 class="mt-5 mb-3 text-warning border-bottom pb-2">Opsi Varian Produk (Tambahan Harga)</h5>

                        {{-- PHP untuk menentukan varian mana yang akan ditampilkan (old input atau data produk) --}}
                        @php
                            $displayVariants = [];

                            // 1. Jika ada old('variants'), gunakan itu untuk mempertahankan input pengguna
                            if (old('variants')) {
                                // Gunakan 'array_values' untuk memastikan indeks berurutan 0, 1, 2...
                                // Ini PENTING untuk logika JS dan penamaan input.
                                $displayVariants = array_values(old('variants'));
                            } else {
                                // 2. JIKA tidak ada old input, gunakan varian dari produk saat ini
                                if ($product->variants) {
                                    $displayVariants = $product->variants->map(function($v) {
                                        return [
                                            'id' => $v->id, // ID LAMA HARUS ADA
                                            'type' => $v->type,
                                            'name' => $v->name,
                                            'price_impact' => $v->price_impact,
                                        ];
                                    })->toArray();
                                }
                            }

                            // Hitung index awal untuk varian baru yang ditambahkan oleh user (harus lebih besar dari index terakhir)
                            $initialVariantIndex = count($displayVariants);
                        @endphp

                        {{-- Container tempat baris varian akan dimasukkan --}}
                        <div id="variants-container">

                            {{-- Memuat varian yang ada atau old input --}}
                            @foreach($displayVariants as $index => $variant)
                                <div class="variant-row p-3 mb-3 border rounded shadow-sm bg-light" data-index="{{ $index }}">
                                    {{-- HIDDEN FIELD UNTUK ID VARIAN (KUNCI SOLUSI) --}}
                                    {{-- Jika varian lama, ID > 0. Jika varian baru (dari old input), ID = 0 --}}
                                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? 0 }}">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Tipe Varian</label>
                                            <select class="form-select variant-type-select" name="variants[{{ $index }}][type]" required onchange="switchNameInput(this)">
                                                <option value="size" {{ ($variant['type'] ?? '') == 'size' ? 'selected' : '' }}>Ukuran (Size)</option>
                                                <option value="addon" {{ ($variant['type'] ?? '') == 'addon' ? 'selected' : '' }}>Tambahan (Addon)</option>
                                                <option value="milk" {{ ($variant['type'] ?? '') == 'milk' ? 'selected' : '' }}>Susu (Milk)</option>
                                                <option value="other" {{ ($variant['type'] ?? '') == 'other' ? 'selected' : '' }}>Lain-lain</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 name-variant-container">
                                            <label class="form-label fw-bold">Nama Varian</label>
                                            {{-- Logika Blade untuk menentukan input awal: SELECT atau TEXT --}}
                                            @if (($variant['type'] ?? '') == 'size')
                                                {{-- Jika tipe size, tampilkan select --}}
                                                <select class="form-select" name="variants[{{ $index }}][name]" required>
                                                    @foreach(['Small', 'Medium', 'Large', 'Extra Large'] as $size)
                                                        <option value="{{ $size }}" {{ ($variant['name'] ?? '') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                                    @endforeach
                                                    {{-- Jika nama varian kustom, tambahkan opsi ini --}}
                                                    @if (!in_array(($variant['name'] ?? ''), ['Small', 'Medium', 'Large', 'Extra Large']) && !empty($variant['name']))
                                                        <option value="{{ $variant['name'] }}" selected>{{ $variant['name'] }}</option>
                                                    @endif
                                                </select>
                                            @else
                                                {{-- Jika tipe lain, tampilkan input text --}}
                                                <input type="text" class="form-control" name="variants[{{ $index }}][name]" value="{{ $variant['name'] ?? '' }}" required>
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Dampak Harga (Rp)</label>
                                            <input type="number" class="form-control" name="variants[{{ $index }}][price_impact]" step="0.01" value="{{ $variant['price_impact'] ?? 0.00 }}" required>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger remove-variant-btn w-100" title="Hapus Varian"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-variant-btn" class="btn btn-outline-success mt-3 w-100">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Opsi Varian Baru
                        </button>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-warning btn-lg shadow-sm">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TEMPLATE HTML LENGKAP UNTUK BARIS VARIAN BARU (Digunakan oleh JS) --}}
<template id="variant-row-template">
    <div class="variant-row p-3 mb-3 border rounded shadow-sm bg-light" data-new="true" data-index="TEMP_INDEX">
        {{-- Varian baru selalu memiliki ID 0 --}}
        <input type="hidden" name="variants[TEMP_INDEX][id]" value="0">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tipe Varian</label>
                <select class="form-select variant-type-select" name="variants[TEMP_INDEX][type]" required onchange="switchNameInput(this)">
                    <option value="size">Ukuran (Size)</option>
                    <option value="addon" selected>Tambahan (Addon)</option>
                    <option value="milk">Susu (Milk)</option>
                    <option value="other">Lain-lain</option>
                </select>
            </div>
            <div class="col-md-4 name-variant-container">
                <label class="form-label fw-bold">Nama Varian</label>
                {{-- Input default adalah text (ditangani oleh JS setelah ditambahkan) --}}
                <input type="text" class="form-control" name="variants[TEMP_INDEX][name]" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Dampak Harga (Rp)</label>
                <input type="number" class="form-control" name="variants[TEMP_INDEX][price_impact]" step="0.01" value="0.00" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger remove-variant-btn w-100" title="Hapus Varian"><i class="fas fa-times"></i></button>
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
    // Preset nilai untuk varian 'size'
    const SIZE_PRESETS = ['Small', 'Medium', 'Large', 'Extra Large'];

    // Fungsi untuk mengganti input nama varian (text atau select) berdasarkan tipe varian
    function switchNameInput(selectElement) {
        const row = selectElement.closest('.variant-row');
        const nameContainer = row.querySelector('.name-variant-container');

        // Ambil index baris (dari data-index)
        const currentIndex = row.getAttribute('data-index');

        // Simpan nilai input saat ini (jika ada) sebelum diganti
        const currentNameElement = nameContainer.querySelector('[name*="[name]"]');
        // Ambil nilai lama. Jika elemen select, ambil value yang dipilih. Jika input text, ambil value.
        const currentValue = currentNameElement ? currentNameElement.value : '';

        // Kosongkan container dan buat label
        nameContainer.innerHTML = '';
        const label = document.createElement('label');
        label.className = 'form-label fw-bold';
        label.textContent = 'Nama Varian';
        nameContainer.appendChild(label);

        // Pilih template yang akan digunakan
        if (selectElement.value === 'size') {
            // Tampilkan SELECT (Dropdown)
            const template = document.getElementById('name-select-template').content.cloneNode(true);
            const selectElementNew = template.querySelector('select');

            // Ganti nama attribute
            selectElementNew.name = `variants[${currentIndex}][name]`;

            // Atur nilai yang dipilih berdasarkan nilai lama
            let foundExistingOption = false;
            selectElementNew.querySelectorAll('option').forEach(option => {
                if (option.value === currentValue) {
                    option.selected = true;
                    foundExistingOption = true;
                } else {
                    option.selected = false;
                }
            });

            // Jika nilai lama BUKAN dari preset (misal dari input text kustom), tambahkan sebagai opsi baru
            if (currentValue && !SIZE_PRESETS.includes(currentValue) && !foundExistingOption) {
                 // Hapus selected default 'Medium'
                selectElementNew.querySelector('option[value="Medium"]').selected = false;

                const customOption = document.createElement('option');
                customOption.value = currentValue;
                customOption.textContent = currentValue;
                customOption.selected = true;
                selectElementNew.appendChild(customOption);
            }

            nameContainer.appendChild(selectElementNew);
        } else {
            // Tampilkan INPUT (Text)
            const template = document.getElementById('name-input-template').content.cloneNode(true);
            const inputElementNew = template.querySelector('input');

            // Ganti nama attribute
            inputElementNew.name = `variants[${currentIndex}][name]`;
            inputElementNew.value = currentValue; // Pertahankan nilai lama

            nameContainer.appendChild(inputElementNew);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const variantsContainer = document.getElementById('variants-container');
        const addVariantBtn = document.getElementById('add-variant-btn');
        const variantTemplate = document.getElementById('variant-row-template').content;

        // Inisialisasi index varian dari PHP:
        // Ini memastikan varian baru (yang ditambahkan JS) akan memiliki index yang berbeda dari varian lama/old input.
        let variantIndex = {{ $initialVariantIndex }};

        // 1. Fungsi untuk menambahkan baris varian baru
        function addVariantRow() {
            const newRowTemplate = variantTemplate.cloneNode(true);
            const newRow = newRowTemplate.firstElementChild;

            // Ganti placeholder TEMP_INDEX dengan index unik
            const elements = newRow.querySelectorAll('[name*="TEMP_INDEX"], [value*="TEMP_INDEX"]');
            elements.forEach(element => {
                // Ganti name attribute
                if(element.name) {
                    element.name = element.name.replace('TEMP_INDEX', variantIndex);
                }
                // Ganti value (hanya untuk hidden input ID)
                if(element.value === 'TEMP_INDEX') {
                    // ID varian baru selalu 0
                    element.value = variantIndex;
                }
            });
            newRow.setAttribute('data-index', variantIndex);

            variantsContainer.appendChild(newRow);

            // Inisialisasi input nama varian baru berdasarkan default (Addon/Text)
            const typeSelect = newRow.querySelector('.variant-type-select');
            // Panggil switchNameInput agar input text default tersetting dengan name yang benar
            switchNameInput(typeSelect);

            variantIndex++; // Tingkatkan index untuk baris berikutnya
        }

        // 2. Event Listener untuk tombol tambah
        addVariantBtn.addEventListener('click', addVariantRow);

        // 3. Event Delegation untuk tombol hapus
        variantsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-variant-btn')) {
                const row = e.target.closest('.variant-row');
                row.remove();
            }
        });

        // 4. Inisialisasi untuk varian lama/old input saat DOMContentLoaded
        // Jalankan switchNameInput untuk setiap varian yang dimuat Blade
        variantsContainer.querySelectorAll('.variant-row').forEach(row => {
            const selectElement = row.querySelector('.variant-type-select');
            // Kita panggil fungsi ini HANYA JIKA tipe variannya BUKAN 'size' saat dimuat.
            // Jika tipe varian adalah 'size', Blade sudah merendernya sebagai <select> di awal.
            if (selectElement.value !== 'size') {
                 // Memanggil switchNameInput memastikan bahwa jika ada old input setelah error validasi,
                 // nama varian akan dipertahankan pada input text yang benar.
                switchNameInput(selectElement);
            }
        });
    });
</script>
@endpush
