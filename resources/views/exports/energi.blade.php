<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead style="background-color: #d4edda;">
        <tr>
            <th>No</th>
            <th>Kantor</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Listrik</th>
            <th>Daya Listrik</th> 
            <th>Air</th>
            <th>BBM</th>
            <th>Jenis BBM</th> 
            <th>Kertas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $row->kantor }}</td>
            <td>{{ $row->bulan }}</td>
            <td>{{ $row->tahun }}</td>
            <td>{{ $row->listrik }}</td>
            <td>{{ $row->daya_listrik ?? '-' }}</td> {{-- ✅ Tambahkan ini --}}
            <td>{{ $row->air }}</td>
            <td>{{ $row->bbm }}</td>
            <td>{{ $row->jenis_bbm ?? '-' }}</td>
            <td>{{ $row->kertas }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
