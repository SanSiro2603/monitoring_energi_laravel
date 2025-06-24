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

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #a8e6cf 0%, #88d8a3 50%, #68c783 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
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
            position: relative;
            z-index: 10;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 200px;
            height: 200px;
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
            font-size: 1.5rem;
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
        }

        .content-left {
            flex: 1;
            max-width: 500px;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
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

            <!-- Laravel Authentication Routes -->
            <nav class="nav-links">
                @if (Route::has('login'))
                    @auth
                
                    @else
                        <a href="{{ route('tentang') }}" class="nav-link tentang">Tentang</a>
                        <a href="{{ route('login') }}" class="nav-link login">Login</a>
                    @endauth
                @endif
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
                    Efesiensi dan Transparansi energi untuk operasional Ramah Lingkungan di Bank Lampung
                </p>
                @auth
                    <a href="{{ route('dashboard') }}" class="cta-button">Masuk Sekarang</a>
                @else
                    <a href="{{ route('login') }}" class="cta-button">Masuk Sekarang</a>
                @endauth
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

    <script>
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
        const ctaButton = document.querySelector('.cta-button');
        ctaButton.addEventListener('click', (e) => {
            ctaButton.style.transform = 'scale(0.95)';
            setTimeout(() => {
                ctaButton.style.transform = 'scale(1)';
            }, 150);
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
    </script>
</body>
</html>