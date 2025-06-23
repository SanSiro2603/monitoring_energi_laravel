<table>
    <thead>
        <tr>
            <th>Kantor</th><th>Bulan</th><th>Tahun</th>
            <th>Listrik</th><th>Air</th><th>BBM</th><th>Kertas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
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
