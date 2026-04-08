<footer id="kontak" class="pt-9 footer-cafe">
    <div class="container-fluid px-lg-5">
        <div class="border-bottom border-white border-opacity-10 pb-5">
            <div class="row mb-sm-12 mb-4">
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <h3 class="fs-5 fw-bold mb-4 text-highlight">Menu Favorit</h3>
                    @foreach($products as $product)
                    <ul class="d-flex flex-column gap-2 list-unstyled">
                        <li>
                            <a href="#menu-kami" class="footer-link">{{ $product->name }}</a>
                        </li>
                    </ul>
                    @endforeach
                </div>

                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <h3 class="fs-5 fw-bold mb-4 text-highlight">Layanan</h3>
                    <ul class="d-flex flex-column gap-2 list-unstyled">
                        <li>
                            <a href="#layanan" class="footer-link">Pengalaman Dine-In</a>
                        </li>
                        <li>
                            <a href="#layanan" class="footer-link">Layanan Take Away</a>
                        </li>
                        <li>
                            <a href="#kenapa-kami" class="footer-link">Kualitas Barista</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-3 col-6 mb-4 mb-md-0 text-white">
                    <h3 class="fs-5 fw-bold mb-4 text-highlight">Ruang Rasa</h3>
                    <ul class="d-flex flex-column gap-2 list-unstyled">
                        <li class="opacity-75 small">
                            <i class="ti ti-map-pin me-2 text-warning"></i>
                            Jalan Raya Cibaduyut, Cibaduyut Wetan, Bojongloa Kidul, Kota Bandung, Jawa Barat, Jawa, 40236, Indonesia
                        </li>
                        <li class="mt-2">
                            <a href="#beranda" class="footer-link">Kembali ke Atas</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-3 col-6">
                    <h3 class="fs-5 fw-bold mb-4 text-highlight">Ikuti Rasa</h3>
                    <div class="d-flex gap-3 mb-4">
                        <a href="https://facebook.com/caferuangrasacoffe" target="_blank" class="social-icon">
                            <img src="{{ asset('assets/images/frontend-pages/icon-facebook.svg') }}" alt="facebook" width="24">
                        </a>
                        <a href="https://www.instagram.com/fhrazharrr/" target="_blank" class="social-icon">
                            <img src="{{ asset('assets/images/frontend-pages/icon-instagram.svg') }}" alt="instagram" width="24">
                        </a>
                    </div>
                    <div>
                        <p class="small opacity-75 text-white">Bagikan momenmu dengan hashtag <br><strong class="text-warning">#RuangRasa</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between py-5 flex-md-nowrap flex-wrap gap-3 align-items-center">
            <div class="d-flex gap-3 align-items-center text-white">
                <img src="{{ asset('assets/images/logos/ruangrasa-vertical.png') }}" alt="Ruang Rasa" width="50" style="filter: brightness(0) invert(1);">
                <p class="small mb-0 opacity-75">&copy; 2026 Ruang Rasa. Menemani setiap cerita dalam rasa.</p>
            </div>
            <div>
                <p class="mb-0 text-white-50 small">Dibuat dengan hati untuk pecinta kopi.</p>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Footer Custom Styling */
    .footer-cafe {
        background-color: #004643 !important; /* Warna Hijau Gelap Navbar */
        position: relative;
        z-index: 10;
    }

    .text-highlight {
        color: #f5b041 !important; /* Warna Emas/Kuning */
    }

    .footer-link {
        color: rgba(255, 255, 255, 0.75) !important;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .footer-link:hover {
        color: #f5b041 !important;
        padding-left: 5px;
    }

    .social-icon {
        transition: transform 0.3s ease;
        display: inline-block;
    }

    .social-icon:hover {
        transform: translateY(-5px);
    }

    /* Penyesuaian garis bawah tipis */
    .border-white.border-opacity-10 {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    @media (max-width: 575px) {
        .footer-cafe h3 {
            font-size: 1.1rem !important;
        }
    }
</style>
