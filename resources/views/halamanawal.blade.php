<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Lampung - Sistem Monitoring Energi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Section 1: Main Landing */
        .section-1 {
            background: linear-gradient(135deg, #a8e6cf 0%, #88d8a3 50%, #68c783 100%);
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* Section 2: About */
        .section-2 {
            background: linear-gradient(135deg, #a8e6cf 0%, #88d8a3 50%, #68c783 100%);
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* Background decorative elements */
        .section-2::before {
            content: '';
            position: absolute;
            top: 20%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .section-2::after {
            content: '';
            position: absolute;
            bottom: -10%;
            right: 20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(168, 230, 207, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .header.scrolled {
            background: rgba(168, 230, 207, 0.98);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
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
            gap: 1rem;
        }

        .nav-link {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            font-weight: 600;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .nav-link.tentang {
            background: rgba(255, 255, 255, 0.2);
            color: #2c3e50;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .nav-link.tentang:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .nav-link.login {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }

        .nav-link.login:hover {
            background: linear-gradient(45deg, #219a52, #27ae60);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.4);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 2rem;
            gap: 4rem;
            margin-top: 100px;
        }

        .content-left {
            flex: 1;
            max-width: 500px;
            opacity: 0;
            transform: translateX(-50px);
            animation: slideInLeft 1s ease 0.3s forwards;
        }

        .title {
            font-size: 2.8rem;
            font-weight: bold;
            color: #2c3e50;
            line-height: 1.2;
            margin-bottom: 2rem;
        }

        .description {
            font-size: 1.1rem;
            color: #34495e;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            text-align: justify;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, #f39c12, #e67e22);
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.3);
        }

        .cta-button:hover {
            background: linear-gradient(45deg, #e67e22, #d35400);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(243, 156, 18, 0.4);
        }

        /* Dashboard Area */
        .content-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            opacity: 0;
            transform: translateX(50px);
            animation: slideInRight 1s ease 0.6s forwards;
        }

        .dashboard-container {
            width: 400px;
            height: 350px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        /* Dashboard Header */
        .dashboard-header {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .dashboard-header h3 {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .dashboard-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .dashboard-btn {
            padding: 0.3rem 0.8rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dashboard-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .dashboard-btn.active {
            background: rgba(255, 255, 255, 0.9);
            color: #27ae60;
        }

        /* Grass decoration */
        .grass {
            height: 20px;
            background: linear-gradient(to top, #229954, #27ae60);
            position: relative;
            margin-bottom: 1rem;
        }

        .grass::before {
            content: '';
            position: absolute;
            top: -5px;
            left: 0;
            right: 0;
            height: 10px;
            background: repeating-linear-gradient(
                90deg,
                #229954 0px,
                #27ae60 2px,
                #229954 4px
            );
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 1rem;
            display: flex;
            gap: 1rem;
        }

        .chart-section {
            flex: 1;
            background: rgba(240, 248, 255, 0.8);
            border-radius: 10px;
            padding: 1rem;
            min-height: 180px;
            position: relative;
        }

        .chart-bars {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 100px;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .bar {
            background: linear-gradient(to top, #27ae60, #2ecc71);
            border-radius: 4px 4px 0 0;
            min-width: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
        }

        .bar:nth-child(1) { height: 40%; }
        .bar:nth-child(2) { height: 60%; }
        .bar:nth-child(3) { height: 80%; }
        .bar:nth-child(4) { height: 100%; }

        .data-section {
            flex: 1;
            background: rgba(248, 249, 250, 0.9);
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .data-label {
            color: #666;
            background: #e9ecef;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .data-value {
            color: #333;
            font-weight: 500;
            background: #f8f9fa;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        /* Dashboard Footer Buttons */
        .dashboard-footer {
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .footer-btn {
            padding: 0.5rem 1rem;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .footer-btn:hover {
            background: #5a6268;
        }

        /* About Section Chart */
        .chart-container {
            width: 350px;
            height: 250px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .chart-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .chart {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 150px;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }

        .chart .bar {
            background: linear-gradient(to top, #27ae60, #2ecc71, #58d68d);
            border-radius: 8px 8px 0 0;
            min-width: 40px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .chart .bar:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .chart .bar:nth-child(1) { height: 60%; }
        .chart .bar:nth-child(2) { height: 80%; }
        .chart .bar:nth-child(3) { height: 95%; }
        .chart .bar:nth-child(4) { height: 100%; }

        /* Animations */
        @keyframes slideInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
                position: relative;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .main-content {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
                padding: 1rem;
                margin-top: 120px;
            }

            .title {
                font-size: 2rem;
            }

            .dashboard-container {
                width: 100%;
                max-width: 350px;
                height: auto;
            }

            .dashboard-content {
                flex-direction: column;
            }

            .chart-container {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 480px) {
            .nav-links {
                flex-direction: column;
                width: 100%;
            }

            .nav-link {
                width: 100%;
                text-align: center;
            }

            .title {
                font-size: 1.8rem;
            }

            .description {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Section 1: Main Landing -->
    <section id="home" class="section-1">
        <div class="container">
           <!-- Header -->
        <header class="header">
            <div class="logo">
                <div class="logo-icon">
                    <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung">
                </div>
                <div class="logo-text">
                </div>
            </div>

                <nav class="nav-links">
                    <a href="#about" class="nav-link tentang">Tentang</a>
                    <a href="/login" class="nav-link login">Login</a>
                    <a href="/register" class="nav-link login">Register</a>
                </nav>
            </header>

            <!-- Main Content -->
            <main class="main-content">
                <div class="content-left">
                    <h1 class="title">
                        Selamat Datang Di<br>
                        Sistem Monitoring<br>
                        Energi
                    </h1>
                    <p class="description">
                        Efisiensi dan Transparansi energi untuk operasional Ramah Lingkungan di Bank Lampung
                    </p>
                    <a href="/login" class="cta-button">Masuk Sekarang</a>
                </div>

                <div class="content-right">
                    <div class="dashboard-container">
                        <!-- Dashboard Header -->
                        <div class="dashboard-header">
                            <h3>Hemat Energi</h3>
                            <div class="dashboard-buttons">
                                <button class="dashboard-btn active">Input Data</button>
                                <button class="dashboard-btn">Report</button>
                            </div>
                        </div>

                        <!-- Grass Decoration -->
                        <div class="grass"></div>

                        <!-- Dashboard Content -->
                        <div class="dashboard-content">
                            <!-- Chart Section -->
                            <div class="chart-section">
                                <div class="chart-bars">
                                    <div class="bar"></div>
                                    <div class="bar"></div>
                                    <div class="bar"></div>
                                    <div class="bar"></div>
                                </div>
                            </div>

                            <!-- Data Section -->
                            <div class="data-section">
                                <div class="data-row">
                                    <span class="data-label">Listrik</span>
                                    <span class="data-value">1,245 kWh</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">Air</span>
                                    <span class="data-value">850 L</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">Kertas</span>
                                    <span class="data-value">125 Rim</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">BBM</span>
                                    <span class="data-value">45 L</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">Gas</span>
                                    <span class="data-value">25 m³</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">Total</span>
                                    <span class="data-value">2,290</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dashboard Footer -->
                        <div class="dashboard-footer">
                            <button class="footer-btn">View</button>
                            <button class="footer-btn">Edit</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </section>

    <!-- Section 2: About -->
    <section id="about" class="section-2">
        <div class="container">
            <!-- Main Content -->
            <main class="main-content">
                <div class="content-left fade-in-up">
                    <h1 class="title">
                        Tentang Sistem<br>
                        Monitoring Energi
                    </h1>
                    <p class="description">
                        Sistem Monitoring Energi Bank Lampung adalah sebuah inisiatif digital yang bertujuan untuk mendukung operasional ramah lingkungan melalui pemantauan penggunaan energi secara efisien dan transparan. Sistem ini mencatat dan menganalisis data konsumsi listrik, air, kertas, dan BBM di seluruh unit kerja Bank Lampung.
                    </p>
                    <a href="/login" class="cta-button">Masuk Sekarang</a>
                </div>

                <div class="content-right fade-in-up">
                    <div class="chart-container">
                        <div class="chart">
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </section>

    <script>
        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });

        // Interactive dashboard buttons
        const dashboardBtns = document.querySelectorAll('.dashboard-btn');
        dashboardBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                dashboardBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });

        // Chart bar hover effects
        const bars = document.querySelectorAll('.bar');
        bars.forEach((bar, index) => {
            bar.addEventListener('mouseenter', () => {
                bar.style.transform = 'scaleY(1.1)';
                bar.style.filter = 'brightness(1.2)';
            });
            
            bar.addEventListener('mouseleave', () => {
                bar.style.transform = 'scaleY(1)';
                bar.style.filter = 'brightness(1)';
            });
        });

        // CTA button click animation
        const ctaButtons = document.querySelectorAll('.cta-button');
        ctaButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                button.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 150);
            });
        });

        // Footer button interactions
        const footerBtns = document.querySelectorAll('.footer-btn');
        footerBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                btn.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    btn.style.transform = 'scale(1)';
                }, 100);
            });
        });

        // Chart bars in about section
        const chartBars = document.querySelectorAll('.chart .bar');
        chartBars.forEach((bar, index) => {
            bar.addEventListener('mouseenter', () => {
                bar.style.transform = 'scale(1.05) translateY(-5px)';
            });
            
            bar.addEventListener('mouseleave', () => {
                bar.style.transform = 'scale(1) translateY(0)';
            });
        });
    </script>
</body>
</html>