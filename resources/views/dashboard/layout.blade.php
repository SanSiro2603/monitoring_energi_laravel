<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hemat Energi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    
      rel="stylesheet"
      integrity="sha512-XxXYbCKWTGyEkVlKQQ5BlVMe6vBQFZ9bUu0sHoP9+ZHzY1kAmSnvcgqRvOQWxlhbU12Gv7ZbyLSAZaCFK1Hw0A=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer" />

    <style>
        :root {
            --primary-green: #2e7d32; /* Hijau Primer (Deep Green) */
            --dark-green: #1b5e20;    /* Hijau Gelap */
            --light-green: #c8e6c9;   /* Hijau Muda */
            --accent-green: #4caf50;  /* Hijau Aksen (Vibrant Green) */
            --accent-yellow: #ffc107; /* Kuning Aksen */
            --neutral-white: #ffffff;
            --neutral-light: #f5f5f5; /* Background body */
            --neutral-dark: #2c3e50;  /* Warna teks umum */
            --header-gradient-start: #a1d68b; /* Warna gradien header */
            --header-gradient-end: #c2f0c2;
            --sidebar-bg: #eaffea;    /* Background sidebar */
            --box-shadow-light: 0 2px 8px rgba(0, 0, 0, 0.1);
            --box-shadow-medium: 0 5px 15px rgba(0, 0, 0, 0.15);
            --box-shadow-strong: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Mencegah scroll pada body utama */
            font-family: 'Montserrat', sans-serif;
            background-color: var(--neutral-light); /* Latar belakang halaman */
            color: var(--neutral-dark);
        }

        .wrapper {
            display: flex;
            flex-direction: column; /* Mengatur item dalam kolom */
            min-height: 100vh; /* Pastikan wrapper mengisi seluruh tinggi viewport */
            overflow: hidden; /* Mencegah scroll pada wrapper itu sendiri */
        }

        /* HEADER */
        .header {
            background: linear-gradient(to right, var(--primary-green), var(--accent-green)); /* Gradien hijau yang lebih kuat */
            padding: 15px 30px;
            color: var(--neutral-white); /* Teks putih */
            box-shadow: var(--box-shadow-medium); /* Bayangan sedang */
            z-index: 1000;
            flex-shrink: 0; /* Pastikan header tidak mengecil */
        }
        .header img {
            height: 55px; /* Sedikit lebih besar */
            padding: 0; /* Hapus padding jika sudah proporsional */
            border-radius: 8px; /* Lebih halus */
            margin-right: 20px;
            /* background-color: rgba(255, 255, 255, 0.1); */ /* Latar belakang transparan dihapus */
            /* Jika masih ada putih, kemungkinan dari gambar itu sendiri */
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4)); /* Biarkan shadow tetap ada */
        }
        .header h4 {
            font-weight: 700;
            font-size: 1.8rem; /* Ukuran judul lebih besar */
            margin-bottom: 2px !important; /* Kurangi margin bawah */
        }
        .header small {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        .header .user-info strong {
            color: var(--accent-yellow); /* Nama user highlight kuning */
            font-weight: 600;
        }
        .header .btn-logout {
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.6);
            color: var(--neutral-white);
            padding: 6px 15px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .header .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--neutral-white);
            color: var(--neutral-white);
            transform: translateY(-1px);
        }

        /* CONTAINER UNTUK SIDEBAR DAN MAIN CONTENT */
        .content-area {
            display: flex;
            flex: 1; /* Konten area mengambil semua ruang yang tersisa */
            overflow: hidden; /* Mencegah scroll pada area ini */
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px; /* Lebar sidebar sedikit lebih besar */
            background-color: var(--sidebar-bg);
            padding-top: 20px;
            border-right: 1px solid var(--light-green); /* Border hijau muda */
            flex-shrink: 0; /* Pastikan sidebar tidak mengecil */
            overflow-y: auto; /* Memungkinkan scroll di sidebar jika menu terlalu banyak */
            height: calc(100vh - 90px); /* Tinggi sidebar menyesuaikan sisa layar */
            position: sticky; /* Sticky position agar sidebar tetap terlihat saat scroll di main-content */
            top: 0; /* Penting untuk sticky */
            align-self: flex-start; /* Penting untuk sticky dalam flex container */
        }
        .sidebar a {
            display: flex; /* Untuk ikon dan teks sejajar */
            align-items: center;
            padding: 12px 25px; /* Padding lebih banyak */
            color: var(--dark-green);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 0 30px 30px 0; /* Radius lebih besar untuk efek pil */
            margin-bottom: 8px; /* Jarak antar item menu */
            font-size: 0.95rem;
        }
        .sidebar a i, .sidebar a img {
            margin-right: 12px; /* Jarak ikon dari teks */
            font-size: 1.1rem;
            width: 20px; /* Ukuran ikon gambar */
            height: 20px;
            object-fit: contain;
        }
        .sidebar a:hover {
            background-color: var(--light-green); /* Hijau muda saat hover */
            color: var(--dark-green); /* Tetap hijau gelap */
            transform: translateX(5px); /* Efek geser sedikit */
            box-shadow: var(--box-shadow-light); /* Bayangan kecil */
        }
        .sidebar a.active {
            background-color: var(--primary-green); /* Hijau primer saat aktif */
            color: var(--neutral-white);
            font-weight: 600;
            box-shadow: var(--box-shadow-medium);
            transform: translateX(0); /* Pastikan tidak bergeser */
        }

        /* MAIN CONTENT */
        .main-content {
            flex-grow: 1; /* Mengambil semua ruang yang tersisa */
            padding: 30px; /* Padding lebih seragam */
            overflow-y: auto; /* Memungkinkan scroll di konten utama jika terlalu panjang */
            height: calc(100vh - 90px); /* Tinggi konten menyesuaikan sisa layar */
        }
        .main-content .card {
            border-radius: 15px; /* Sudut kartu lebih halus */
            border: none;
            box-shadow: var(--box-shadow-light); /* Bayangan pada kartu */
            margin-bottom: 25px; /* Jarak antar kartu */
        }
        .main-content .card h4 {
            color: var(--primary-green);
            font-weight: 700;
            font-size: 1.6rem;
        }
        .main-content .card p {
            color: var(--neutral-dark);
            font-size: 1rem;
        }

        /* FOOTER */
        .footer {
            background-color: var(--dark-green); /* Warna hijau gelap untuk footer */
            color: var(--neutral-white);
            text-align: center;
            padding: 15px;
            flex-shrink: 0; /* Pastikan footer tidak mengecil */
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.15); /* Bayangan di atas footer */
            font-size: 0.9rem;
        }

        /* MODAL LOGOUT */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: var(--box-shadow-strong);
        }
        .modal-header .modal-title {
            color: var(--primary-green);
            font-weight: 700;
        }
        .modal-footer .btn-outline-success {
            border-color: var(--accent-green);
            color: var(--accent-green);
            transition: all 0.2s ease;
        }
        .modal-footer .btn-outline-success:hover {
            background-color: var(--accent-green);
            color: var(--neutral-white);
        }
        .modal-footer .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
            transition: all 0.2s ease;
        }
        .modal-footer .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        /* RESPONSIVITAS */
        @media (max-width: 992px) { /* Untuk tablet dan layar yang lebih kecil */
            .header {
                padding: 10px 20px;
            }
            .header img {
                height: 45px;
                margin-right: 15px;
            }
            .header h4 {
                font-size: 1.5rem;
            }
            .header small {
                font-size: 0.8rem;
            }
            .sidebar {
                width: 200px; /* Lebar sidebar sedikit lebih kecil */
                padding-top: 15px;
                height: calc(100vh - 75px); /* Sesuaikan tinggi karena header lebih kecil */
            }
            .sidebar a {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            .sidebar a i, .sidebar a img {
                margin-right: 10px;
                font-size: 1rem;
                width: 18px; height: 18px;
            }
            .main-content {
                padding: 20px;
                height: calc(100vh - 75px); /* Sesuaikan tinggi */
            }
            .main-content .card {
                padding: 20px;
            }
            .main-content .card h4 {
                font-size: 1.4rem;
            }
            .footer {
                padding: 12px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 768px) { /* Untuk mobile */
            html, body {
                overflow: auto; /* Izinkan scroll pada body jika konten sidebar/main-content terlalu panjang */
            }
            .wrapper {
                flex-direction: column;
            }
            .header {
                position: relative; /* Header tidak fixed di mobile */
            }
            .content-area {
                flex-direction: column; /* Sidebar dan konten utama bertumpuk */
                overflow: visible; /* Izinkan scroll pada content-area di mobile */
            }
            .sidebar {
                width: 100%;
                height: auto; /* Tinggi otomatis */
                position: relative; /* Tidak sticky di mobile */
                border-right: none;
                border-bottom: 1px solid var(--light-green); /* Border di bawah sidebar */
                padding-bottom: 10px;
                margin-bottom: 15px; /* Jarak dari main content */
                overflow-y: visible; /* Matikan scroll internal sidebar */
            }
            .sidebar a {
                border-radius: 10px; /* Radius untuk tombol penuh */
                margin: 0 10px 8px 10px; /* Margin horizontal */
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
                height: auto; /* Tinggi otomatis */
                overflow-y: visible; /* Matikan scroll internal main-content */
            }
            .footer {
                position: relative; /* Footer tidak fixed di mobile */
            }
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <div class="header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/BLPUTIH.png') }}" alt="Bank Lampung">
                <div>
                    <h4 class="mb-0">Hemat Energi</h4>
                    <small>Pencatatan Pemakaian Air, Listrik, Kertas dan BBM</small>
                </div>
            </div>
            <div class="user-info d-flex align-items-center">
                @auth
                    <span class="d-none d-md-inline">👤 <strong>{{ Auth::user()->name }}</strong> |</span>
                    <button class="btn btn-logout ms-md-2" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt d-md-none"></i>
                        <span class="d-none d-md-inline">Logout</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>
                @endauth
            </div>
        </div>

        <div class="content-area">
            @auth
            <div class="sidebar">
                <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>

                {{-- Menu Super User --}}
                @if(Auth::user()->role === 'super_user')
                    <a href="/admin/energi/create" class="{{ request()->is('admin/energi/create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> Input Data Energi
                    </a>
                    <a href="/admin/energi" class="{{ request()->is('admin/energi') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i> Kelola Data
                    </a>
                    <a href="/admin/laporan" class="{{ request()->is('admin/laporan') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                    <a href="/admin/users" class="{{ request()->is('admin/users') ? 'active' : '' }}">
                        <i class="fas fa-address-card"></i> Kelola Users
                    </a>
                    <a href="{{ route('profil.index') }}" class="{{ request()->is('profil*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i> Profil Saya
                    </a>
                @endif

                {{-- Menu Divisi --}}
                @if(Auth::user()->role === 'divisi_user')
                    <a href="/divisi/energi/create" class="{{ request()->is('divisi/energi/create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> Input Data Energi
                    </a>
                    <a href="/divisi/laporan" class="{{ request()->is('divisi/laporan') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                    <a href="/divisi/users" class="{{ request()->is('divisi/users') ? 'active' : '' }}">
                        <i class="fas fa-address-card"></i> Kelola Users
                    </a>
                    <a href="{{ route('profil.index') }}" class="{{ request()->is('profil*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i> Profil Saya
                    </a>
                @endif

                {{-- Menu User Umum --}}
                @if(Auth::user()->role === 'user_umum')
                <a href="/umum/energi/create" class="{{ request()->is('umum/energi/create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i> Input Data Energi
                </a>
                <a href="/umum/laporan" class="{{ request()->is('umum/laporan') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
                    <a href="{{ route('profil.index') }}" class="{{ request()->is('profil*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i> Profil Saya
                    </a>
                @endif
            </div>
            @endauth

            <div class="main-content">
                @auth
                    @if(request()->is('dashboard'))
                    <div class="card shadow-sm p-4 mb-4">
                        <h4>Selamat Datang, {{ Auth::user()->name }}! 👋</h4>
                        <p class="mb-0">Anda masuk ke sistem pemantauan penggunaan energi pada kantor Bank Lampung.</p>
                    </div>
                    @endif
                @endauth

                {{-- Konten dari Blade --}}
                @yield('content')
            </div>
        </div>
    </div>

    <div class="footer">
        Hak Cipta ©2025 PT. Bank Lampung
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>