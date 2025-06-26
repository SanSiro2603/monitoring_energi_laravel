<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - Hemat Energi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-image: url("<?php echo asset('assets/img/rumpu.png'); ?>");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 40px;
      font-family: 'Segoe UI', sans-serif;
      position: relative;
    }

    .btn-back {
      position: absolute;
      left: 20px;
      top: 40px;
      z-index: 10;
      background-color: #fff;
    }

    .top-logo {
      text-align: center;
      margin-bottom: 20px;
    }

    .top-logo img {
      width: 150px;
      margin-bottom: 10px;
    }

    .top-logo h3 {
      color: #000;
      font-weight: bold;
      font-size: 24px;
    }

    .login-box {
      background-color: #fff;
      border-radius: 20px;
      padding: 30px 25px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      width: 100%;
      max-width: 360px;
      text-align: center;
    }

    .login-box h4 {
      color: green;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .form-control {
      border-radius: 12px;
      margin-bottom: 18px;
      padding-left: 40px;
    }

    .form-group {
      position: relative;
    }

    .form-group i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #aaa;
    }

    .btn-login {
      background-color: #1f6f29;
      color: white;
      border-radius: 25px;
      width: 100%;
      padding: 10px;
      font-weight: bold;
    }

    .btn-login:hover {
      background-color: #155d22;
    }

    .bottom-text {
      margin-top: 16px;
      font-size: 14px;
      color: #155d22;
    }

    .footer-bank {
      background-color: #fff;
      width: 100%;
      padding: 15px 0;
      display: flex;
      justify-content: center;
      align-items: center;
      position: fixed;
      bottom: 0;
    }

    .footer-bank img {
      width: 160px;
    }
  </style>
</head>
<body>

  <!-- ✅ Tombol Halaman Awal di kiri -->
  <div class="btn-back">
    <form action="{{ url('/') }}" method="GET">
      <button type="submit" class="btn btn-outline-success btn-sm">
        <i class="bi bi-arrow-left-circle"></i> Halaman Awal
      </button>
    </form>
  </div>

  <!-- Bagian Atas: Logo & Judul -->
  <div class="top-logo">
    <img src="{{ asset('assets/img/ataslogin.png') }}" alt="Icon Energi">
    <h3>Hemat Energi</h3>
  </div>

  <!-- Kotak Login -->
  <div class="login-box">
    <h4>Login</h4>

    @if(session('success'))
      <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/login">
      @csrf
      <div class="form-group">
        <i class="bi bi-person-fill"></i>
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>

      <div class="form-group">
        <i class="bi bi-lock-fill"></i>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>

      <button type="submit" class="btn btn-login">Login</button>
    </form>

    <div class="bottom-text">
      <a href="{{ route('password.request') }}">Lupa password?</a>
    </div>
  </div>

  <!-- Footer Logo Bank Full Width -->
  <div class="footer-bank">
    <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung">
  </div>

</body>
</html>
