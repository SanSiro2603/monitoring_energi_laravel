<!DOCTYPE html>
<html>
<head>
    <title>Laporan Energi</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        h4 { text-align: center; }
    </style>
</head>
<body>
    <h4>Laporan Konsumsi Energi</h4>
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
