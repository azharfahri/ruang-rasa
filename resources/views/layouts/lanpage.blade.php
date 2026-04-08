<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/ruangrasa.png') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }} " />

    <link rel="stylesheet" href="{{ asset('assets/libs/owl.carousel/dist/assets/owl.carousel.min.css') }}" />

    <title>Ruang Rasa</title>

    <style>
        /* ========================================
           COLOR PALETTE - RUANG RASA
           ======================================== */
        :root {
            /* Elements */
            --bg-primary: #004643;
            /* Background */
            --text-headline: #fffffe;
            /* Headline */
            --text-paragraph: #abd1c6;
            /* Paragraph */
            --btn-primary: #f9bc60;
            /* Button */
            --btn-text: #001e1d;
            /* Button text */

            /* Illustration */
            --stroke: #001e1d;
            /* Stroke */
            --main: #e8e4e6;
            /* Main */
            --highlight: #f9bc60;
            /* Highlight */
            --secondary: #abd1c6;
            /* Secondary */
            --tertiary: #e16162;
            /* Tertiary (accent) */

            /* Supporting Colors */
            --card-bg: #00332f;
            /* Card background */
            --border-color: rgba(171, 209, 198, 0.1);
        }

        /* ========================================
           BACKGROUND & BASE COLORS
           ======================================== */
        body,
        .main-wrapper,
        .offcanvas-body {
            background-color: var(--bg-primary) !important;
            color: var(--text-paragraph) !important;
        }

        /* ========================================
           TYPOGRAPHY
           ======================================== */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .fw-bold,
        .fw-bolder {
            color: var(--text-headline) !important;
        }

        p,
        span,
        li {
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
        .btn-primary,
        .top-btn {
            background-color: var(--btn-primary) !important;
            border-color: var(--btn-primary) !important;
            color: var(--btn-text) !important;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .top-btn:hover {
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

        .btn-outline-light:hover,
        .btn-outline-light:focus {
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
                    rgba(0, 30, 29, 0.9));
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
            position: fixed;
            inset: 0;
            background-color: var(--bg-primary);
            background:
                radial-gradient(circle at top, rgba(255, 214, 170, 0.08), transparent 60%),
                radial-gradient(circle at bottom, rgba(111, 78, 55, 0.25), transparent 70%),
                linear-gradient(180deg, #1e1b18, #15120f);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: #f5e6d3;
            font-family: 'Poppins', sans-serif;
        }


        .coffee-cup {
            position: relative;
            width: 120px;
            height: 100px;
        }

        .cup {
            width: 120px;
            height: 80px;
            border: 4px solid #f5e6d3;
            border-radius: 0 0 20px 20px;
            position: relative;
            overflow: hidden;
        }

        .cup::after {
            content: '';
            position: absolute;
            right: -30px;
            top: 20px;
            width: 30px;
            height: 40px;
            border: 4px solid #f5e6d3;
            border-left: none;
            border-radius: 0 20px 20px 0;
        }

        .coffee {
            position: absolute;
            bottom: -100%;
            width: 100%;
            height: 100%;
            background: #6f4e37;
            animation: fillCoffee 2.5s infinite;
        }

        @keyframes fillCoffee {
            0% {
                bottom: -100%;
            }

            50% {
                bottom: 0;
            }

            100% {
                bottom: 0;
            }
        }

        .smoke {
            position: absolute;
            top: -20px;
            width: 20px;
            height: 40px;
            border-radius: 50%;
            background: rgba(245, 230, 211, 0.4);
            animation: smoke 2s infinite ease-in-out;
        }

        .smoke1 {
            left: 20px;
            animation-delay: 0s;
        }

        .smoke2 {
            left: 50px;
            animation-delay: .5s;
        }

        .smoke3 {
            left: 80px;
            animation-delay: 1s;
        }

        @keyframes smoke {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateY(-40px) scale(1.3);
                opacity: 0;
            }
        }

        .loading-text {
            margin-top: 20px;
            font-size: 14px;
            letter-spacing: 1px;
            opacity: .8;
        }


        /* ========================================
           NAVBAR & SIDEBAR
           ======================================== */
        .navbar,
        .offcanvas {
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

        @media (max-width: 767px) {
            h1.display-3 {
                font-size: 2.2rem !important;
                /* Tidak terlalu raksasa di HP */
            }

            .fs-4 {
                font-size: 1.1rem !important;
                /* Paragraf lebih nyaman dibaca */
            }

            .btn-lg {
                font-size: 1rem;
                /* Ukuran tombol normal di mobile */
            }

            /* Mengurangi padding container agar konten tidak terlalu "tenggelam" */
            .container {
                padding-top: 40px !important;
                padding-bottom: 40px !important;
            }
        }

        /* Efek highlight yang lebih halus */
        .icon-highlight {
            color: #f5b041;
            /* Warna emas/kuning sesuai brand */
            display: inline-block;
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
        .form-control,
        .form-select {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-paragraph);
        }

        .form-control:focus,
        .form-select:focus {
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

        .btn,
        .card,
        .nav-link,
        .accordion-button,
        a {
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
        <div class="coffee-cup">
            <div class="cup">
                <div class="coffee"></div>
            </div>
            <div class="smoke smoke1"></div>
            <div class="smoke smoke2"></div>
            <div class="smoke smoke3"></div>
        </div>
        <p class="loading-text">Tunggu Sebentar</p>
    </div>


    @include('layouts.landing-page.navbar')

    @include('layouts.landing-page.sidebar')


    <div class="main-wrapper overflow-hidden">
        @yield('content')
    </div>

    <a href="javascript:void(0)"
        class="top-btn btn btn-primary d-flex align-items-center justify-content-center rounded-circle">
        <i class="ti ti-arrow-up fs-7"></i>
    </a>

    @include('layouts.landing-page.footer')

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
    <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="{{ asset('assets/libs/owl.carousel/dist/owl.carousel.min.js') }}"></script>
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
