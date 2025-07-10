<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrasi - Bank Lampung</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --primary-green: #2e7d32;
      --dark-green: #1b5e20;
      --light-green: #c8e6c9;
      --accent-green: #4caf50;
      --accent-yellow: #ffc107;
      --neutral-white: #ffffff;
      --neutral-dark: #2c3e50;
      --box-shadow-strong: 0 15px 40px rgba(0, 0, 0, 0.3);
    }

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Montserrat', sans-serif;
      background: url('https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') no-repeat center center;
      background-size: cover;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(46, 125, 50, 0.85);
      z-index: 0;
    }

    .btn-back-floating {
      position: absolute;
      left: 20px;
      top: 20px;
      z-index: 10;
      background: rgba(255, 255, 255, 0.9);
      color: var(--primary-green);
      border: none;
      border-radius: 50%;
      width: 45px;
      height: 45px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1.2rem;
    }

    .btn-back-floating:hover {
      background: var(--primary-green);
      color: var(--neutral-white);
      transform: translateY(-2px);
    }

    .main-content-wrapper {
      position: relative;
      z-index: 1;
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 20px;
      text-align: center;
    }

    .top-logo {
      color: var(--neutral-white);
      margin-bottom: 20px;
    }

    .top-logo img {
      width: 120px;
      margin-bottom: 10px;
    }

    .top-logo h3 {
      font-size: 2rem;
      font-weight: 700;
    }

    .register-box {
      background: rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 20px;
      box-shadow: var(--box-shadow-strong);
      max-width: 420px;
      width: 100%;
  
    }

    .register-box h4 {
      color: var(--neutral-white);
      margin-bottom: 20px;
      font-size: 1.8rem;
      font-weight: 700;
    }

    .form-group {
      position: relative;
      margin-bottom: 15px;
    }

    .form-control {
      border-radius: 12px;
      padding: 12px 15px 12px 45px;
      border: 2px solid var(--light-green);
      font-size: 1rem;
    }

    .form-control:focus {
      border-color: var(--accent-green);
      box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.35);
      outline: none;
    }

    .form-group i {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      color: var(--primary-green);
      font-size: 1.1rem;
    }

    .btn-register {
      background: var(--accent-yellow);
      color: var(--dark-green);
      border-radius: 12px;
      width: 100%;
      padding: 12px;
      font-weight: 700;
      font-size: 1.1rem;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-register:hover {
      background: var(--accent-green);
      color: white;
    }

    .bottom-text {
      margin-top: 15px;
      font-size: 0.95rem;
      color: #FFCC00;
    }

    .bottom-text a {
      color: #ffffff;
      font-weight: 600;
      text-decoration: none;
    }

    .bottom-text a:hover {
      text-decoration: underline;
    }

    .footer-bank {
      background: var(--dark-green);
      width: 100%;
      padding: 15px 0;
      text-align: center;
      z-index: 1;
      color: var(--neutral-white);
      font-size: 0.85rem;
    }

    @media (max-width: 480px) {
      .top-logo img {
        width: 100px;
      }

      .top-logo h3 {
        font-size: 1.7rem;
      }

      .register-box {
        padding: 20px;
        max-width: 90%;
      }

      .register-box h4 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="btn-back-floating" onclick="window.location.href='{{ url('/') }}'">
    <i class="fas fa-arrow-left"></i>
  </div>

  <div class="main-content-wrapper">
    <div class="top-logo">
      <img src="{{ asset('assets/img/BLPUTIH.png') }}" alt="Bank Lampung" />
 
    </div>

    <div class="register-box">
      <h4>Registrasi Akun</h4>

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
          <i class="fas fa-user"></i>
          <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" required>
        </div>

        <div class="form-group">
          <i class="fas fa-user-circle"></i>
          <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>

        <div class="form-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="form-group">
          <i class="fas fa-building"></i>
          <input type="text" name="unit_kerja" class="form-control" placeholder="Unit Kerja" required>
        </div>

        <div class="form-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="form-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password" required>
        </div>

        <button type="submit" class="btn btn-register">Daftar</button>
      </form>

      <div class="bottom-text">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
      </div>
    </div>
  </div>

  <div class="footer-bank">
    ©2025 PT. Bank Lampung - Sistem Monitoring Energi
  </div>
</body>
</html>
