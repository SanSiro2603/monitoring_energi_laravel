<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - Hemat Energi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('{{ asset('assets/img/rumpu.png') }}') no-repeat center center;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .reset-card {
      background: white;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      max-width: 420px;
      width: 100%;
      text-align: center;
    }

    .reset-card h4 {
      font-weight: bold;
      color: #2e7d32;
      margin-bottom: 25px;
    }

    .form-control {
      border-radius: 12px;
      border: 2px solid #2e7d32;
      margin-bottom: 20px;
      padding: 12px;
      font-size: 16px;
    }

    .btn-success {
      background-color: #4CAF50;
      border: none;
      border-radius: 12px;
      padding: 10px;
      font-weight: 600;
      font-size: 16px;
      width: 100%;
    }

    .logo-icons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 20px;
    }

    .logo-icons img {
      width: 40px;
      height: 40px;
    }

    .footer {
      margin-top: 30px;
      text-align: center;
    }

    .footer img {
      height: 30px;
    }

    a {
      font-size: 14px;
      text-decoration: none;
      display: inline-block;
      margin-top: 12px;
      color: #2e7d32;
    }
  </style>
</head>
<body>

  <div class="logo-icons">
    <img src="{{ asset('assets/img/ataslogin.png') }}" alt="Energi">
  </div>
  <h3><strong>Hemat Energi</strong></h3>

  <div class="reset-card">
    <h4>Reset Password</h4>

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <input type="email" name="email" class="form-control" placeholder="Email" required>
      <input type="password" name="password" class="form-control" placeholder="Password Baru" required>
      <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password" required>

      <button type="submit" class="btn btn-success">Reset Password</button>
    </form>

    <a href="{{ route('login') }}">Kembali ke Login</a>
  </div>

  <div class="footer mt-4">
    <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung"><br>
    <small>Hak Cipta ©2025 PT. Bank Lampung</small>
  </div>

</body>
</html>
