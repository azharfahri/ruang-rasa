@extends('layouts.lanpage')

@section('content')
    <!-- Hero Section -->
    <section class="position-relative text-white overflow-hidden"
        style="min-height: 100vh; margin-top: -90px; padding-top: 90px;">
        <!-- Background Image + Overlay -->
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-image"
            style="background-image: url({{ asset('assets/images/backgrounds/lanpag.jpg') }});
                    background-size: cover;
                    background-position: center;">
            <div class="position-absolute top-0 start-0 w-100 h-100 hero-gradient"></div>
        </div>

        <div class="container position-relative"
            style="z-index: 10; padding-top: 80px; padding-bottom: 80px; min-height: 100vh; display: flex; align-items: center;">
            <div class="row justify-content-center text-center w-100">
                <div class="col-lg-10 col-xl-9">
                    <!-- Headline -->
                    <h1 class="display-3 fw-bold mb-4" style="line-height: 1.2;">
                        Rayakan Kelezatan di <span class="icon-highlight">Ruang Rasa</span>
                    </h1>

                    <!-- Subheadline -->
                    <p class="fs-4 mb-5" style="max-width: 800px; margin-left: auto; margin-right: auto;">
                        Tempat cerita dimulai — perpaduan autentik Nusantara dan sentuhan modern dalam suasana hangat &
                        tenang.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                        <a href="#menu-kami" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">
                            Lihat Menu Kami
                        </a>
                        {{-- <a href="#reservasi" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill shadow-lg">
                            Reservasi Meja
                        </a> --}}
                    </div>

                    {{-- <!-- Social Proof & Icons -->
                    <div class="d-flex justify-content-center align-items-center gap-4 gap-lg-5 flex-wrap mt-5 pt-3">
                        <!-- Customer Avatars -->
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex">
                                <img src="https://images.pexels.com/photos/4920899/pexels-photo-4920899.jpeg?auto=compress&cs=tinysrgb&w=120&h=120&fit=crop"
                                    class="rounded-circle border border-3 border-white shadow" width="50" height="50"
                                    alt="Customer">
                                <img src="https://images.pexels.com/photos/771742/pexels-photo-771742.jpeg?auto=compress&cs=tinysrgb&w=120&h=120&fit=crop"
                                    class="rounded-circle border border-3 border-white shadow" style="margin-left: -15px;"
                                    width="50" height="50" alt="Customer">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&h=120&q=80"
                                    class="rounded-circle border border-3 border-white shadow" style="margin-left: -15px;"
                                    width="50" height="50" alt="Customer">
                            </div>
                            <p class="mb-0 fw-semibold">10,000+ Pelanggan Puas</p>
                        </div>

                        <!-- Feature Icons -->
                        <div class="d-flex gap-4">
                            <div class="text-center">
                                <i class="ti ti-leaf fs-2 mb-2 d-block icon-secondary"></i>
                                <p class="small mb-0">Bahan Segar</p>
                            </div>
                            <div class="text-center">
                                <i class="ti ti-certificate fs-2 mb-2 d-block icon-highlight"></i>
                                <p class="small mb-0">Halal</p>
                            </div>
                            <div class="text-center">
                                <i class="ti ti-truck-delivery fs-2 mb-2 d-block icon-highlight"></i>
                                <p class="small mb-0">Pengiriman Cepat</p>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </section>

    <!-- Why Us Section -->
    <section class="py-5" style="padding-top: 5rem !important; padding-bottom: 5rem !important;">
        <div class="container">
            <!-- Section Header -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold mb-3">Apa yang Membuat Kami Berbeda</h2>
                    <p class="fs-5">Bahan premium • Resep warisan • Suasana yang bikin betah</p>
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="row g-4">
                <!-- Card 1: Chef -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-lg rounded-4 h-100 overflow-hidden hover-card">
                        <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=800"
                            class="card-img-top" alt="Cozy Interior" style="height: 220px; object-fit: cover;">
                        <div class="card-body text-center p-4">
                            <i class="ti ti-chef-hat fs-1 mb-3 d-block icon-highlight"></i>
                            <h5 class="fw-bold fs-5 mb-3">Chef Berpengalaman</h5>
                            <p class="mb-0" style="line-height: 1.6;">Setiap hidangan diolah dengan cinta dan keahlian
                                profesional.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Featured Dish -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-lg rounded-4 h-100 overflow-hidden hover-card"
                        style="background: linear-gradient(135deg, #f9bc60 0%, #e0a855 100%);">
                        <div class="card-body d-flex flex-column justify-content-center text-center p-4"
                            style="color: #001e1d;">
                            <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=800&q=80"
                                class="rounded-3 shadow mb-4 mx-auto" alt="Featured Dish"
                                style="max-height: 220px; width: auto; max-width: 100%; border: 4px solid #001e1d;">
                            <h2 class="fs-4 fw-bold mb-2" style="color: #001e1d;">Menu Spesial Bulan Ini</h2>
                            <p class="fs-6 mb-0" style="color: #001e1d;">Steak Saus Rendang & Es Kopi Aren Signature</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Quality Ingredients -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-lg rounded-4 h-100 overflow-hidden hover-card">
                        <img src="https://images.unsplash.com/photo-1517248135467-2c7ed3ab7229?auto=format&fit=crop&q=80&w=800"
                            class="card-img-top" alt="Bahan Berkualitas" style="height: 220px; object-fit: cover;">
                        <div class="card-body text-center p-4">
                            <i class="ti ti-medal fs-1 mb-3 d-block icon-highlight"></i>
                            <h5 class="fw-bold fs-5 mb-3">Bahan Berkualitas</h5>
                            <p class="mb-0" style="line-height: 1.6;">Segar dari supplier lokal terbaik, halal & terjamin.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Tabs Section -->
    <section class="py-5" style="padding-top: 5rem !important; padding-bottom: 5rem !important;">
        <div class="container">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-justified border-0 mb-5 shadow-sm rounded-pill overflow-hidden" id="servicesTab"
                role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 px-4 rounded-pill border-0" id="dine-in-tab" data-bs-toggle="tab"
                        data-bs-target="#dine-in" type="button" role="tab">
                        <i class="ti ti-tools-kitchen-2 me-2"></i>
                        <span class="d-none d-sm-inline">Dine In & Chill</span>
                        <span class="d-inline d-sm-none">Dine In</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 rounded-pill border-0" id="catering-tab" data-bs-toggle="tab"
                        data-bs-target="#catering" type="button" role="tab">
                        <i class="ti ti-truck me-2"></i>
                        <span class="d-none d-sm-inline">Catering Service</span>
                        <span class="d-inline d-sm-none">Catering</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 rounded-pill border-0" id="event-tab" data-bs-toggle="tab"
                        data-bs-target="#event" type="button" role="tab">
                        <i class="ti ti-confetti me-2"></i>
                        <span class="d-none d-sm-inline">Private Events</span>
                        <span class="d-inline d-sm-none">Events</span>
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content">
                <!-- Dine In Tab -->
                <div class="tab-pane fade show active" id="dine-in" role="tabpanel">
                    <div class="row align-items-center g-4 g-lg-5">
                        <div class="col-lg-6">
                            <img src="{{ asset('assets/images/backgrounds/lanpag.jpg') }}"
                                class="rounded-4 shadow-lg w-100" alt="Suasana Dine In"
                                style="max-height: 500px; object-fit: cover;">
                        </div>
                        <div class="col-lg-6">
                            <h2 class="display-6 fw-bold mb-4">Nikmati Suasana Nyaman</h2>

                            <!-- Accordion -->
                            <div class="accordion accordion-flush" id="dineInAccordion">
                                <div class="accordion-item mb-3 border-0 rounded-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold rounded-3 py-3"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne">
                                            <i class="ti ti-air-conditioning me-2 icon-highlight"></i>
                                            Area Indoor Ber-AC & Outdoor
                                        </button>
                                    </h2>
                                    <div id="flush-collapseOne" class="accordion-collapse collapse">
                                        <div class="accordion-body py-3">
                                            Pilih spot favoritmu untuk kerja atau kumpul keluarga.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item mb-3 border-0 rounded-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold rounded-3 py-3"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo">
                                            <i class="ti ti-wifi me-2 icon-highlight"></i>
                                            Free Wi-Fi Super Cepat
                                        </button>
                                    </h2>
                                    <div id="flush-collapseTwo" class="accordion-collapse collapse">
                                        <div class="accordion-body py-3">
                                            Cocok buat remote work sambil ngopi dan ngobrol.
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Catering Tab -->
                <div class="tab-pane fade" id="catering" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="ti ti-truck fs-1 mb-4 d-block icon-highlight"></i>
                        <h3 class="display-6 fw-bold mb-3">Catering Service Kami</h3>
                        <p class="fs-5 mb-0" style="max-width: 700px; margin: 0 auto;">
                            Siap melayani acara kantor, gathering, hingga intimate wedding dengan rasa premium Ruang Rasa.
                        </p>
                    </div>
                </div>

                <!-- Event Tab -->
                <div class="tab-pane fade" id="event" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="ti ti-confetti fs-1 mb-4 d-block icon-highlight"></i>
                        <h3 class="display-6 fw-bold mb-3">Private Events</h3>
                        <p class="fs-5 mb-0" style="max-width: 700px; margin: 0 auto;">
                            Ruang privat eksklusif untuk birthday, anniversary, atau meeting spesial.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-5 text-center cta-gradient" style="padding-top: 5rem !important; padding-bottom: 5rem !important;">
        <div class="container">
            <h2 class="display-5 fw-bold mb-3 text-dark">Siap Merasakan Pengalaman Baru?</h2>
            <p class="fs-5 mb-4 text-dark" style="max-width: 600px; margin: 0 auto;">
                Kunjungi Ruang Rasa hari ini atau pesan via aplikasi Ruang Rasa Mobile
            </p>
            <a href="#menu-kami" class="btn btn-dark btn-lg px-5 py-4 rounded-pill shadow-lg" style="font-weight: 600;">
                Jelajahi Menu
            </a>
        </div>
    </section>
@endsection
