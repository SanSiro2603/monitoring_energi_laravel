@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📝 Input Data Konsumsi Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $role = Auth::user()->role;
        $prefix = $role === 'super_user' ? 'admin' : ($role === 'divisi_user' ? 'divisi' : 'umum');
    @endphp

    <!-- Tombol Toggle -->
    <button class="btn btn-success mb-3" onclick="toggleForm()">➕ Tambah Data Manual</button>

    <!-- Form Manual -->
    <div id="formManual" style="display: none;">
        <form method="POST" action="{{ url("$prefix/energi") }}">
            @csrf
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="data-table-body">
                        <tr>
                            <td>
                                <input name="kantor[]" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <select name="bulan[]" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                            </td>
                            <td>
                                <input name="tahun[]" type="number" class="form-control form-control-sm" required min="2020" max="2030">
                            </td>
                            <td>
                                <input name="pertalite[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                            </td>
                            <td>
                                <input name="pertamax[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                            </td>
                            <td>
                                <input name="solar[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                            </td>
                            <td>
                                <input name="dexlite[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                            </td>
                            <td>
                                <input name="pertamina_dex[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                            </td>
                            <td>
                                <input name="listrik[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
                            </td>
                            <td>
                                <input name="daya_listrik[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="1300">
                            </td>
                            <td>
                                <input name="air[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
                            </td>
                            <td>
                                <input name="kertas[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="hapusRow(this)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tombol Tambah dan Simpan -->
            <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                <button type="submit" class="btn btn-success">💾 Simpan Data</button>
                <button type="button" class="btn btn-outline-primary" onclick="tambahRow()">
                    ➕ Tambah Baris
                </button>
            </div>
        </form>
    </div>

    <hr>

    <h5>⬆️ Import Data Energi dari Excel</h5>
    <div class="alert alert-info">
        <strong>Format Excel yang diharapkan:</strong><br>
        Kolom: Kantor | Bulan | Tahun | PERTALITE | PERTAMAX | SOLAR | DEXLITE | PERTAMINA DEX | Listrik (kWh) | Daya Listrik (VA) | Air (m³) | Kertas (rim)
    </div>
    <form method="POST" action="{{ url('/energi/import') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="fileexcel" accept=".xlsx, .xls" class="form-control mb-3" required>
        <button type="submit" class="btn btn-primary">📥 Import Excel</button>
    </form>

    <!-- Template Excel Download -->
    <div class="mt-3">
        <a href="{{ url('/energi/template') }}" class="btn btn-outline-secondary">
            📄 Download Template Excel
        </a>
    </div>
</div>

<script>
function toggleForm() {
    const formDiv = document.getElementById("formManual");
    formDiv.style.display = (formDiv.style.display === "none") ? "block" : "none";
}

function tambahRow() {
    const tbody = document.getElementById('data-table-body');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <input name="kantor[]" class="form-control form-control-sm" required>
        </td>
        <td>
            <select name="bulan[]" class="form-select form-select-sm" required>
                <option value="">-- Pilih --</option>
                <option value="Januari">Januari</option>
                <option value="Februari">Februari</option>
                <option value="Maret">Maret</option>
                <option value="April">April</option>
                <option value="Mei">Mei</option>
                <option value="Juni">Juni</option>
                <option value="Juli">Juli</option>
                <option value="Agustus">Agustus</option>
                <option value="September">September</option>
                <option value="Oktober">Oktober</option>
                <option value="November">November</option>
                <option value="Desember">Desember</option>
            </select>
        </td>
        <td>
            <input name="tahun[]" type="number" class="form-control form-control-sm" required min="2020" max="2030">
        </td>
        <td>
            <input name="pertalite[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
        </td>
        <td>
            <input name="pertamax[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
        </td>
        <td>
            <input name="solar[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
        </td>
        <td>
            <input name="dexlite[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
        </td>
        <td>
            <input name="pertamina_dex[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
        </td>
        <td>
            <input name="listrik[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
        </td>
        <td>
            <input name="daya_listrik[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="1300">
        </td>
        <td>
            <input name="air[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
        </td>
        <td>
            <input name="kertas[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="hapusRow(this)">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
}

function hapusRow(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('data-table-body');
    
    // Pastikan minimal ada 1 baris
    if (tbody.children.length > 1) {
        row.remove();
    } else {
        alert('Minimal harus ada 1 baris data!');
    }
}

// Auto-resize table pada window resize
window.addEventListener('resize', function() {
    const table = document.querySelector('.table-responsive');
    if (table) {
        table.style.overflowX = 'auto';
    }
});
</script>

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
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endsection