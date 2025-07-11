<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Bank Lampung</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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
      --footer-bg: rgba(255, 255, 255, 0.2);
      --box-shadow-medium: 0 10px 30px rgba(0, 0, 0, 0.2);
      --box-shadow-strong: 0 15px 40px rgba(0, 0, 0, 0.3);
    }

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Montserrat', sans-serif;
      background: url('https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') no-repeat center center;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      color: var(--neutral-dark);
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
      box-shadow: var(--box-shadow-medium);
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1.2rem;
    }

    .btn-back-floating:hover {
      background: var(--primary-green);
      color: var(--neutral-white);
      box-shadow: var(--box-shadow-strong);
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
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
      margin-bottom: 40px;
    }

    .top-logo img {
      width: 140px;
      margin-bottom: 20px;
    }

    .top-logo h3 {
      font-size: 2.5rem;
      font-weight: 700;
    }

    .login-box {
      background: rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 40px;
      box-shadow: var(--box-shadow-strong);
      max-width: 420px;
      width: 100%;
      z-index: 2;
    }

    .login-box h4 {
      color: var(--neutral-white);
      margin-bottom: 30px;
      font-size: 2rem;
      font-weight: 700;
    }

    .form-group {
      position: relative;
      margin-bottom: 25px;
    }

    .form-control {
      border-radius: 12px;
      padding: 14px 18px 14px 50px;
      border: 2px solid var(--light-green);
      font-size: 1.05rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--accent-green);
      box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.35);
      outline: none;
      background: var(--neutral-light);
    }

    .form-group i {
      position: absolute;
      top: 50%;
      left: 18px;
      transform: translateY(-50%);
      color: var(--primary-green);
      font-size: 1.2rem;
    }

    .btn-login {
      background: var(--accent-yellow);
      color: var(--dark-green);
      border-radius: 12px;
      width: 100%;
      padding: 15px;
      font-weight: 700;
      font-size: 1.2rem;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      background: var(--accent-green);
      color: white;
      box-shadow: var(--box-shadow-medium);
    }

    .bottom-text {
      margin-top: 25px;
      font-size: 1rem;
      color: #FFCC00;
    }

    .bottom-text a {
      color:rgb(255, 255, 255);
      font-weight: 600;
      text-decoration: none;
    }

    .bottom-text a:hover {
      color: var(--neutral-white);
      text-decoration: underline;
    }

    .alert {
      font-size: 0.95rem;
      padding: 1rem 1.5rem;
      border-radius: 10px;
      margin-bottom: 25px;
      text-align: left;
    }

    .alert-success {
      background: var(--light-green);
      color: var(--dark-green);
    }

    .alert-danger {
      background: #ffe0b2;
      color: #d32f2f;
    }

    .footer-bank {
      background: var(--dark-green);
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      width: 100%;
      padding: 20px 0;
      text-align: center;
      box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.1);
      z-index: 1;
    }

    .footer-bank img {
      width: 180px;
    }

    .footer-bank span {
      display: block;
      margin-top: 5px;
      font-size: 0.85rem;
      color: var(--neutral-white);
    }

    @media (max-width: 768px) {
      .btn-back-floating {
        left: 15px;
        top: 15px;
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
      }

      .top-logo img {
        width: 120px;
      }

      .top-logo h3 {
        font-size: 2rem;
      }

      .login-box {
        padding: 30px;
        max-width: 360px;
      }

      .login-box h4 {
        font-size: 1.7rem;
      }

      .footer-bank img {
        width: 150px;
      }
    }

    @media (max-width: 480px) {
      .btn-back-floating {
        left: 10px;
        top: 10px;
        width: 35px;
        height: 35px;
        font-size: 1rem;
      }

      .top-logo img {
        width: 100px;
      }

      .top-logo h3 {
        font-size: 1.8rem;
      }

      .login-box {
        padding: 25px;
        max-width: 300px;
      }

      .login-box h4 {
        font-size: 1.5rem;
      }

      .footer-bank img {
        width: 120px;
      }
    }
  </style>
</head>
<body>
  <div class="btn-back-floating" onclick="window.location.href='{{ url('/') }}'">
    <i class="fas fa-arrow-left"></i>
    <span>Back</span>
  </div>

  <div class="main-content-wrapper">
    <div class="top-logo">
       <img src="{{ asset('assets/img/BLPUTIH.png') }}" alt="Bank Lampung" />
      <h3>Monitoring Energi</h3>
    </div>

    <div class="login-box">
      <h4>Login ke Sistem</h4>

      @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}">

        @csrf
        <div class="form-group">
          <i class="bi bi-person-fill"></i>
          <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="form-group">
          <i class="bi bi-lock-fill"></i>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-login">Masuk</button>
      </form>

      <div class="bottom-text">
        @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">
          <i class="bi bi-question-circle me-1"></i> Lupa password?
        </a>
        @else
        <span>Reset password tidak tersedia</span>
        @endif
      </div>
    </div>
  </div>

  <div class="footer-bank">
    <span>&copy;2025 PT. Bank Lampung - Sistem Monitoring Energi</span>
  </div>
</body>
</html>
