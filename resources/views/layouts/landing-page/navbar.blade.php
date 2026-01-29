<header class="header-fp p-0 w-100">
    <nav class="navbar navbar-expand-lg py-3 py-lg-4">
        <div class="custom-container d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="text-nowrap logo-img">
                <img src="{{ asset('assets/images/logos/ruangrasa-vertical.png') }}"
                     class="dark-logo"
                     alt="Ruang Rasa Logo"
                     style="height: 60px; width: auto;" />
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler border-0 p-0 shadow-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasRight"
                    aria-controls="offcanvasRight"
                    aria-label="Toggle navigation">
                <i class="ti ti-menu-2 fs-8 icon-highlight"></i>
            </button>

            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto mb-2 gap-xl-4 gap-lg-3 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom"
                           href="{{ route('home') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom"
                           href="#menu-kami">
                            Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom"
                           href="#tentang-kami">
                            Tentang Kami
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom"
                           href="#layanan">
                            Layanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom"
                           href="#reservasi">
                            Reservasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom"
                           href="#kontak">
                            Kontak
                        </a>
                    </li>
                </ul>

                <!-- CTA Button -->
                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('login') }}"
                       class="btn btn-primary px-4 py-2 rounded-pill">
                        <i class="ti ti-login me-2"></i>
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
    /* Navbar Styling - FIX TRANSPARENCY ISSUE */
    .header-fp {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        z-index: 9999 !important;
        background-color: #004643 !important;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .header-fp .navbar {
        background-color: transparent !important;
    }

    .header-fp.scrolled {
        background-color: rgba(0, 70, 67, 0.98) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    /* Add padding to body to prevent content from hiding under fixed navbar */
    body {
        padding-top: 90px;
    }

    @media (max-width: 991px) {
        body {
            padding-top: 80px;
        }
    }

    /* Custom Container */
    .custom-container {
        max-width: 1320px;
        margin: 0 auto;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        width: 100%;
    }

    /* Nav Link Styling */
    .nav-link-custom {
        color: var(--text-paragraph) !important;
        position: relative;
        transition: all 0.3s ease;
        padding: 0.5rem 0.75rem !important;
    }

    .nav-link-custom:hover,
    .nav-link-custom:focus {
        color: var(--highlight) !important;
    }

    /* Active State dengan Underline */
    .nav-link-custom.active,
    .nav-link-custom:hover {
        color: var(--highlight) !important;
    }

    .nav-link-custom.active::after,
    .nav-link-custom:hover::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 3px;
        background-color: var(--highlight);
        border-radius: 2px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            width: 0;
        }
        to {
            width: 30px;
        }
    }

    /* Mobile Toggle Button */
    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
    }

    .navbar-toggler .icon-highlight {
        font-size: 2rem;
    }

    /* Logo */
    .logo-img img {
        transition: transform 0.3s ease;
    }

    .logo-img:hover img {
        transform: scale(1.05);
    }

    /* Navbar on Scroll Effect */
    @media (min-width: 992px) {
        .navbar {
            transition: padding 0.3s ease;
        }

        .header-fp.scrolled .navbar {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
        }

        .header-fp.scrolled .logo-img img {
            height: 50px;
        }
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .navbar-collapse {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #004643 !important;
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            margin-top: 1px;
        }

        .navbar-nav {
            gap: 0 !important;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link-custom {
            padding: 0.75rem 1rem !important;
            border-radius: 0.5rem;
        }

        .nav-link-custom:hover {
            background-color: rgba(249, 188, 96, 0.1);
        }

        .nav-link-custom.active::after,
        .nav-link-custom:hover::after {
            display: none;
        }

        /* Mobile CTA Button */
        .navbar-collapse > div {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
    }
</style>

<script>
    // Navbar Scroll Effect
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.header-fp');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Active Link on Current Page
    document.addEventListener('DOMContentLoaded', function() {
        const currentLocation = window.location.href;
        const navLinks = document.querySelectorAll('.nav-link-custom');

        navLinks.forEach(link => {
            if (link.href === currentLocation) {
                link.classList.add('active');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId !== '#' && targetId !== '') {
                    e.preventDefault();
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - 90; // Adjust for fixed header
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    });
</script>
