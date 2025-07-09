<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Lampung - Sistem Monitoring Energi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #2e7d32;
            --dark-green: #1b5e20;
            --light-green: #c8e6c9;
            --accent-green: #4caf50;
            --accent-yellow: #ffc107;
            --neutral-white: #ffffff;
            --neutral-light: #f5f5f5;
            --neutral-dark: #2c3e50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Montserrat', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            background-color: #f0f7f0;
            color: #333;
            line-height: 1.6;
            background-image: linear-gradient(to bottom right, #e0f2e9, #c8e6c9, #a5d6a7);
        }

        /* Header Styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.4s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header.scrolled {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 5%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

.logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 50pxx;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .logo-text .bank {
            color: #e74c3c;
        }

        .logo-text .lampung {
            color: #3498db;
        }

        /* Navigation */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.8rem;
        }

        .nav-link {
            padding: 0.5rem 0;
            text-decoration: none;
            font-weight: 600;
            color: var(--dark-green);
            font-size: 1rem;
            letter-spacing: 0.5px;
            position: relative;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: uppercase;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-green);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link:hover {
            color: var(--accent-green);
        }

        .nav-link i {
            font-size: 1.1rem;
        }

        /* Main Content */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 2rem;
            position: relative;
            background: url('https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            padding-top: 100px; /* Adjust this value based on your header height */
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(46, 125, 50, 0.85);
            z-index: 0;
        }

        .hero-content {
            max-width: 900px;
            margin-top: 0;
            position: relative;
            z-index: 1;
        }

        .hero-subtitle {
            font-size: 1.6rem;
            font-weight: 500;
            color: var(--accent-yellow);
            letter-spacing: 3px;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--neutral-white);
            line-height: 1.1;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
        }

        .hero-description {
            font-size: 1.2rem;
            color: var(--neutral-light);
            line-height: 1.8;
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            background: rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            border-radius: 10px;
            backdrop-filter: blur(5px);
        }

        .cta-button {
            display: inline-block;
            padding: 1.2rem 3rem;
            background: var(--accent-yellow);
            color: var(--dark-green);
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            transition: all 0.4s ease;
            font-size: 1.1rem;
            border: 2px solid var(--accent-yellow);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2rem;
        }

        .cta-button:hover {
            background: transparent;
            color: var(--neutral-white);
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        /* Data Grid Section */
        .data-monitoring-section {
            padding: 5rem 2rem;
            background: var(--neutral-light);
            text-align: center;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            max-width: 1000px;
            margin: 3rem auto 0;
            padding: 0 1rem;
        }

        .data-card {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--accent-green);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .data-card:hover {
            transform: translateY(-10px);
            background: var(--neutral-white);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .data-icon {
            font-size: 2.5rem;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .data-label {
            font-size: 1.1rem;
            color: var(--dark-green);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .data-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-green);
        }

        /* Features Section */
        .features-section {
            padding: 5rem 2rem;
            background: var(--neutral-white);
            text-align: center;
        }

        .section-title {
            font-size: 2.5rem;
            color: var(--dark-green);
            margin-bottom: 3rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--accent-green);
            border-radius: 2px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--neutral-light);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.5rem;
            color: var(--dark-green);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .feature-description {
            color: var(--neutral-dark);
            line-height: 1.7;
        }

        /* Footer Styling */
        .footer {
            background: var(--dark-green);
            color: var(--neutral-white);
            padding: 3rem 2rem;
            text-align: center;
        }

        .footer-content-wrapper { /* New wrapper for grid layout */
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr; /* Adjust column width as needed */
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            text-align: left; /* Align content to the left within columns */
        }

        .footer-col { /* Common styling for footer columns */
            margin-bottom: 1.5rem;
        }

        .footer-col img {
            max-height: 50px; /* Adjust logo size in footer */
            margin-bottom: 1rem;
        }

        .footer-col p {
            font-size: 0.9rem;
            line-height: 1.5;
            color: var(--neutral-light);
        }

        .footer-col h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-yellow); /* Matching accent color for titles */
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-col ul li {
            margin-bottom: 0.7rem;
        }

        .footer-col ul li a {
            color: var(--neutral-light);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .footer-col ul li a:hover {
            color: var(--accent-green);
        }

        .social-media-icons {
            display: flex;
            gap: 1rem; /* Adjust gap between icons */
            font-size: 1.5rem; /* Adjust icon size */
        }

        .social-media-icons a {
            color: var(--neutral-light);
            transition: color 0.3s ease;
        }

        .social-media-icons a:hover {
            color: var(--accent-yellow); /* Hover effect for social media icons */
        }

        .footer-bottom-text {
            text-align: center;
            font-size: 0.8rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light-green);
        }

        .footer-bottom-text .font-bold {
            font-weight: 700;
        }


        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 3rem;
            }
            
            .hero-description {
                font-size: 1.1rem;
            }
            
            .data-grid,
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .footer-content-wrapper {
                grid-template-columns: 1fr; /* Stack columns on smaller screens */
                text-align: center; /* Center align for stacked columns */
            }
            .footer-col {
                margin-bottom: 2rem; /* Add more space between stacked columns */
            }
            .social-media-icons {
                justify-content: center; /* Center social icons when stacked */
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 1rem;
            }
            
            .nav-links {
                margin-top: 1rem;
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }
            
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .hero-section {
                padding-top: 120px;
            }

            .hero-content {
                margin-top: 0;
            }
            
            .cta-button {
                padding: 1rem 2rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .nav-links {
                gap: 0.8rem;
            }
            
            .nav-link {
                font-size: 0.85rem;
                gap: 0.3rem;
            }
            
            .logo img { /* Ensure logo resizes well on small screens */
                max-height: 45px;
            }
            
            .hero-title {
                font-size: 1.8rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .hero-description {
                font-size: 0.95rem;
                padding: 1rem;
            }
            
            .data-grid,
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 2rem;
            }
            .footer-col h4 {
                font-size: 1.1rem;
            }
            .footer-col p, .footer-col ul li a {
                font-size: 0.85rem;
            }
            .social-media-icons {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
       <div class="logo">
                <div class="logo-icon">
                    <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung">
                </div>
                <div class="logo-text">
                </div>
            </div>


        <nav class="nav-links">
            <a href="#home" class="nav-link">
                <i class="fas fa-home"></i> HOME
            </a>
                        <a href="#monitoring" class="nav-link">
                <i class="fas fa-chart-line"></i> MONITORING
            </a>
            <a href="#about" class="nav-link">
                <i class="fas fa-info-circle"></i> TENTANG
            </a>
            <a href="#contact" class="nav-link">
                <i class="fas fa-envelope"></i> KONTAK
            </a>
            <a href="/register" class="nav-link">
                <i class="fas fa-id-card"></i> REGISTER
            </a>
        </nav>
    </header>

    <section class="hero-section" id="home">
        <div class="hero-content">
            <div class="hero-subtitle">SISTEM MONITORING ENERGI</div>
            <h1 class="hero-title">
                BANK LAMPUNG
            </h1>
            <p class="hero-description">
                Efisiensi dan transparansi energi menjadi fondasi dalam mewujudkan operasional Bank Lampung yang ramah lingkungan, berorientasi pada masa depan, dan memberi dampak positif bagi masyarakat serta lingkungan sekitar.
            </p>
            <a href="/login" class="cta-button">
                <i class="fas fa-rocket"></i> LOGIN
            </a>
        </div>
    </section>

    <section class="data-monitoring-section" id="monitoring">
        <h2 class="section-title">DATA MONITORING</h2>
        <div class="data-grid">
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="data-label">Konsumsi Listrik</div>
                <div class="data-value"></div>
            </div>
            
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <div class="data-label">Penggunaan Air</div>
                <div class="data-value"></div>
            </div>
            
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="data-label">Penggunaan Kertas</div>
                <div class="data-value"></div>
            </div>
            
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-gas-pump"></i>
                </div>
                <div class="data-label">Konsumsi BBM</div>
                <div class="data-value"></div>
            </div>
        </div>
    </section>

    <section class="features-section" id="about">
        <h2 class="section-title">FITUR UTAMA</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="feature-title">Analisis Real-time</h3>
                <p class="feature-description">
                    Pantau konsumsi energi secara langsung dengan grafik interaktif dan laporan real-time untuk seluruh cabang Bank Lampung.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3 class="feature-title">Efisiensi Hijau</h3>
                <p class="feature-description">
                    Sistem kami membantu mengurangi jejak karbon dengan mengidentifikasi area potensial penghematan energi dan sumber daya.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h3 class="feature-title">Dashboard Intuitif</h3>
                <p class="feature-description">
                    Antarmuka pengguna yang mudah digunakan dengan panel kontrol lengkap untuk manajemen energi yang efektif.
                </p>
            </div>
        </div>
    </section>

    <footer class="footer" id="contact">
    <div class="footer-content-wrapper">
        <div class="footer-col">
            <img src="{{ asset('assets/img/BLPUTIH.png') }}" alt="Bank Lampung">
            <p class="text-xs leading-5">
                Jl. Wolter Monginsidi No.182, Teluk Betung, Kota Bandar Lampung, Lampung 35211, Indonesia<br />
                Call Center: 1500575
            </p>
        </div>

        <div class="footer-col">
            <h4>Tentang Kami</h4>
            <ul>
                <li><a href="https://www.banklampung.co.id/profil" target="_blank" rel="noopener">Profil</a></li>
                <li><a href="https://www.banklampung.co.id/manajemen-bank-lampung" target="_blank" rel="noopener">Manajemen</a></li>
                <li><a href="https://www.banklampung.co.id/kontak-kami" target="_blank" rel="noopener">Kontak</a></li>
                <li><a href="https://rekrutmen.banklampung.co.id/" target="_blank" rel="noopener">Rekrutmen</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Sosial Media</h4>
            <div class="social-media-icons">
                <a href="https://www.tiktok.com/@banklampungofficial" target="_blank" rel="noopener" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                <a href="https://www.facebook.com/people/Bank-Lampung/100070309804120/" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/bpd_lampung/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://www.youtube.com/channel/UCzQzr2QsuE3lE5rLlFpSoxw" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom-text">
        PT Bank Lampung Berizin & diawasi oleh Otoritas Jasa Keuangan serta merupakan peserta penjaminan LPS<br />
        © 2025 <span class="font-bold">Bank Lampung</span>.
    </div>
</footer>


    <script>
        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    // Calculate offset to account for fixed header
                    const headerOffset = document.querySelector('.header').offsetHeight;
                    const elementPosition = target.getBoundingClientRect().top + window.pageYOffset;
                    const offsetPosition = elementPosition - headerOffset - 20; // Added extra padding

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>