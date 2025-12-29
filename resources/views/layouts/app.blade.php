<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">
<head>
    <meta charset="UTF-8">
    <title>Ruang Rasa</title>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body>

<div id="main-wrapper">
    @include('layouts.components.sidebar')

    <div class="page-wrapper">
        @include('layouts.components.navbar')

        <div class="body-wrapper">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        @include('layouts.components.footer')
    </div>
</div>

<div class="dark-transparent sidebartoggler"></div>

<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
<script src="{{ asset('assets/js/theme/theme.js') }}"></script>
<script src="{{ asset('assets/js/theme/app.min.js') }}"></script>
<script src="{{ asset('assets/js/theme/sidebarmenu.js') }}"></script>

@stack('scripts')
</body>
</html>
