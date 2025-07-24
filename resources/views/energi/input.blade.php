@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📝 Input Data Konsumsi Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $role = Auth::user()->role;
        $prefix = $role === 'super_user' ? 'admin' : ($role === 'divisi_user' ? 'divisi' : 'umum');
    @endphp

    <!-- Section Kelola Kantor -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>🏢 Kelola Data Kantor</h5>
        </div>
        <div class="card-body">
            <!-- Form Tambah Kantor -->
            <div class="row align-items-end mb-3">
                <div class="col-md-8">
                    <label for="namaKantor" class="form-label">Nama Kantor</label>
                    <input type="text" id="namaKantor" class="form-control" placeholder="Masukkan nama kantor...">
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary" onclick="tambahKantor()">
                        ➕ Tambah Kantor
                    </button>
                </div>
            </div>

            <!-- Daftar Kantor -->
            <div class="row">
                <div class="col-12">
                    <label class="form-label">Daftar Kantor:</label>
                    <div id="daftarKantor" class="border rounded p-2 bg-light" style="min-height: 100px;">
                        <!-- Kantor yang sudah ada akan muncul di sini -->
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <th style="width: 50px;">#</th>
                            <th>Kantor</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Pilih BBM & Input</th>
                            <th>Listrik </th>
                            <th>Daya Listrik </th>
                            <th>Air </th>
                            <th>Kertas </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="data-table-body">
                        <!-- Baris akan ditambahkan secara dinamis -->
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
// Array untuk menyimpan daftar kantor
let daftarKantorArray = [];
let rowCounter = 0;

// Mapping BBM untuk kemudahan
const bbmData = {
    'pertalite': { name: 'PERTALITE', unit: 'L' },
    'pertamax': { name: 'PERTAMAX', unit: 'L' },
    'solar': { name: 'SOLAR', unit: 'L' },
    'dexlite': { name: 'DEXLITE', unit: 'L' },
    'pertamina_dex': { name: 'PERTAMINA DEX', unit: 'L' }
};

// Fungsi untuk menambah kantor
function tambahKantor() {
    const inputKantor = document.getElementById('namaKantor');
    const namaKantor = inputKantor.value.trim();
    
    if (namaKantor === '') {
        alert('Silakan masukkan nama kantor!');
        return;
    }
    
    // Cek apakah kantor sudah ada
    if (daftarKantorArray.includes(namaKantor)) {
        alert('Kantor sudah ada dalam daftar!');
        return;
    }
    
    // Tambah ke array
    daftarKantorArray.push(namaKantor);
    
    // Update tampilan daftar kantor
    updateDaftarKantor();
    
    // Update semua dropdown kantor
    updateKantorDropdowns();
    
    // Kosongkan input
    inputKantor.value = '';
    
    // Simpan ke localStorage untuk persistensi
    localStorage.setItem('daftarKantor', JSON.stringify(daftarKantorArray));
}

// Fungsi untuk update tampilan daftar kantor
function updateDaftarKantor() {
    const container = document.getElementById('daftarKantor');
    
    if (daftarKantorArray.length === 0) {
        container.innerHTML = '<em class="text-muted">Belum ada kantor yang ditambahkan.</em>';
        return;
    }
    
    let html = '';
    daftarKantorArray.forEach((kantor, index) => {
        html += `
            <span class="badge bg-primary me-2 mb-2 d-inline-flex align-items-center">
                ${kantor}
                <button type="button" class="btn-close btn-close-white ms-2" 
                        onclick="hapusKantor(${index})" style="font-size: 0.7em;"></button>
            </span>
        `;
    });
    
    container.innerHTML = html;
}

// Fungsi untuk menghapus kantor
function hapusKantor(index) {
    if (confirm('Apakah Anda yakin ingin menghapus kantor ini?')) {
        daftarKantorArray.splice(index, 1);
        updateDaftarKantor();
        updateKantorDropdowns();
        
        // Update localStorage
        localStorage.setItem('daftarKantor', JSON.stringify(daftarKantorArray));
    }
}

// Fungsi untuk update semua dropdown kantor
function updateKantorDropdowns() {
    const dropdowns = document.querySelectorAll('.kantor-dropdown');
    
    dropdowns.forEach(dropdown => {
        const currentValue = dropdown.value;
        
        // Hapus semua option kecuali yang pertama
        dropdown.innerHTML = '<option value="">-- Pilih Kantor --</option>';
        
        // Tambah option dari daftar kantor
        daftarKantorArray.forEach(kantor => {
            const option = document.createElement('option');
            option.value = kantor;
            option.textContent = kantor;
            if (kantor === currentValue) {
                option.selected = true;
            }
            dropdown.appendChild(option);
        });
    });
}

