@extends('layouts.admin')

@section('content')
<style> .map-wrapper { height: 350px; position: relative; /* ini kuncinya */ z-index: 1; } /* paksa leaflet nurut */ .leaflet-container { z-index: 1 !important; } </style>
    <a href="{{ route('branches.index') }}" class="btn btn-light mb-3">← Kembali</a>

    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">Tambah Cabang</h4>

            <form action="{{ route('branches.store') }}" method="POST" class="confirm-submit" data-type="save">
                @csrf

                <div class="mb-3">
                    <label>Nama Cabang</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div id="map" class="map-wrapper mb-4"></div>

                <div class="mb-3">
                    <label>Alamat</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="lat"
                            class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude') }}" >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="lng"
                            class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude') }}" >
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Jam Buka</label>
                        <input type="time" name="open_time" class="form-control @error('open_time') is-invalid @enderror"
                            value="{{ old('open_time') }}">
                        @error('open_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Jam Tutup</label>
                        <input type="time" name="close_time"
                            class="form-control @error('close_time') is-invalid @enderror" value="{{ old('close_time') }}">
                        @error('close_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const map = L.map('map').setView([-6.914744, 107.60981], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let marker;

        map.on('click', function(e) {
            const {
                lat,
                lng
            } = e.latlng;

            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }

            // reverse geocoding
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('address').value = data.display_name;
                    }
                })
                .catch(err => console.log(err));
        });
    </script>
@endpush
