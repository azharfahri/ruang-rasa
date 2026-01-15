<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/ruangrasa.png') }}" />

    <!-- Core Css -->
   <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }} "/>

    <title>Modernize Bootstrap Admin</title>
    <!-- Owl Carousel  -->
    <link rel="stylesheet" href="{{ asset('assets/libs/owl.carousel/dist/assets/owl.carousel.min.css') }}" />
</head>

<body>


    <!-- Preloader -->
    <div class="preloader">
        <img src="../assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <!-- ------------------------------------- -->
    <!-- Top Bar Start -->
    <!-- ------------------------------------- -->
    <div class="topbar-image bg-primary py-1 rounded-0 mb-0 alert alert-dismissible fade show" role="alert">
        <div
            class="d-flex justify-content-center gap-sm-3 gap-2 align-items-center text-center flex-md-nowrap flex-wrap">
            <span class="badge bg-white bg-opacity-10 fs-2 fw-bolder px-2">New</span>
            <p class="mb-0 text-white fw-bold">Frontend Pages Included!</p>
        </div>
        <button type="button" class="btn-close btn-close-white p-2 fs-2" data-bs-dismiss="alert"
            aria-label="Close"></button>
    </div>
    <!-- ------------------------------------- -->
    <!-- Top Bar End -->
    <!-- ------------------------------------- -->

    @include('layouts.landing-page.navbar')

    @include('layouts.landing-page.sidebar')

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <iframe width="100%" height="500"
                        src="https://www.youtube.com/embed/W_ADbeKyP4c?si=-63qC3_L1fI5wEsO"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="main-wrapper overflow-hidden">
        @yield('content')
    </div>


    <!-- Scroll Top -->
    <a href="javascript:void(0)"
        class="top-btn btn btn-primary d-flex align-items-center justify-content-center round-54 p-0 rounded-circle">
        <i class="ti ti-arrow-up fs-7"></i>
    </a>

    <script src="{{ asset('assets/js/vendor.min.js')}}"></script>
    <!-- Import Js Files -->
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js')}}"></script>
    <script src="{{ asset('assets/js/theme/app.init.js')}}"></script>
    <script src="{{ asset('assets/js/theme/theme.js')}}"></script>
    <script src="{{ asset('assets/js/theme/app.min.js')}}"></script>

    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="{{ asset('assets/libs/owl.carousel/dist/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('assets/js/frontend-landingpage/homepage.js') }}"></script>

@stack('scripts')
</body>

</html>
