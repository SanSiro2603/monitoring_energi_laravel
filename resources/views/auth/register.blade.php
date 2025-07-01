<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Registrasi - Hemat Energi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(to bottom, #c6f9c6, #a5e9a5);
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }
    .register-container {
      max-width: 400px;
      margin: 60px auto;
      padding: 25px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      text-align: center;
    }
    .icon-group {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 10px;
    }
    .icon-group img {
      height: 45px;
    }
    .title {
      font-size: 24px;
      font-weight: bold;
      color: #2e4d24;
      margin-bottom: 20px;
    }
    .form-control {
      border: 2px solid #2e7d32;
      border-radius: 12px;
      padding: 10px 12px 10px 40px;
      margin-bottom: 15px;
    }
    .input-icon {
      position: relative;
    }
    .input-icon i {
      position: absolute;
      top: 11px;
      left: 12px;
      color: #777;
    }
    .btn-success {
      background-color: #2e7d32;
      border: none;
      width: 100%;
      border-radius: 12px;
      padding: 10px;
      font-weight: 600;
    }
    .footer {
      text-align: center;
      font-size: 13px;
      margin-top: 30px;
      color: #444;
    }
    .footer img {
      height: 28px;
      margin-bottom: 6px;
    }
    a {
      color: #2e7d32;
      text-decoration: none;
      font-weight: 500;
    }
  </style>
</head>
<body>

<!-- ✅ Icon Energi Atas -->
<div class="icon-group" style="margin-top: 1cm; text-align: center;">
  <img src="{{ asset('assets/img/ataslogin.png') }}" alt="Ikon Energi" style="width: 150px; height: auto;">
</div>

<h2 class="text-center fw-bold mb-2">Hemat Energi</h2>

<div class="register-container">
  <div class="title">Registrasi</div>

  {{-- ✅ NOTIFIKASI BERHASIL --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
      ✅ {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  @endif

  {{-- ❌ NOTIFIKASI GAGAL --}}
  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
      <strong>Terjadi kesalahan:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>❌ {{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  @endif

  <form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="input-icon">
      <i class="fas fa-user"></i>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
      @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="input-icon">
     <i class="fas fa-user-circle"></i>
    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" value="{{ old('username') }}" required>
    @error('username') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="input-icon">
      <i class="fas fa-envelope"></i>
      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
      @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="input-icon">
      <i class="fas fa-building"></i>
      <input type="text" name="unit_kerja" class="form-control @error('unit_kerja') is-invalid @enderror" placeholder="Unit Kerja" value="{{ old('unit_kerja') }}" required>
      @error('unit_kerja') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="input-icon">
      <i class="fas fa-lock"></i>
      <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
      @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="input-icon">
      <i class="fas fa-lock"></i>
      <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password" required>
    </div>

    <button type="submit" class="btn btn-success">Daftar</button>
  </form>

  <div class="mt-3">
    <span>Sudah punya akun?</span>
    <a href="{{ route('login') }}">Masuk di sini</a>
  </div>
</div>

<div class="footer">
  <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung"><br>
  ©2025 PT. Bank Lampung - Sistem Monitoring Energi
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
