<header class="header-fp p-0 w-100">
    <nav class="navbar navbar-expand-lg py-3 py-lg-4">
        <div class="custom-container d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-nowrap logo-img">
                <img src="{{ asset('assets/images/logos/ruangrasa-vertical.png') }}" class="dark-logo"
                    alt="Ruang Rasa Logo" style="height: 60px; width: auto;" />
            </a>

            <button class="navbar-toggler border-0 p-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="ti ti-menu-2 fs-8 icon-highlight"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 gap-xl-4 gap-lg-3 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom" href="#beranda">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom" href="#kenapa-kami">
                            Keunggulan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom" href="#layanan">
                            Layanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom" href="#menu-kami">
                            Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-semibold nav-link-custom" href="#kontak">
                            Kontak
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<style>
    /* Navbar Styling */
    .header-fp {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        z-index: 9999 !important;
        background-color: #004643 !important;
        /* Warna hijau gelap sesuai palet Anda */
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .header-fp .navbar {
        background-color: transparent !important;
    }

    .header-fp.scrolled {
        background-color: rgba(0, 70, 67, 0.98) !important;
        backdrop-filter: blur(10px);
    }

    body {
        padding-top: 90px;
    }

    @media (max-width: 991px) {
        body {
            padding-top: 80px;
        }
    }

    .custom-container {
        max-width: 1320px;
        margin: 0 auto;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        width: 100%;
    }

    /* Nav Link Styling - Menggunakan warna teks putih agar kontras dengan hijau gelap */
    .nav-link-custom {
        color: #ffffff !important;
        position: relative;
        transition: all 0.3s ease;
        padding: 0.5rem 0.75rem !important;
        opacity: 0.85;
    }

    .nav-link-custom:hover,
    .nav-link-custom.active {
        color: var(--highlight, #f5b041) !important;
        /* Aksen Kuning/Emas */
        opacity: 1;
    }

    /* Active Underline */
    .nav-link-custom.active::after,
    .nav-link-custom:hover::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 25px;
        height: 3px;
        background-color: var(--highlight, #f5b041);
        border-radius: 2px;
    }

    /* Logo */
    .logo-img img {
        transition: transform 0.3s ease;
    }

    .logo-img:hover img {
        transform: scale(1.05);
    }

    /* Mobile Adjustments */
    @media (max-width: 991px) {
        .navbar-collapse {
            background-color: #004643 !important;
            padding: 1rem;
            border-radius: 0 0 1rem 1rem;
            margin-top: 10px;
        }

        .nav-link-custom.active::after,
        .nav-link-custom:hover::after {
            display: none;
        }
    }

    /* Menghapus outline biru/kotak saat link diklik */
    .nav-link-custom:focus {
        outline: none !important;
        box-shadow: none !important;
        background-color: transparent !important;
        /* Mencegah background biru di mobile */
        color: var(--highlight) !important;
        /* Pastikan teks tetap berwarna emas/kuning */
    }

    /* Menghapus efek focus pada tombol jika masih ada */
    .navbar-toggler:focus {
        box-shadow: none !important;
        outline: none !important;
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

    // Handle Active Link & Smooth Scroll
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link-custom');

        // Update active class on click
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId !== '#' && targetId !== '') {
                    e.preventDefault();
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - 90;
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
