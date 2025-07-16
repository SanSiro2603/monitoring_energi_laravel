@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>✏️ Edit Data Konsumsi Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $role = Auth::user()->role;
        $prefix = $role === 'super_user' ? 'admin' : ($role === 'divisi_user' ? 'divisi' : 'umum');
    @endphp

    <form method="POST" action="{{ url("$prefix/energi/{$item->id}") }}">
        @csrf 
        @method('PUT')

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Kantor</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>PERTALITE (L)</th>
                        <th>PERTAMAX (L)</th>
                        <th>SOLAR (L)</th>
                        <th>DEXLITE (L)</th>
                        <th>PERTAMINA DEX (L)</th>
                        <th>Listrik (kWh)</th>
                        <th>Daya Listrik (VA)</th>
                        <th>Air (m³)</th>
                        <th>Kertas (rim)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input name="kantor" value="{{ $item->kantor }}" class="form-control form-control-sm" required>
                        </td>
                        <td>
                            <select name="bulan" class="form-select form-select-sm" required>
                                <option value="">-- Pilih --</option>
                                <option value="Januari" {{ $item->bulan == 'Januari' ? 'selected' : '' }}>Januari</option>
                                <option value="Februari" {{ $item->bulan == 'Februari' ? 'selected' : '' }}>Februari</option>
                                <option value="Maret" {{ $item->bulan == 'Maret' ? 'selected' : '' }}>Maret</option>
                                <option value="April" {{ $item->bulan == 'April' ? 'selected' : '' }}>April</option>
                                <option value="Mei" {{ $item->bulan == 'Mei' ? 'selected' : '' }}>Mei</option>
                                <option value="Juni" {{ $item->bulan == 'Juni' ? 'selected' : '' }}>Juni</option>
                                <option value="Juli" {{ $item->bulan == 'Juli' ? 'selected' : '' }}>Juli</option>
                                <option value="Agustus" {{ $item->bulan == 'Agustus' ? 'selected' : '' }}>Agustus</option>
                                <option value="September" {{ $item->bulan == 'September' ? 'selected' : '' }}>September</option>
                                <option value="Oktober" {{ $item->bulan == 'Oktober' ? 'selected' : '' }}>Oktober</option>
                                <option value="November" {{ $item->bulan == 'November' ? 'selected' : '' }}>November</option>
                                <option value="Desember" {{ $item->bulan == 'Desember' ? 'selected' : '' }}>Desember</option>
                            </select>
                        </td>
                        <td>
                            <input name="tahun" value="{{ $item->tahun }}" type="number" class="form-control form-control-sm" required min="2020" max="2030">
                        </td>
                        <td>
                            <input name="pertalite" value="{{ $item->pertalite ?? 0 }}" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                        </td>
                        <td>
                            <input name="pertamax" value="{{ $item->pertamax ?? 0 }}" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                        </td>
                        <td>
                            <input name="solar" value="{{ $item->solar ?? 0 }}" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                        </td>
                        <td>
                            <input name="dexlite" value="{{ $item->dexlite ?? 0 }}" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                        </td>
                        <td>
                            <input name="pertamina_dex" value="{{ $item->pertamina_dex ?? 0 }}" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                        </td>
                        <td>
                            <input name="listrik" value="{{ $item->listrik }}" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
                        </td>
                        <td>
                            <input name="daya_listrik" value="{{ $item->daya_listrik ?? 1300 }}" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="1300">
                        </td>
                        <td>
                            <input name="air" value="{{ $item->air }}" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
                        </td>
                        <td>
                            <input name="kertas" value="{{ $item->kertas }}" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
            <div>
                <button type="submit" class="btn btn-success">💾 Update Data</button>
                <a href="{{ url("$prefix/energi") }}" class="btn btn-secondary">❌ Batal</a>
            </div>
        </div>
    </form>
</div>

<style>
.table-responsive {
    overflow-x: auto;
}

.table th, .table td {
    white-space: nowrap;
    vertical-align: middle;
}

.form-control-sm, .form-select-sm {
    min-width: 100px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .form-control-sm, .form-select-sm {
        min-width: 80px;
        font-size: 11px;
    }
    
    .btn {
        font-size: 0.875rem;
    }
}
</style>
@endsection