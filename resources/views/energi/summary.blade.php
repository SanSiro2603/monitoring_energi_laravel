@extends('dashboard.layout')

@section('content')
<div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
    <div style="overflow-x: auto;">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
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
                    <td>{{ $row->daya_listrik ?? '-' }}</td> {{-- Tambahan --}}
                    <td>{{ $row->air }}</td>
                    <td>{{ $row->bbm }}</td>
                    <td>{{ $row->jenis_bbm ?? '-' }}</td>
                    <td>{{ $row->kertas }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="fw-bold">
                <tr>
                    <td colspan="4">Total</td>
                    <td>{{ $total['listrik'] ?? 0 }}</td>
                    <td>{{ $total['daya_listrik'] ?? 0 }}</td> {{-- Tambahan --}}
                    <td>{{ $total['air'] ?? 0 }}</td>
                    <td>{{ $total['bbm'] ?? 0 }}</td>
                    <td>-</td>
                    <td>{{ $total['kertas'] ?? 0 }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
