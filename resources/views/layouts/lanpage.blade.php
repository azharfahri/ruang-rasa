<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/ruangrasa.png') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }} "/>

    <link rel="stylesheet" href="{{ asset('assets/libs/owl.carousel/dist/assets/owl.carousel.min.css') }}" />

    <title>Ruang Rasa</title>

    <style>
        /* ========================================
           COLOR PALETTE - RUANG RASA
           ======================================== */
        :root {
            /* Elements */
            --bg-primary: #004643;      /* Background */
            --text-headline: #fffffe;   /* Headline */
            --text-paragraph: #abd1c6;  /* Paragraph */
            --btn-primary: #f9bc60;     /* Button */
            --btn-text: #001e1d;        /* Button text */

            /* Illustration */
            --stroke: #001e1d;          /* Stroke */
            --main: #e8e4e6;            /* Main */
            --highlight: #f9bc60;       /* Highlight */
            --secondary: #abd1c6;       /* Secondary */
            --tertiary: #e16162;        /* Tertiary (accent) */

            /* Supporting Colors */
            --card-bg: #00332f;         /* Card background */
            --border-color: rgba(171, 209, 198, 0.1);
        }

        /* ========================================
           BACKGROUND & BASE COLORS
           ======================================== */
        body, .main-wrapper, .offcanvas-body {
            background-color: var(--bg-primary) !important;
            color: var(--text-paragraph) !important;
        }

        /* ========================================
           TYPOGRAPHY
           ======================================== */
        h1, h2, h3, h4, h5, h6, .fw-bold, .fw-bolder {
            color: var(--text-headline) !important;
        }

        p, span, li {
            color: var(--text-paragraph);
        }

        /* Utility Classes for Text Colors */
        .text-headline {
            color: var(--text-headline) !important;
        }

        .text-paragraph {
            color: var(--text-paragraph) !important;
        }

        /* ========================================
           BUTTONS
           ======================================== */
        .btn-primary, .top-btn {
            background-color: var(--btn-primary) !important;
            border-color: var(--btn-primary) !important;
            color: var(--btn-text) !important;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover, .btn-primary:focus, .top-btn:hover {
            background-color: #e0a855 !important;
            border-color: #e0a855 !important;
            color: var(--btn-text) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 188, 96, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-outline-light {
            border-color: var(--text-headline);
            color: var(--text-headline);
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover, .btn-outline-light:focus {
            background-color: var(--text-headline);
            border-color: var(--text-headline);
            color: var(--bg-primary);
            transform: translateY(-2px);
        }

        .btn-dark {
            background-color: var(--btn-text) !important;
            border-color: var(--btn-text) !important;
            color: var(--btn-primary) !important;
            font-weight: 600;
        }

        .btn-dark:hover {
            background-color: #000d0c !important;
            border-color: #000d0c !important;
            transform: translateY(-2px);
        }

        /* ========================================
           CARDS
           ======================================== */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            border-color: rgba(249, 188, 96, 0.3);
        }

        .hover-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3) !important;
        }

        .card-body {
            color: var(--text-paragraph);
        }

        /* ========================================
           NAVIGATION TABS
           ======================================== */
        .nav-tabs {
            background-color: var(--card-bg);
            padding: 0.5rem;
            border: none;
        }

        .nav-tabs .nav-link {
            color: var(--text-paragraph);
            border: none;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link i {
            color: var(--highlight);
        }

        .nav-tabs .nav-link:hover:not(.active) {
            background-color: rgba(249, 188, 96, 0.1);
            color: var(--highlight);
        }

        .nav-tabs .nav-link.active {
            background-color: var(--btn-primary) !important;
            color: var(--btn-text) !important;
            font-weight: 600;
        }

        .nav-tabs .nav-link.active i {
            color: var(--btn-text) !important;
        }

        /* ========================================
           ACCORDION
           ======================================== */
        .accordion-item {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .accordion-button {
            background-color: var(--card-bg);
            color: var(--text-headline);
            border: none;
            transition: all 0.3s ease;
        }

        .accordion-button:not(.collapsed) {
            background-color: var(--bg-primary) !important;
            color: var(--text-headline) !important;
            box-shadow: none;
        }

        .accordion-button:hover {
            background-color: rgba(249, 188, 96, 0.05);
        }

        .accordion-button:focus {
            box-shadow: none;
            border: none;
        }

        .accordion-button::after {
            filter: brightness(0) invert(1);
        }

        .accordion-body {
            background-color: var(--card-bg);
            color: var(--text-paragraph);
        }

        /* ========================================
           HERO SECTION
           ======================================== */
        .hero-gradient {
            background: linear-gradient(to bottom,
                rgba(0, 70, 67, 0.8),
                rgba(0, 30, 29, 0.9)
            );
        }

        /* ========================================
           CTA SECTION
           ======================================== */
        .cta-gradient {
            background: linear-gradient(135deg, var(--btn-primary), #e0a855);
        }

        .cta-gradient h2,
        .cta-gradient p,
        .cta-gradient .text-dark {
            color: var(--btn-text) !important;
        }

        /* ========================================
           ICONS & ACCENTS
           ======================================== */
        .icon-highlight {
            color: var(--highlight) !important;
        }

        .icon-secondary {
            color: var(--secondary) !important;
        }

        .icon-tertiary {
            color: var(--tertiary) !important;
        }

        /* ========================================
           SHADOWS & BORDERS
           ======================================== */
        .shadow-custom {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 30, 29, 0.3);
        }

        .border-custom {
            border-color: var(--border-color) !important;
        }

        /* ========================================
           MODAL
           ======================================== */
        .modal-content {
            background-color: var(--bg-primary);
            border: 1px solid rgba(171, 209, 198, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
        }

        /* ========================================
           PRELOADER
           ======================================== */
        .preloader {
            background-color: var(--bg-primary);
        }

        /* ========================================
           NAVBAR & SIDEBAR
           ======================================== */
        .navbar, .offcanvas {
            background-color: var(--bg-primary) !important;
        }

        .navbar-nav .nav-link {
            color: var(--text-paragraph);
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: var(--highlight);
        }

        .navbar-nav .nav-link.active {
            color: var(--highlight);
        }

        /* Header Sticky */
        .header-fp {
            background-color: var(--bg-primary);
            transition: all 0.3s ease;
        }

        /* Custom Container for Navbar */
        .custom-container {
            max-width: 1320px;
            margin: 0 auto;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            width: 100%;
        }

        @media (max-width: 1400px) {
            .custom-container {
                max-width: 1140px;
            }
        }

        @media (max-width: 1200px) {
            .custom-container {
                max-width: 960px;
            }
        }

        @media (max-width: 992px) {
            .custom-container {
                max-width: 720px;
            }
        }

        @media (max-width: 768px) {
            .custom-container {
                max-width: 540px;
            }
        }

        @media (max-width: 576px) {
            .custom-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* ========================================
           FORMS & INPUTS
           ======================================== */
        .form-control, .form-select {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-paragraph);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--card-bg);
            border-color: var(--highlight);
            color: var(--text-paragraph);
            box-shadow: 0 0 0 0.2rem rgba(249, 188, 96, 0.25);
        }

        .form-control::placeholder {
            color: rgba(171, 209, 198, 0.5);
        }

        /* ========================================
           BADGES & ALERTS
           ======================================== */
        .badge {
            font-weight: 600;
        }

        .badge-primary {
            background-color: var(--btn-primary);
            color: var(--btn-text);
        }

        .alert {
            border: 1px solid var(--border-color);
        }

        /* ========================================
           LINKS
           ======================================== */
        a {
            color: var(--highlight);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #e0a855;
        }

        /* ========================================
           SMOOTH ANIMATIONS
           ======================================== */
        * {
            scroll-behavior: smooth;
        }

        .btn, .card, .nav-link, .accordion-button, a {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ========================================
           TOP BUTTON
           ======================================== */
        .top-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 3.5rem;
            height: 3.5rem;
            z-index: 999;
            display: none;
        }

        .top-btn.show {
            display: flex;
            animation: fadeInUp 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========================================
           RESPONSIVE ADJUSTMENTS
           ======================================== */
        @media (max-width: 768px) {
            h1.display-3 {
                font-size: 2.5rem !important;
            }

            .display-5 {
                font-size: 2rem !important;
            }

            .display-6 {
                font-size: 1.5rem !important;
            }

            .fs-4 {
                font-size: 1.1rem !important;
            }

            .fs-5 {
                font-size: 1rem !important;
            }

            .top-btn {
                bottom: 1rem;
                right: 1rem;
                width: 3rem;
                height: 3rem;
            }
        }

        @media (max-width: 576px) {
            .btn-lg {
                padding: 0.75rem 2rem !important;
            }
        }

        /* ========================================
           UTILITIES
           ======================================== */
        .bg-primary-custom {
            background-color: var(--bg-primary) !important;
        }

        .bg-card {
            background-color: var(--card-bg) !important;
        }

        .bg-highlight {
            background-color: var(--highlight) !important;
        }

        .text-highlight {
            color: var(--highlight) !important;
        }

        /* ========================================
           LOADING & TRANSITIONS
           ======================================== */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .slide-up {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="preloader">
        <img src="../assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
    </div>

    @include('layouts.landing-page.navbar')

    @include('layouts.landing-page.sidebar')

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <a href="javascript:void(0)"
        class="top-btn btn btn-primary d-flex align-items-center justify-content-center rounded-circle">
        <i class="ti ti-arrow-up fs-7"></i>
    </a>

    @include('layouts.landing-page.footer')

    <script src="{{ asset('assets/js/vendor.min.js')}}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js')}}"></script>
    <script src="{{ asset('assets/js/theme/app.init.js')}}"></script>
    <script src="{{ asset('assets/js/theme/theme.js')}}"></script>
    <script src="{{ asset('assets/js/theme/app.min.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="{{ asset('assets/libs/owl.carousel/dist/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('assets/js/frontend-landingpage/homepage.js') }}"></script>

    <script>
        // Show/Hide Top Button
        window.addEventListener('scroll', function() {
            const topBtn = document.querySelector('.top-btn');
            if (window.pageYOffset > 300) {
                topBtn.classList.add('show');
            } else {
                topBtn.classList.remove('show');
            }
        });

        // Smooth scroll to top
        document.querySelector('.top-btn').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
