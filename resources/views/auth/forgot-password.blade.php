<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Hemat Energi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #c6f9c6, #a5e9a5);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
        /* Logo at the top */
        .icon-group {
            display: flex;
            justify-content: center;
            margin-bottom: 15px; /* Reduced the margin for better proximity */
        }
        .icon-group img {
            height: 100px; /* Adjusted size of logos */
            margin: 0 10px; /* Reduced space between logos */
        }
        .forgot-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 25px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2e4d24;
        }
        .subtitle {
            font-size: 14px;
            color: #555;
            background: #f5e9ff;
            border-left: 5px solid #9c27b0;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }
        .form-control {
            border: 2px solid #2e7d32;
            border-radius: 8px;
            padding: 10px 12px;
        }
        .btn-success {
            background-color: #2e7d32;
            border: none;
            width: 100%;
            border-radius: 8px;
        }
        .footer {
            background-color: #e0e0e0; /* Background color for the footer */
            text-align: center;
            font-size: 13px;
            margin-top: 30px;
            color: #444;
            padding: 10px 0;
        }
        .footer img {
            height: 28px;
            margin-bottom: 6px;
        }
        a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-top: 12px;
        }
    </style>
</head>
<body>

    <!-- ✅ Icon Energi Atas -->
    <div class="icon-group">
        <img src="{{ asset('assets/img/ataslogin.png') }}" alt="Ikon Energi">
    </div>

    <div class="forgot-container">
        <div class="title">🔑 Lupa Password</div>

        {{-- ✅ NOTIFIKASI SUKSES --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
                ✅ {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- ✅ NOTIFIKASI CUSTOM (jika ada) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- ❌ NOTIFIKASI ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <div class="subtitle">
            Masukkan email yang terdaftar pada akun Anda. Kami akan mengirimkan link untuk mereset password ke email tersebut.
        </div>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3 text-start">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan Email Anda" required>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-success">Kirim Link Reset Password</button>
        </form>

        <a href="{{ route('login') }}">⬅ Kembali ke Login</a>
    </div>

    <div class="footer">
        <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung"><br>
        ©2025 PT. Bank Lampung - Sistem Monitoring Energi
    </div>

    <!-- Bootstrap JS for close alert -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
