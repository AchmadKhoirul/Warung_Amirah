<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Warung Amirah - Toko Klontong & Grosir Terpercaya</title>
    <meta content="Toko klontong dan grosir yang menyediakan berbagai kebutuhan pokok dengan harga terjangkau dan kualitas terbaik" name="description">
    <meta content="toko klontong, grosir, kebutuhan pokok, sembako, warung amirah" name="keywords">

    <!-- Favicons -->
    <link href="<?= base_url('NiceAdmin/assets/img/favicon.png') ?>" rel="icon">
    <link href="<?= base_url('NiceAdmin/assets/img/apple-touch-icon.png') ?>" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="<?= base_url('NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('NiceAdmin/assets/vendor/boxicons/css/boxicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('NiceAdmin/assets/vendor/quill/quill.snow.css') ?>" rel="stylesheet">
    <link href="<?= base_url('NiceAdmin/assets/vendor/quill/quill.bubble.css') ?>" rel="stylesheet">
    <link href="<?= base_url('NiceAdmin/assets/vendor/remixicon/remixicon.css') ?>" rel="stylesheet">
    <link href="<?= base_url('NiceAdmin/assets/vendor/simple-datatables/style.css') ?>" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?= base_url('NiceAdmin/assets/css/style.css') ?>" rel="stylesheet">

    <style>
        body {
            background: #faf0e6;
        }
        .hero-section {
            background: linear-gradient(135deg, #f5f5dc 0%, #deb887 100%);
            color: #8b4513;
            padding: 100px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #8b4513;
        }
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
            color: #8b4513;
        }
        .btn-primary-custom {
            background: #8b4513;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #fff;
            display: inline-block;
        }
        .btn-primary-custom:hover {
            background: #654321;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 69, 19, 0.4);
        }
        .features-section {
            padding: 80px 0;
            background: #faf0e6;
        }
        .feature-card {
            background: #fff8dc;
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            height: 100%;
            border: 1px solid #f0e68c;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            color: #deb887;
            margin-bottom: 20px;
        }
        .about-section {
            padding: 80px 0;
            background: #fff8dc;
        }
        .about-content {
            padding: 40px;
            color: #8b4513;
        }
        .about-image {
            border-radius: 15px;
            overflow: hidden;
        }
        .cta-section {
            background: linear-gradient(135deg, #f5f5dc 0%, #deb887 100%);
            color: #8b4513;
            padding: 80px 0;
            text-align: center;
        }
        .cta-section .btn-primary-custom {
            background: #8b4513;
            color: #fff;
        }
        .cta-section .btn-primary-custom:hover {
            background: #654321;
            color: #fff;
        }
        .cta-section .btn.btn-outline-light {
            border-color: #8b4513;
            color: #8b4513;
        }
        .cta-section .btn.btn-outline-light:hover {
            background: #8b4513;
            color: #fff;
        }
        .footer {
            background: #deb887;
            color: #fff;
            padding: 40px 0;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="<?= base_url() ?>" class="logo d-flex align-items-center">
                <img src="<?= base_url('NiceAdmin/assets/img/warung_amirah.png') ?>" alt="Warung Amirah">
                <span class="d-none d-lg-block">Warung Amirah</span>
            </a>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('login') ?>">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Login</span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <!-- ======= Hero Section ======= -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1>Selamat Datang di Warung Amirah</h1>
                        <p>Toko klontong dan grosir terpercaya yang menyediakan berbagai kebutuhan pokok dengan harga terjangkau dan kualitas terbaik untuk memenuhi kebutuhan sehari-hari keluarga Anda.</p>
                        <a href="<?= base_url('login') ?>" class="btn-primary-custom">
                            <i class="bi bi-cart-plus me-2"></i>Beli Sekarang
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="<?= base_url('NiceAdmin/assets/img/Warung_Amirah.png') ?>" alt="Toko Klontong" class="img-fluid" style="border-radius: 15px;">
                </div>
            </div>
        </div>
    </section>

    <!-- ======= Features Section ======= -->
    <section class="features-section">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="fw-bold">Mengapa Memilih Warung Amirah?</h2>
                    <p class="text-muted">Kami berkomitmen memberikan pelayanan terbaik dengan berbagai keunggulan</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-tags"></i>
                        </div>
                        <h4>Harga Terjangkau</h4>
                        <p>Dapatkan berbagai produk kebutuhan pokok dengan harga grosir yang lebih hemat untuk kantong Anda.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4>Kualitas Terjamin</h4>
                        <p>Produk-produk berkualitas tinggi dengan tanggal kadaluarsa yang masih jauh untuk keamanan konsumsi.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h4>Pengiriman Cepat</h4>
                        <p>Layanan pengiriman yang cepat dan aman ke lokasi Anda dengan berbagai pilihan layanan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======= About Section ======= -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-image">
                        <img src="<?= base_url('NiceAdmin/assets/img/Warung_Amirah.png') ?>" alt="Tentang Kami" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2 class="fw-bold mb-4">Tentang Warung Amirah</h2>
                        <p class="mb-4">Warung Amirah adalah toko klontong dan grosir yang telah melayani masyarakat dengan berbagai kebutuhan pokok sejak lama. Kami menyediakan berbagai produk sembako, makanan ringan, minuman, dan kebutuhan rumah tangga lainnya.</p>
                        <p class="mb-4">Dengan pengalaman bertahun-tahun, kami memahami kebutuhan pelanggan dan selalu berusaha memberikan produk berkualitas dengan harga yang kompetitif.</p>
                        <div class="row">
                            <div class="col-6">
                                <h4 class="text-primary">1000+</h4>
                                <p>Produk Tersedia</p>
                            </div>
                            <div class="col-6">
                                <h4 class="text-primary">500+</h4>
                                <p>Pelanggan Puas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======= CTA Section ======= -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-4">Siap Berbelanja?</h2>
                    <p class="mb-4">Daftar sekarang dan nikmati kemudahan berbelanja kebutuhan pokok dengan harga terbaik!</p>
                    <a href="<?= base_url('login') ?>" class="btn-primary-custom me-3">
                        <i class="bi bi-cart-plus me-2"></i>Beli Sekarang
                    </a>
                    <a href="<?= base_url('register') ?>" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-person-plus me-2"></i>Daftar
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ======= Footer ======= -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h4>Warung Amirah</h4>
                    <p>Toko Klontong & Grosir Terpercaya</p>
                    <p>&copy; 2024 Warung Amirah. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="<?= base_url('NiceAdmin/assets/vendor/apexcharts/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('NiceAdmin/assets/vendor/chart.js/chart.umd.js') ?>"></script>
    <script src="<?= base_url('NiceAdmin/assets/vendor/echarts/echarts.min.js') ?>"></script>
    <script src="<?= base_url('NiceAdmin/assets/vendor/quill/quill.min.js') ?>"></script>
    <script src="<?= base_url('NiceAdmin/assets/vendor/simple-datatables/simple-datatables.js') ?>"></script>
    <script src="<?= base_url('NiceAdmin/assets/vendor/tinymce/tinymce.min.js') ?>"></script>
    <script src="<?= base_url('NiceAdmin/assets/vendor/php-email-form/validate.js') ?>"></script>

    <!-- Template Main JS File -->
    <script src="<?= base_url('NiceAdmin/assets/js/main.js') ?>"></script>
</body>
</html> 