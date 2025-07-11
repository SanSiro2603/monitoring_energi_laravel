<!DOCTYPE html>
<html>
<head>
    <title>Laporan Konsumsi Energi Bank Lampung</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Font lebih modern */
            font-size: 10px;
            margin: 20px;
            color: #333; /* Warna teks umum */
        }

        /* HEADER SECTION */
        .header-container {
            display: table; /* Gunakan table layout untuk kompatibilitas DomPDF */
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee; /* Garis tipis pemisah */
            padding-bottom: 10px;
        }

        .header-column {
            display: table-cell;
            vertical-align: middle;
        }

        .header-column.left {
            width: 60%; /* Alokasi lebih besar untuk logo dan judul */
            text-align: left;
        }

        .header-column.right {
            width: 40%;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        .header-column.left img {
            max-width: 120px; /* Ukuran logo disesuaikan */
            height: auto;
            display: block; /* Agar tidak ada spasi di bawah gambar */
            margin-bottom: 5px;
        }

        .header-column.left .sub-title {
            font-size: 14px; /* Ukuran lebih besar untuk judul utama */
            font-weight: bold;
            color: #2e7d32; /* Warna hijau senada dengan tema monitoring */
            margin: 0;
            padding: 0;
        }

        .header-column.left .tagline {
            font-size: 10px; /* Tagline lebih kecil */
            color: #555;
            margin: 0;
            padding: 0;
        }


        /* MAIN TITLE */
        h1 {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            color: #2e7d32; /* Warna hijau */
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        /* FILTER INFO */
        .filter-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 10px;
            background-color: #e8f5e9; /* Latar belakang hijau muda */
            padding: 8px 15px;
            border-radius: 4px;
            border: 1px solid #c8e6c9;
            color: #388e3c; /* Teks hijau gelap */
        }

        .filter-info span {
            font-weight: bold;
            color: #1b5e20; /* Lebih gelap untuk label */
        }

        /* TABLE STYLING */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px; /* Ukuran font tabel sedikit lebih kecil lagi untuk kepadatan */
            margin-top: 15px;
        }

        th {
            background-color: #2e7d32; /* Hijau tua */
            color: #ffffff;
            border: 1px solid #1b5e20; /* Border sedikit lebih gelap dari background */
            padding: 8px 4px; /* Padding vertikal lebih besar, horizontal lebih kecil */
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            text-transform: uppercase; /* Uppercase untuk header */
        }

        td {
            border: 1px solid #ddd;
            padding: 6px 4px; /* Padding yang disesuaikan */
            text-align: center;
            vertical-align: top;
        }

        /* Styling spesifik untuk kolom angka agar rata kanan */
        td.align-right {
            text-align: right;
            padding-right: 8px; /* Tambahan padding di kanan untuk angka */
        }

        td.no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #888; /* Warna abu-abu yang lebih lembut */
            background-color: #f9f9f9; /* Latar belakang abu-abu muda */
        }

        /* Footer (opsional, jika ingin menambahkan) */
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="header-column left">
            {{-- Menggunakan base64 encode untuk memastikan gambar tampil di DomPDF --}}
            {{-- Pastikan path 'public_path('assets/img/banklpg.png')' benar --}}
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/banklpg.png'))) }}" alt="Bank Lampung">
            <p class="sub-title">Laporan Konsumsi Energi</p>
            <p class="tagline">Pencatatan Pemakaian Air, Listrik, Kertas dan BBM</p>
        </div>
        <div class="header-column right">
            Tanggal Cetak: {{ date('d-m-Y H:i:s') }}
        </div>
    </div>

    {{-- Hapus <h1> di sini karena sudah dipindahkan ke sub-title di header --}}

    {{-- Informasi Filter yang Aktif --}}
    <div class="filter-info">
        <span>Filter Aktif:</span>
        Kantor: <span>{{ $kantor ?: 'Semua Kantor' }}</span> |
        Bulan: <span>{{ $bulan ?: 'Semua Bulan' }}</span> |
        Tahun: <span>{{ $tahun ?: 'Semua Tahun' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kantor</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Listrik (kWh)</th>
                <th>Daya Listrik (VA)</th>
                <th>Air (m³)</th>
                <th>BBM (liter)</th>
                <th>Jenis BBM</th>
                <th>Kertas (rim)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->kantor }}</td>
                <td>{{ $row->bulan }}</td>
                <td>{{ $row->tahun }}</td>
                <td class="align-right">{{ number_format($row->listrik, 2, ',', '.') }}</td>
                <td class="align-right">{{ number_format($row->daya_listrik, 2, ',', '.') }}</td>
                <td class="align-right">{{ number_format($row->air, 2, ',', '.') }}</td>
                <td class="align-right">{{ number_format($row->bbm, 2, ',', '.') }}</td>
                <td>{{ $row->jenis_bbm ?: '-' }}</td>
                <td class="align-right">{{ number_format($row->kertas, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="no-data">Tidak ada data konsumsi energi yang ditemukan untuk filter yang dipilih.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Opsi: Menambahkan footer --}}
    <div class="footer">
        Laporan ini dibuat secara otomatis oleh Sistem Monitoring Konsumsi Energi Bank Lampung.
    </div>
</body>
</html>