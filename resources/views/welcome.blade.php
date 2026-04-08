@extends('layouts.lanpage')

@section('content')
    <section id="beranda" class="position-relative text-white overflow-hidden"
        style="min-height: 100vh; margin-top: -90px; padding-top: 90px;">

        <div class="position-absolute top-0 start-0 w-100 h-100 bg-image"
            style="background-image: url({{ asset('assets/images/backgrounds/lanpag.jpg') }});
                    background-size: cover;
                    background-position: center;">
            <div class="position-absolute top-0 start-0 w-100 h-100 hero-gradient"></div>
        </div>

        <div class="container position-relative"
            style="z-index: 10; padding-top: 60px; padding-bottom: 60px; min-height: 100vh; display: flex; align-items: center;">
            <div class="row justify-content-center text-center w-100 m-0">
                <div class="col-lg-10 col-xl-9">
                    <h1 class="display-3 fs-1 fw-bold mb-3 mb-md-4" style="line-height: 1.2;">
                        Rayakan Kelezatan di <span class="icon-highlight">Ruang Rasa</span>
                    </h1>

                    <p class="fs-5 mb-4 mb-md-5 opacity-90 px-2"
                        style="max-width: 800px; margin-left: auto; margin-right: auto;">
                        Tempat cerita dimulai — perpaduan autentik Nusantara dan sentuhan modern dalam suasana hangat &
                        tenang.
                    </p>

                    <div class="d-flex justify-content-center mb-4 mb-md-5">
                        <a href="#menu-kami"
                            class="btn btn-primary btn-lg px-4 py-2 px-md-5 py-md-3 rounded-pill shadow-lg border-0">
                            Lihat Menu Kami
                        </a>
                    </div>

                    <div
                        class="d-flex justify-content-center align-items-center flex-column flex-sm-row gap-3 mt-4 pt-4 border-top border-white border-opacity-25">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex">
                                <img src="https://images.pexels.com/photos/4920899/pexels-photo-4920899.jpeg?auto=compress&cs=tinysrgb&w=120&h=120&fit=crop"
                                    class="rounded-circle border border-2 border-white shadow" width="40" height="40"
                                    alt="User">
                                <img src="https://images.pexels.com/photos/771742/pexels-photo-771742.jpeg?auto=compress&cs=tinysrgb&w=120&h=120&fit=crop"
                                    class="rounded-circle border border-2 border-white shadow" style="margin-left: -15px;"
                                    width="40" height="40" alt="User">
                            </div>
                            <p class="mb-0 fw-medium small opacity-75">Bergabung dengan 10,000+ Pecinta Kopi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="kenapa-kami" class="py-5 bg-cafe-soft">
        <div class="container py-lg-5">
            <div class="row justify-content-center mb-5 text-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold mb-3 text-cafe-primary">Mengapa Memilih Kami?</h2>
                    <p class="fs-5 text-muted">Kualitas Premium • Cita Rasa Autentik • Pelayanan Prima</p>
                </div>
            </div>

            <div class="row g-4 align-items-stretch">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-card">
                        <img src="{{ asset('assets/images/backgrounds/barista.webp') }}" class="card-img-top" alt="Barista"
                            style="height: 240px; object-fit: cover;">
                        <div class="card-body text-center p-4 bg-cafe-primary text-white">
                            <h5 class="fw-bold fs-4 mb-3">Barista Profesional</h5>
                            <p class="mb-0 opacity-75">Setiap cangkir diracik oleh barista berpengalaman untuk presisi rasa
                                terbaik.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-lg rounded-4 h-100 overflow-hidden hover-card bg-cafe-accent-light">
                        <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                            <div class="mb-4">
                                <img src="{{ asset('assets/images/backgrounds/kopi2.avif') }}"
                                    class="rounded-circle shadow-lg border border-4 border-white mx-auto" alt="Menu"
                                    style="width: 160px; height: 160px; object-fit: cover;">
                            </div>
                            <h5 class="fw-bold fs-4 mb-2 text-cafe-primary">Signature Beans</h5>
                            <p class="text-cafe-primary opacity-75">House blend pilihan yang dipanggang sempurna untuk aroma
                                yang memikat.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-card">
                        <img src="{{ asset('assets/images/backgrounds/mesin.jpg') }}" class="card-img-top" alt="Mesin Kopi"
                            style="height: 240px; object-fit: cover;">
                        <div class="card-body text-center p-4 bg-cafe-primary text-white">
                            <h5 class="fw-bold fs-4 mb-3">Peralatan Modern</h5>
                            <p class="mb-0 opacity-75">Ekstraksi sempurna menggunakan teknologi terbaru untuk kualitas yang
                                konsisten.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-5" style="padding-top: 5rem !important; padding-bottom: 5rem !important;">
        <div class="container">
            <ul class="nav nav-tabs nav-justified border-0 mb-5 shadow-sm rounded-pill overflow-hidden" id="servicesTab"
                role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 px-4 rounded-pill border-0" id="nongkrong-tab" data-bs-toggle="tab"
                        data-bs-target="#nongkrong" type="button" role="tab">
                        <i class="ti ti-coffee me-2"></i>
                        <span class="d-none d-sm-inline">Pengalaman Dine-In</span>
                        <span class="d-inline d-sm-none">Dine-In</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 rounded-pill border-0" id="takeaway-tab" data-bs-toggle="tab"
                        data-bs-target="#takeaway" type="button" role="tab">
                        <i class="ti ti-bolt me-2"></i>
                        <span class="d-none d-sm-inline">Layanan Take Away</span>
                        <span class="d-inline d-sm-none">Take Away</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 rounded-pill border-0" id="menu-tab" data-bs-toggle="tab"
                        data-bs-target="#menu" type="button" role="tab">
                        <i class="ti ti-cup me-2"></i>
                        <span class="d-none d-sm-inline">Seleksi Menu</span>
                        <span class="d-inline d-sm-none">Menu</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="nongkrong" role="tabpanel">
                    <div class="row align-items-center g-4 g-lg-5">
                        <div class="col-lg-6">
                            <img src="{{ asset('assets/images/backgrounds/lanpag.jpg') }}"
                                class="rounded-4 shadow-lg w-100" alt="Suasana Kafe"
                                style="max-height: 500px; object-fit: cover;">
                        </div>
                        <div class="col-lg-6">
                            <h2 class="display-6 fw-bold mb-4">Ruang Nyaman untuk Setiap Momen</h2>

                            <div class="accordion accordion-flush" id="ngopiAccordion">
                                <div class="accordion-item mb-3 border-0 rounded-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold rounded-3 py-3"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#ngopiOne">
                                            <i class="ti ti-sofa me-2 icon-highlight"></i>
                                            Ambiance Kontemporer & Estetik
                                        </button>
                                    </h2>
                                    <div id="ngopiOne" class="accordion-collapse collapse">
                                        <div class="accordion-body py-3">
                                            Didesain khusus untuk memberikan ketenangan, baik untuk keperluan diskusi
                                            bisnis, bekerja jarak jauh, maupun bersantai.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item mb-3 border-0 rounded-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold rounded-3 py-3"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#ngopiTwo">
                                            <i class="ti ti-wifi me-2 icon-highlight"></i>
                                            Fasilitas Penunjang Produktivitas
                                        </button>
                                    </h2>
                                    <div id="ngopiTwo" class="accordion-collapse collapse">
                                        <div class="accordion-body py-3">
                                            Dilengkapi dengan koneksi Wi-Fi berkecepatan tinggi dan ketersediaan stopkontak
                                            di setiap sudut untuk mendukung efektivitas kerja Anda.
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="takeaway" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="ti ti-bolt fs-1 mb-4 d-block icon-highlight"></i>
                        <h3 class="display-6 fw-bold mb-3">Layanan Cepat & Praktis</h3>
                        <p class="fs-5 mb-0 text-muted" style="max-width: 700px; margin: 0 auto;">
                            Nikmati kopi favorit Anda tanpa perlu mengantre lama. Solusi sempurna bagi Anda dengan mobilitas
                            tinggi tanpa mengompromikan standar rasa.
                        </p>
                    </div>
                </div>

                <div class="tab-pane fade" id="menu" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="ti ti-cup fs-1 mb-4 d-block icon-highlight"></i>
                        <h3 class="display-6 fw-bold mb-3">Kurasi Menu Unggulan</h3>
                        <p class="fs-5 mb-0 text-muted" style="max-width: 700px; margin: 0 auto;">
                            Dari racikan klasik hingga inovasi modern, setiap menu kami dikembangkan secara teliti untuk
                            memberikan pengalaman rasa yang konsisten dan memuaskan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="menu-kami" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-cafe-primary">Menu Andalan Kami</h2>
                <p class="text-muted">Cita rasa autentik yang siap menemani ceritamu.</p>
            </div>

            <div class="row g-4">
                @foreach ($products as $product)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-card">
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">

                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold mb-0">{{ $product->name }}</h5>
                                    <span class="badge bg-cafe-primary rounded-pill">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="card-text text-muted small mb-0">
                                    {{ Str::limit($product->description, 80) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
