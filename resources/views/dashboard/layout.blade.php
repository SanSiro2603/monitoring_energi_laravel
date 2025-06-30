<!DOCTYPE html>
<html lang="id">
<head>
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

  <meta charset="UTF-8">
  <title>Dashboard - Hemat Energi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body { height: 100%; margin: 0; padding: 0; }
    body {
      background-color: #f2fff2;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex; flex-direction: column;
    }
    .wrapper { flex: 1; display: flex; flex-direction: column; }
    .header {
      background: linear-gradient(to right, #c2f0c2, #a1d68b);
      padding: 15px 30px; color: #2e4d24;
      position: fixed; top: 0; width: 100%;
      z-index: 1000;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .header img {
      height: 50px; padding: 4px;
      border-radius: 5px; margin-right: 15px;
    }
    .sidebar {
      position: fixed; top: 90px; left: 0;
      width: 230px; height: calc(100% - 90px);
      background-color: #eaffea; padding-top: 20px;
      border-right: 1px solid #cce5cc;
    }
    .sidebar a {
      display: block; padding: 12px 20px;
      color: #2e4d24; text-decoration: none;
      font-weight: 500; transition: background 0.2s;
      border-radius: 0 20px 20px 0;
      margin-bottom: 5px;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #a1d68b; color: white;
    }
    .main-content {
      margin-left: 240px;
      padding: 120px 30px 80px 30px;
      flex: 1;
    }
    .footer {
      background-color: #2e4d24;
      color: white; text-align: center;
      padding: 15px; width: 100%;
      position: fixed; bottom: 0; left: 0;
      z-index: 999;
    }
    @media (max-width: 768px) {
      .sidebar {
        width: 100%; height: auto;
        position: relative; border-right: none;
      }
      .main-content {
        margin-left: 0;
        padding: 100px 15px 100px;
      }
      .footer {
        position: relative; margin-left: 0;
      }
    }
  </style>
</head>
<body>

  <div class="wrapper">
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
        @auth
          👤 <strong>{{ Auth::user()->name }}</strong>
          | <button class="btn btn-sm btn-light text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
        @else
          <a href="{{ route('login') }}" class="btn btn-sm btn-outline-success">Login</a>
        @endauth
      </div>
    </div>

    <!-- SIDEBAR -->
    @auth
    <div class="sidebar">
      <a href="{{ url('/dashboard') }}">🏠 Dashboard</a>

      {{-- Menu Super User --}}
      @if(Auth::user()->role === 'super_user')
        <a href="/admin/energi/create">➕ Input Data Energi</a>
        <a href="/admin/energi">📋 Kelola Data</a>
        <a href="/admin/laporan">📊 Laporan</a>
        <a href="{{ route('profil.index') }}">👤 Profil Saya</a>
        <a href="/admin/users">👥 Kelola User</a>
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
    @endauth

    <!-- MAIN CONTENT -->
    <div class="main-content">
      @auth
        @if(request()->is('dashboard'))
        <div class="card shadow p-4 mb-4">
          <h4>Selamat Datang, {{ Auth::user()->name }}! 👋</h4>
          <p class="mb-0">Anda masuk ke sistem pemantauan penggunaan energi pada kantor Bank Lampung.</p>
        </div>
        @endif
      @endauth

      {{-- Konten dari Blade --}}
      @yield('content')
    </div>
  </div>

  <!-- ✅ FOOTER FULL WIDTH -->
  <div class="footer">
    Hak Cipta ©2025 PT. Bank Lampung
  </div>

  <!-- ✅ Modal Logout -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg" style="border-radius: 12px; max-width: 350px; margin: auto;">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold text-center w-100" id="logoutLabel">LOGOUT</h5>
        </div>
        <div class="modal-body text-center">
          Apakah Anda yakin ingin logout?
        </div>
        <div class="modal-footer justify-content-center border-0">
          <button type="button" class="btn btn-outline-success px-4" data-bs-dismiss="modal">Batal</button>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-success px-4">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  @stack('scripts')
  <!-- ✅ Bootstrap JS untuk modal -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
