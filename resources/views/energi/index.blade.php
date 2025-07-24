@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📋 Daftar Data Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ url()->current() }}" class="row g-2 mb-3">
        <div class="col-md-2">
            <input type="text" name="cari_kantor" class="form-control" placeholder="Cari Kantor" value="{{ request('cari_kantor') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="cari_bulan" class="form-control" placeholder="Cari Bulan" value="{{ request('cari_bulan') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="cari_tahun" class="form-control" placeholder="Cari Tahun" value="{{ request('cari_tahun') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="cari_email" class="form-control" placeholder="Cari Email" value="{{ request('cari_email') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="cari_nama" class="form-control" placeholder="Cari Nama" value="{{ request('cari_nama') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success btn-sm">🔍 Cari</button>
            <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">🔄 Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Timestamp</th>
                    <th>Email Address</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th>Kantor</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>PERTALITE </th>
                    <th>PERTAMAX </th>
                    <th>SOLAR </th>
                    <th>DEXLITE </th>
                    <th>PERTAMINA DEX </th>
                    <th>Listrik /th>
                    <th>Daya Listrik </th>
                    <th>Air </th>
                    <th>Kertas </th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                    <td>
                        <small>
                            {{ $row->created_at->format('d/m/Y') }}<br>
                            {{ $row->created_at->format('H:i:s') }}
                        </small>
                    </td>
                    <td>{{ $row->user->email ?? '-' }}</td>
                    <td>
                        @if($row->user)
                            {{ $row->user->name }}
                        @else
                            <span class="text-danger">User dihapus</span>
                        @endif
                    </td>
                    <td>{{ $row->user->jabatan ?? '-' }}</td>
                    <td>{{ $row->kantor }}</td>
                    <td>{{ $row->bulan }}</td>
                    <td>{{ $row->tahun }}</td>
                    <td>{{ $row->pertalite ?? '0' }}</td>
                    <td>{{ $row->pertamax ?? '0' }}</td>
                    <td>{{ $row->solar ?? '0' }}</td>
                    <td>{{ $row->dexlite ?? '0' }}</td>
                    <td>{{ $row->pertamina_dex ?? '0' }}</td>
                    <td>{{ $row->listrik }}</td>
                    <td>{{ $row->daya_listrik ?? '-' }}</td>
                    <td>{{ $row->air }}</td>
                    <td>{{ $row->kertas }}</td>
                    <td>
                        @if(Auth::user()->role === 'super_user')
                            <a href="/admin/energi/{{ $row->id }}/edit" class="btn btn-sm btn-warning mb-1">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endif
                        <form action="{{ (Auth::user()->role === 'super_user') ? '/admin/energi/'.$row->id : '/divisi/energi/'.$row->id }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Yakin ingin menghapus data ini?')" class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $data->withQueryString()->links() }}
    </div>

    <!-- Summary Info -->
    <div class="mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">📊 Ringkasan Data</h6>
                        <p class="card-text">
                            Total Data: <strong>{{ $data->total() }}</strong><br>
                            Halaman: <strong>{{ $data->currentPage() }}</strong> dari <strong>{{ $data->lastPage() }}</strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">ℹ️ Keterangan</h6>
                        <p class="card-text">
                            <small>
                                • Timestamp: Waktu input data<br>
                                • BBM dalam satuan Liter (L)<br>
                                • Listrik dalam satuan kWh<br>
                                • Air dalam satuan m³
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
}

.table th, .table td {
    white-space: nowrap;
    vertical-align: middle;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Styling untuk timestamp */
.table td small {
    color: #6c757d;
    font-size: 0.75rem;
}
</style>
@endsection
