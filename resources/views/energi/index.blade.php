@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📋 Daftar Data Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-success">
            <tr>
                <th>No</th>
                <th>Kantor</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Listrik</th>
                <th>Air</th>
                <th>BBM</th>
                <th>Jenis BBM</th> {{-- tambahkan kolom ini --}}
                <th>Kertas</th>
                <th>Aksi</th>
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
                <td>{{ $row->jenis_bbm ?? '-' }}</td> {{-- tampilkan jenis BBM --}}
                <td>{{ $row->kertas }}</td>
                <td>
                    @if(Auth::user()->role === 'super_user')
                        <a href="/admin/energi/{{ $row->id }}/edit" class="btn btn-sm btn-warning">Edit</a>
                    @endif
                    <form action="{{ (Auth::user()->role === 'super_user') ? '/admin/energi/'.$row->id : '/divisi/energi/'.$row->id }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Yakin?')" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
