<!DOCTYPE html>
<html>
<head>
    <title>Laporan Energi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 150px;
        }

        .header .tanggal {
            font-size: 12px;
            text-align: right;
        }

        h4 {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            background-color: #2e7d32; /* hijau senada */
            color: #ffffff; /* teks putih agar kontras */
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('assets/img/banklpg.png') }}" alt="Bank Lampung">
        </div>
        <div class="tanggal">
            Tanggal: {{ date('d-m-Y') }}
        </div>
    </div>

    <h4>Laporan Konsumsi Energi Bank Lampung</h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kantor</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Listrik</th>
                <th>Air</th>
                <th>BBM</th>
                <th>Kertas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->kantor }}</td>
                <td>{{ $row->bulan }}</td>
                <td>{{ $row->tahun }}</td>
                <td>{{ $row->listrik }}</td>
                <td>{{ $row->air }}</td>
                <td>{{ $row->bbm }}</td>
                <td>{{ $row->kertas }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