// Fungsi untuk toggle BBM input pada baris tertentu
function toggleBBMInput(rowId, bbmType, checkbox) {
    const inputContainer = document.getElementById(`bbm-input-${rowId}-${bbmType}`);
    const hiddenInput = document.getElementById(`hidden-${rowId}-${bbmType}`);
    
    if (checkbox.checked) {
        inputContainer.style.display = 'block';
        hiddenInput.disabled = false;
    } else {
        inputContainer.style.display = 'none';
        hiddenInput.disabled = true;
        hiddenInput.value = '';
    }
}

// Fungsi untuk membuat BBM section
function createBBMSection(rowId) {
    let bbmHtml = '<div class="bbm-selection-container">';
    
    Object.keys(bbmData).forEach(bbmKey => {
        const bbm = bbmData[bbmKey];
        bbmHtml += `
            <div class="bbm-item mb-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" 
                           id="check-${rowId}-${bbmKey}" 
                           onchange="toggleBBMInput(${rowId}, '${bbmKey}', this)">
                    <label class="form-check-label" for="check-${rowId}-${bbmKey}">
                        <small><strong>${bbm.name}</strong></small>
                    </label>
                </div>
                <div id="bbm-input-${rowId}-${bbmKey}" class="bbm-input-container" style="display: none;">
                    <div class="input-group input-group-sm">
                        <input type="number" 
                               id="hidden-${rowId}-${bbmKey}"
                               name="${bbmKey}[]" 
                               class="form-control form-control-sm" 
                               step="0.01" 
                               min="0" 
                               placeholder="0"
                               disabled>
                        <span class="input-group-text">${bbm.unit}</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    bbmHtml += '</div>';
    return bbmHtml;
}

// Fungsi untuk membuat baris baru
function createNewRow() {
    rowCounter++;
    const rowId = rowCounter;
    
    return `
        <td class="text-center">
            <span class="badge bg-secondary">${rowId}</span>
        </td>
        <td>
            <select name="kantor[]" class="form-select form-select-sm kantor-dropdown" required>
                <option value="">-- Pilih Kantor --</option>
            </select>
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
        <td style="min-width: 300px;">
            ${createBBMSection(rowId)}
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
}

// Fungsi toggle form
function toggleForm() {
    const formDiv = document.getElementById("formManual");
    const isVisible = formDiv.style.display !== "none";
    
    if (!isVisible) {
        // Jika form akan ditampilkan, pastikan ada minimal 1 baris
        const tbody = document.getElementById('data-table-body');
        if (tbody.children.length === 0) {
            tambahRow();
        }
    }
    
    formDiv.style.display = isVisible ? "none" : "block";
}

// Fungsi tambah row
function tambahRow() {
    const tbody = document.getElementById('data-table-body');
    const newRow = document.createElement('tr');
    newRow.innerHTML = createNewRow();
    tbody.appendChild(newRow);
    
    // Update dropdown kantor untuk row baru
    updateKantorDropdowns();
}

// Fungsi hapus row
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

// Event listener untuk Enter key pada input kantor
document.addEventListener('DOMContentLoaded', function() {
    const inputKantor = document.getElementById('namaKantor');
    
    inputKantor.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            tambahKantor();
        }
    });
    
    // Load data kantor dari localStorage
    const savedKantor = localStorage.getItem('daftarKantor');
    if (savedKantor) {
        daftarKantorArray = JSON.parse(savedKantor);
        updateDaftarKantor();
        updateKantorDropdowns();
    }
});

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

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.badge {
    font-size: 0.85em;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

.bbm-selection-container {
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: #f8f9fa;
}

.bbm-item {
    padding: 5px 0;
    border-bottom: 1px solid #e9ecef;
}

.bbm-item:last-child {
    border-bottom: none;
}

.bbm-input-container {
    margin-top: 5px;
    margin-left: 20px;
}

.form-check-inline {
    margin-bottom: 5px;
}

.input-group-sm {
    width: 120px;
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
    
    .badge {
        font-size: 0.75em;
        margin-bottom: 0.5rem;
    }
    
    .bbm-selection-container {
        max-height: 200px;
        padding: 5px;
    }
    
    .bbm-input-container {
        margin-left: 10px;
    }
    
    .input-group-sm {
        width: 100px;
    }
}

/* Custom scrollbar untuk BBM container */
.bbm-selection-container::-webkit-scrollbar {
    width: 6px;
}

.bbm-selection-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.bbm-selection-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.bbm-selection-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection