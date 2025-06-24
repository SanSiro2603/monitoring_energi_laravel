@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📊 Ringkasan Penggunaan Energi</h4>

    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Kantor</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Listrik</th>
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
                    <td>{{ $row->air }}</td>
                    <td>{{ $row->bbm }}</td>
                    <td>{{ $row->jenis_bbm ?? '-' }}</td>
                    <td>{{ $row->kertas }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
