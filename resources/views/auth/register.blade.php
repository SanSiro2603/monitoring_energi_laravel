<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Bank Lampung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(to bottom, var(--light-green), var(--accent-green));
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .main-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            width: 100%;
            padding: 20px;
        }

        .btn-back-floating {
            position: absolute;
            left: 20px;
            top: 20px;
            z-index: 10;
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--primary-green);
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .btn-back-floating:hover {
            background-color: var(--primary-green);
            color: var(--neutral-white);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .btn-back-floating span {
            display: none;
            margin-left: 8px;
            font-size: 1rem;
            white-space: nowrap;
        }

        .btn-back-floating:hover {
            width: auto;
            padding: 0 15px;
            border-radius: 25px;
        }

        .btn-back-floating:hover span {
            display: inline;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-section img {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }

        .header-section h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-green);
        }

        .register-container {
            max-width: 450px;
            width: 100%;
            padding: 30px;
            background: var(--neutral-white);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
            flex-shrink: 0;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .input-icon {
            position: relative;
            width: 100%;
        }

        .input-icon i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: var(--primary-green);
            font-size: 1.1rem;
            z-index: 5;
        }

        .form-control {
            border: 2px solid var(--light-green);
            border-radius: 12px;
            padding: 12px 15px 12px 45px;
            width: 100%;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
            outline: none;
        }

        .btn-success {
            background-color: var(--primary-green);
            border: none;
            width: 100%;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-success:hover {
            background-color: var(--dark-green);
            transform: translateY(-2px);
        }

        .mt-3 {
            margin-top: 20px !important;
            font-size: 0.95rem;
            color: var(--neutral-dark);
        }

        .mt-3 a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .mt-3 a:hover {
            color: var(--accent-green);
            text-decoration: underline;
        }

        .footer {
            text-align: center;
            font-size: 0.85rem;
            margin-top: 30px;
            color: var(--dark-green);
            width: 100%;
            padding-bottom: 20px;
            flex-shrink: 0;
        }

        .footer img {
            height: 35px;
            margin-bottom: 8px;
        }

        .alert {
            font-size: 0.9rem;
            padding: 0.8rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: left;
        }

        .alert-success {
            background-color: var(--light-green);
            color: var(--dark-green);
            border-color: var(--accent-green);
        }

        .alert-danger {
            background-color: #ffe0b2;
            color: #d32f2f;
            border-color: #ffb74d;
        }

        .alert-dismissible .btn-close {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <div class="btn-back-floating" onclick="window.location.href='{{ url('/') }}'">
        <i class="fas fa-arrow-left"></i>
        <span>Back</span>
    </div>

    <div class="main-wrapper">
        <div class="header-section">
            <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung">
            <h2>Monitoring Energi</h2>
        </div>

        <div class="register-container">
            <div class="title">Registrasi Akun</div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✅ {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>❌ {{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                    </div>
                    @error('name') <small class="text-danger d-block mt-1 text-start">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-user-circle"></i>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" value="{{ old('username') }}" required>
                    </div>
                    @error('username') <small class="text-danger d-block mt-1 text-start">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                    </div>
                    @error('email') <small class="text-danger d-block mt-1 text-start">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-building"></i>
                        <input type="text" name="unit_kerja" class="form-control @error('unit_kerja') is-invalid @enderror" placeholder="Unit Kerja" value="{{ old('unit_kerja') }}" required>
                    </div>
                    @error('unit_kerja') <small class="text-danger d-block mt-1 text-start">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                    </div>
                    @error('password') <small class="text-danger d-block mt-1 text-start">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Daftar</button>
            </form>

            <div class="mt-3">
                <span>Sudah punya akun?</span>
                <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>

    <div class="footer">
    <div>
        <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung" style="height: 40px; margin-bottom: 5px;">
    </div>
    <div>©2025 PT. Bank Lampung - Sistem Monitoring Energi</div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
