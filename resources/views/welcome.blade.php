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

        /* Background decorative elements */
        body::before {
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

        body::after {
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

        .nav-link.registrasi {
            background: rgba(255, 255, 255, 0.2);
            color: #2c3e50;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .nav-link.registrasi:hover {
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

        .nav-link.home {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .nav-link.home:hover {
            background: linear-gradient(45deg, #2980b9, #2471a3);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
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

        /* Chart Area */
        .content-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

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

        .bar {
            background: linear-gradient(to top, #27ae60, #2ecc71, #58d68d);
            border-radius: 8px 8px 0 0;
            min-width: 40px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .bar:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .bar:nth-child(1) { height: 60%; }
        .bar:nth-child(2) { height: 80%; }
        .bar:nth-child(3) { height: 95%; }
        .bar:nth-child(4) { height: 100%; }

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

            <!-- Laravel Authentication Routes Integration -->
            <nav class="nav-links">
                <!-- Simulated Laravel Blade conditional rendering -->
                <!-- @if (Route::has('login')) -->
                    <!-- @auth -->
                        <!-- If user is authenticated, show Home link -->
                        <a href="/home" class="nav-link home">Home</a>
                    <!-- @else -->
                        <!-- If user is not authenticated, show Login and Register links -->
                        <a href="/login" class="nav-link login">Log in</a>
                        
                        <!-- @if (Route::has('register')) -->
                            <a href="/register" class="nav-link registrasi">Registrasi</a>
                        <!-- @endif -->
                    <!-- @endauth -->
                <!-- @endif -->
            </nav>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-left">
                <h1 class="title">
                    Tentang Sistem<br>
                    Monitoring Energi
                </h1>
                <p class="description">
                    Sistem Monitoring Energi Bank Lampung adalah sebuah inisiatif digital akan bertujuan untuk mendukung operasional ramah lingkungan melalui pemantauan penggunaan energi secara efisien dan transparan. Sistem ini mencatat adn dan menganalisis data konsumsi listrik, air, kertas, dan BBM di seluruh unit kerja Bank Lampung.
                </p>
                <a href="#" class="cta-button">Masuk Sekarang</a>
            </div>

            <div class="content-right">
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

    <script>
        // Simulate Laravel authentication state
        // In a real Laravel application, this would be handled server-side
        const isAuthenticated = false; // Change this to true to simulate logged-in state
        const hasRegisterRoute = true; // Simulates Route::has('register')

        function updateNavigation() {
            const navLinks = document.querySelector('.nav-links');
            
            if (isAuthenticated) {
                // Show Home link for authenticated users
                navLinks.innerHTML = '<a href="/home" class="nav-link home">Home</a>';
            } else {
                // Show Login and Register links for guests
                let navHTML = '<a href="/login" class="nav-link login">Log in</a>';
                
                if (hasRegisterRoute) {
                    navHTML += '<a href="/register" class="nav-link registrasi">Registrasi</a>';
                }
                
                navLinks.innerHTML = navHTML;
            }
        }

        // Initialize navigation on page load
        document.addEventListener('DOMContentLoaded', updateNavigation);

        // Add smooth hover effects for bars
        const bars = document.querySelectorAll('.bar');
        bars.forEach((bar, index) => {
            bar.addEventListener('mouseenter', () => {
                bar.style.transform = 'scale(1.05) translateY(-5px)';
            });
            
            bar.addEventListener('mouseleave', () => {
                bar.style.transform = 'scale(1) translateY(0)';
            });
        });

        // Add click animation for CTA button
        const ctaButton = document.querySelector('.cta-button');
        ctaButton.addEventListener('click', (e) => {
            e.preventDefault();
            ctaButton.style.transform = 'scale(0.95)';
            setTimeout(() => {
                ctaButton.style.transform = 'scale(1)';
            }, 150);
        });
    </script>
</body>
</html>