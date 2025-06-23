<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Hemat Energi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f2fff2;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    /* HEADER */
    .header {
      background: linear-gradient(to right, #c2f0c2, #a1d68b);
      padding: 15px 30px;
      color: #2e4d24;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .header img {
      height: 50px;
    
      padding: 4px;
      border-radius: 5px;
      margin-right: 15px;
    }

    /* SIDEBAR */
    .sidebar {
      position: fixed;
      top: 90px;
      left: 0;
      width: 230px;
      height: calc(100% - 90px);
      background-color: #eaffea;
      padding-top: 20px;
      border-right: 1px solid #cce5cc;
    }

    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: #2e4d24;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.2s;
      border-radius: 0 20px 20px 0;
      margin-bottom: 5px;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #a1d68b;
      color: white;
    }

    /* CONTENT */
    .main-content {
      margin-left: 240px;
      padding: 120px 30px 30px 30px;
    }

    .footer {
      background-color: #2e4d24;
      color: white;
      text-align: center;
      padding: 15px;
      margin-left: 240px;
      margin-top: 40px;
    }

    /* Responsive (optional) */
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        border-right: none;
      }

      .main-content, .footer {
        margin-left: 0;
        padding: 100px 15px 15px;
      }
    }
  </style>
</head>
<body>

  <!-- HEADER -->
  <div class="header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <img src="{{ asset('assets/img/banklpg.png') }}" alt="Bank Lampung">
      <div>
        <h4 class="mb-0 fw-bold">Hemat Energi</h4>
        <small>Pencatatan Pemakaian Air, Listrik, Kertas dan BBM</small>
      </div>
    </div>
    <div>
      👤 <strong>{{ Auth::user()->name }}</strong>
      | <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf
          <button class="btn btn-sm btn-light text-danger">Logout</button>
        </form>
    </div>
  </div>

  <!-- SIDEBAR -->
  <div class="sidebar">
   <a href="{{ url('/dashboard') }}">🏠 Dashboard</a>

    {{-- Menu Super User --}}
    @if(Auth::user()->role === 'super_user')
      <a href="/admin/energi/create">➕ Input Data Energi</a>
      <a href="/admin/energi">📋 Kelola Data</a>
      <a href="/admin/laporan">📊 Laporan</a>
      <a href="{{ route('profil.index') }}">👤 Profil Saya</a>

    @endif

    {{-- Menu Divisi --}}
    @if(Auth::user()->role === 'divisi_user')
      <a href="/divisi/energi/create">➕ Input Data Energi</a>
      <a href="/divisi/energi">📋 Kelola Data</a>
      <a href="{{ route('profil.index') }}">👤 Profil Saya</a>
    @endif

    {{-- Menu User Umum --}}
    @if(Auth::user()->role === 'user_umum')
      <a href="/umum/summary">📄 Lihat Summary</a>
    @endif
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="card shadow p-4 mb-4">
      <h4>Selamat Datang, {{ Auth::user()->name }}! 👋</h4>
      <p class="mb-0">Anda masuk ke sistem pemantauan penggunaan energi pada kantor Bank Lampung.</p>
    </div>

    {{-- Konten dari Blade --}}
    @yield('content')
  </div>

  <!-- FOOTER -->
  <div class="footer">
    Hak Cipta ©2025 PT. Bank Lampung
  </div>

@stack('scripts')
</body>
</html>
