<!DOCTYPE html>
<html>
<head>
    <title>Laporan Konsumsi Energi Bank Lampung</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
            margin: 20px;
            color: #333;
        }

        /* HEADER SECTION */
        .header-container {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .header-column {
            display: table-cell;
            vertical-align: middle;
        }

        .header-column.left {
            width: 60%;
            text-align: left;
        }

        .header-column.right {
            width: 40%;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        .header-column.left img {
            max-width: 120px;
            height: auto;
            display: block;
            margin-bottom: 5px;
        }

        .header-column.left .sub-title {
            font-size: 14px;
            font-weight: bold;
            color: #2e7d32;
            margin: 0;
            padding: 0;
        }

        .header-column.left .tagline {
            font-size: 10px;
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
            color: #2e7d32;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        /* FILTER INFO */
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

        /* TABLE STYLING */
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
            {{-- Perbaikan pada bagian ini --}}
            @if(file_exists(public_path('assets/img/banklpg.png')))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/banklpg.png'))) }}" alt="Bank Lampung">
            @else
                <div style="height: 50px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; width: 120px;">
                    Logo tidak ditemukan
                </div>
            @endif
            <p class="sub-title">Laporan Konsumsi Energi</p>
            <p class="tagline">Pencatatan Pemakaian Air, Listrik, Kertas dan BBM</p>
        </div>
        <div class="header-column right">
            Tanggal Cetak: {{ date('d-m-Y H:i:s') }}
        </div>
    </div>

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
                <th>Air (m&sup3;)</th>
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

    <div class="footer">
        Laporan ini dibuat secara otomatis oleh Sistem Monitoring Konsumsi Energi Bank Lampung.
    </div>
</body>
</html>