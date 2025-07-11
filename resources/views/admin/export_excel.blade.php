<!DOCTYPE html>
<html>
<head>
    <title>Laporan Konsumsi Energi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
            color: #333;
        }

        /* Menggunakan layout berbasis tabel untuk header agar kompatibel dengan Excel */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .header-table td {
            padding: 5px 0;
            vertical-align: middle;
        }
        .header-table .logo-cell {
            width: 60%;
            text-align: left;
        }
        .header-table .date-cell {
            width: 40%;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        .header-table img {
            max-width: 120px; /* Ukuran logo disesuaikan */
            height: auto;
            display: block;
            margin-bottom: 5px;
        }

        .header-table .sub-title {
            font-size: 14px;
            font-weight: bold;
            color: #2e7d32;
            margin: 0;
            padding: 0;
        }

        .header-table .tagline {
            font-size: 10px;
            color: #555;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            color: #2e7d32;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .filter-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 10px;
            background-color: #e8f5e9;
            padding: 8px 15px;
            border-radius: 4px;
            border: 1px solid #c8e6c9;
            color: #388e3c;
        }

        .filter-info span {
            font-weight: bold;
            color: #1b5e20;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-top: 15px;
        }

        th {
            background-color: #2e7d32;
            color: #ffffff;
            border: 1px solid #1b5e20;
            padding: 8px 4px;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
            vertical-align: top;
        }

        td.align-right {
            text-align: right;
            padding-right: 8px;
        }

        td.no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #888;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                {{-- Menggunakan base64 encode untuk memastikan gambar tampil di Excel --}}
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/banklpg.png'))) }}" alt="Bank Lampung">
                <p class="sub-title">Laporan Konsumsi Energi</p>
                <p class="tagline">Pencatatan Pemakaian Air, Listrik, Kertas dan BBM</p>
            </td>
            <td class="date-cell">
                Tanggal Cetak: {{ date('d-m-Y H:i:s') }}
            </td>
        </tr>
    </table>

    <h1>Laporan Konsumsi Energi Bank Lampung</h1>

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
</body>
</html>