@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📋 Daftar Data Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Form Search -->
    <form method="GET" action="{{ url()->current() }}" class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="text" name="kantor" class="form-control" placeholder="Cari Kantor" value="{{ request('kantor') }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="bulan" class="form-control" placeholder="Cari Bulan" value="{{ request('bulan') }}">
        </div>
        <div class="col-md-3">
            <input type="number" name="tahun" class="form-control" placeholder="Cari Tahun" value="{{ request('tahun') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-success">🔍 Cari</button>
            <a href="{{ url()->current() }}" class="btn btn-secondary">🔄 Reset</a>
        </div>
    </form>

    <!-- Scroll wrapper -->
    <div style="overflow-x: auto; overflow-y: auto; max-height: 500px;">
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Kantor</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Listrik</th>
                    <th>Daya Listrik</th> <!-- ✅ Kolom baru -->
                    <th>Air</th>
                    <th>BBM</th>
                    <th>Kertas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->kantor }}</td>
                    <td>{{ $row->bulan }}</td>
                    <td>{{ $row->tahun }}</td>
                    <td>{{ $row->listrik }}</td>
                    <td>{{ $row->daya_listrik ?? '-' }}</td> <!-- ✅ Nilai daya listrik -->
                    <td>{{ $row->air }}</td>
                    <td>{{ $row->bbm }}</td>
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
                @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data ditemukan.</td> <!-- Ubah colspan jadi 10 -->
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
