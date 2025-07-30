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
            @if($role === 'super_user')
                <!-- Form Tambah Kantor - Hanya untuk Super User -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-primary">➕ Tambah Kantor Baru</h6>
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="inputTambahKantor" class="form-label">Nama Kantor Baru</label>
                                <input type="text" 
                                       id="inputTambahKantor" 
                                       class="form-control" 
                                       placeholder="Masukkan nama kantor baru..."
                                       autocomplete="off">
                                <div class="form-text">Format: Huruf kapital hanya di awal kata</div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" onclick="tambahKantor()">
                                    ➕ Tambah Kantor
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Search/Pilih Kantor - Untuk Semua User -->
            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="text-success">🔍 {{ $role === 'super_user' ? 'Cari Kantor yang Sudah Ada' : 'Pilih Kantor' }}</h6>
                    <div class="col-md-8">
                        <label for="namaKantor" class="form-label">{{ $role === 'super_user' ? 'Cari Kantor' : 'Pilih Kantor' }}</label>
                        <div class="dropdown-search-container">
                            <input type="text" 
                                   id="namaKantor" 
                                   class="form-control kantor-search-input" 
                                   placeholder="{{ $role === 'super_user' ? 'Ketik untuk mencari kantor yang sudah ada...' : 'Ketik untuk mencari dan memilih kantor...' }}"
                                   autocomplete="off">
                            <div class="dropdown-list" id="kantorDropdownList" style="display: none;"></div>
                        </div>
                        <div class="form-text">{{ $role === 'super_user' ? 'Pilih dari kantor yang sudah ditambahkan' : 'Cari dan pilih kantor dari daftar yang tersedia' }}</div>
                    </div>
                </div>
            </div>

            <!-- Daftar Kantor -->
            <div class="row">
                <div class="col-12">
                    <label class="form-label">Daftar Kantor:</label>
                    <div id="daftarKantor" class="border rounded p-2 bg-light kantor-list-container" style="min-height: 100px;">
                        <!-- Kantor yang sudah ada akan muncul di sini -->
                    </div>
                    @if($role !== 'super_user')
                        <div class="form-text text-muted mt-2">
                            <small>💡 Anda hanya dapat melihat dan memilih kantor. Untuk menambah kantor baru, hubungi Super User.</small>
                        </div>
                    @endif
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
let filteredKantorArray = [];
let rowCounter = 0;

// Daftar kantor default/template - kosong di awal
const kantorTemplate = [];

// Mapping BBM untuk kemudahan
const bbmData = {
    'pertalite': { name: 'PERTALITE', unit: 'L' },
    'pertamax': { name: 'PERTAMAX', unit: 'L' },
    'solar': { name: 'SOLAR', unit: 'L' },
    'dexlite': { name: 'DEXLITE', unit: 'L' },
    'pertamina_dex': { name: 'PERTAMINA DEX', unit: 'L' }
};

// Fungsi untuk inisialisasi dropdown search pada input kantor
function initializeKantorSearch() {
    const inputKantor = document.getElementById('namaKantor');
    const inputTambahKantor = document.getElementById('inputTambahKantor');
    const dropdownList = document.getElementById('kantorDropdownList');
    
    // Event listener untuk search input
    inputKantor.addEventListener('focus', function() {
        showKantorDropdown();
    });
    
    inputKantor.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterKantorOptions(searchTerm);
    });
    
    inputKantor.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdownList.style.display = 'none';
        }
    });
    
    // Event listener untuk tambah kantor input (hanya jika ada - super_user)
    if (inputTambahKantor) {
        inputTambahKantor.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                tambahKantor();
            }
        });
        
        // Real-time validation untuk input tambah kantor
        inputTambahKantor.addEventListener('input', function() {
            const namaKantor = this.value;
            const validationErrors = validateKantorName(namaKantor);
            
            // Remove existing validation feedback
            const existingFeedback = this.parentNode.querySelector('.validation-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            if (namaKantor.trim() !== '' && validationErrors.length > 0) {
                this.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'validation-feedback invalid-feedback';
                feedback.innerHTML = validationErrors.join('<br>');
                this.parentNode.appendChild(feedback);
            } else {
                this.classList.remove('is-invalid');
                if (namaKantor.trim() !== '') {
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-search-container')) {
            dropdownList.style.display = 'none';
        }
    });
}

// Fungsi untuk menampilkan dropdown kantor
function showKantorDropdown() {
    const inputKantor = document.getElementById('namaKantor');
    const dropdownList = document.getElementById('kantorDropdownList');
    const searchTerm = inputKantor.value.toLowerCase();
    
    filterKantorOptions(searchTerm);
}

// Fungsi untuk filter opsi kantor berdasarkan pencarian
function filterKantorOptions(searchTerm) {
    const dropdownList = document.getElementById('kantorDropdownList');
    
    // Hanya tampilkan kantor yang sudah ditambahkan jika ada pencarian
    filteredKantorArray = daftarKantorArray.filter(kantor => 
        kantor.toLowerCase().includes(searchTerm.toLowerCase())
    );
    
    // Clear dropdown
    dropdownList.innerHTML = '';
    
    if (filteredKantorArray.length === 0) {
        if (searchTerm.trim() === '') {
            if (daftarKantorArray.length === 0) {
                dropdownList.innerHTML = '<div class="dropdown-list-item text-muted">Belum ada kantor. Ketik nama kantor baru...</div>';
            } else {
                dropdownList.innerHTML = '<div class="dropdown-list-item text-muted">Pilih kantor yang sudah ada atau ketik nama baru...</div>';
            }
        } else {
            dropdownList.innerHTML = '<div class="dropdown-list-item text-muted">Tidak ada kantor yang cocok. Ketik nama baru...</div>';
        }
    } else {
        // Tampilkan maksimal 10 hasil
        const displayKantor = filteredKantorArray.slice(0, 10);
        
        displayKantor.forEach(kantor => {
            const item = document.createElement('div');
            item.className = 'dropdown-list-item';
            
            // Highlight text yang cocok dengan pencarian
            if (searchTerm.trim() !== '') {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                item.innerHTML = kantor.replace(regex, '<strong>$1</strong>');
            } else {
                item.textContent = kantor;
            }
            
            // Tambahkan indikator bahwa kantor sudah ada
            item.innerHTML += ' <span class="badge bg-success ms-1">✓</span>';
            item.classList.add('already-added');
            
            item.onclick = function() {
                document.getElementById('namaKantor').value = kantor;
                dropdownList.style.display = 'none';
            };
            
            dropdownList.appendChild(item);
        });
        
        // Tampilkan info jika ada lebih dari 10 hasil
        if (filteredKantorArray.length > 10) {
            const moreInfo = document.createElement('div');
            moreInfo.className = 'dropdown-list-item text-muted text-center';
            moreInfo.innerHTML = `<small>Dan ${filteredKantorArray.length - 10} kantor lainnya...</small>`;
            dropdownList.appendChild(moreInfo);
        }
    }
    
    dropdownList.style.display = 'block';
}

// Fungsi untuk validasi format nama kantor
function validateKantorName(namaKantor) {
    const errors = [];
    
    // Cek tidak boleh kosong
    if (!namaKantor || namaKantor.trim() === '') {
        errors.push('Nama kantor tidak boleh kosong');
        return errors;
    }
    
    // Cek huruf kapital di tengah kata (sebelum spasi)
    const words = namaKantor.trim().split(' ');
    for (let i = 0; i < words.length; i++) {
        const word = words[i];
        if (word.length > 1) {
            // Cek apakah ada huruf kapital di tengah kata (posisi 1 sampai word.length-1)
            for (let j = 1; j < word.length; j++) {
                if (/[A-Z]/.test(word[j])) {
                    errors.push(`Kata "${word}" tidak boleh ada huruf kapital di tengah kata`);
                    break;
                }
            }
        }
    }
    
    return errors;
}

// Fungsi untuk format nama kantor yang benar
function formatKantorName(namaKantor) {
    return namaKantor.trim()
        .split(' ')
        .filter(word => word.length > 0) // Hapus spasi berlebih
        .map(word => {
            // Kapitalisasi huruf pertama, lowercase sisanya
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        })
        .join(' ');
}

// Fungsi untuk menambah kantor - hanya untuk super_user
function tambahKantor() {
    // Cek apakah user adalah super_user
    const userRole = '{{ Auth::user()->role }}';
    if (userRole !== 'super_user') {
        showErrorMessage('Anda tidak memiliki akses untuk menambah kantor baru!');
        return;
    }
    
    const inputKantor = document.getElementById('inputTambahKantor');
    if (!inputKantor) {
        showErrorMessage('Form tambah kantor tidak tersedia!');
        return;
    }
    
    const namaKantor = inputKantor.value.trim();
    
    // Validasi format nama kantor
    const validationErrors = validateKantorName(namaKantor);
    if (validationErrors.length > 0) {
        showErrorMessage(validationErrors.join('<br>'));
        return;
    }
    
    // Format nama kantor yang benar
    const formattedName = formatKantorName(namaKantor);
    
    // Cek apakah kantor sudah ada
    if (daftarKantorArray.includes(formattedName)) {
        showErrorMessage(`Kantor "${formattedName}" sudah ada dalam daftar!`);
        return;
    }
    
    // Tambah ke array
    daftarKantorArray.push(formattedName);
    
    // Update tampilan daftar kantor
    updateDaftarKantor();
    
    // Update semua dropdown kantor di tabel
    updateKantorDropdowns();
    
    // Kosongkan input
    inputKantor.value = '';
    
    // Simpan ke localStorage untuk persistensi
    localStorage.setItem('daftarKantor', JSON.stringify(daftarKantorArray));
    
    // TODO: Simpan juga ke database/server agar data terpusat
    // Idealnya data kantor disimpan di database dan diakses via API
    saveKantorToServer(formattedName);
    
    // Show success message
    showSuccessMessage(`Kantor "${formattedName}" berhasil ditambahkan!`);
}

// Fungsi untuk menyimpan kantor ke server (placeholder)
function saveKantorToServer(namaKantor) {
    // TODO: Implementasikan AJAX call ke server untuk menyimpan kantor
    // fetch('/api/kantor', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json',
    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //     },
    //     body: JSON.stringify({ nama_kantor: namaKantor })
    // });
}

// Fungsi untuk load kantor dari server (placeholder)
function loadKantorFromServer() {
    // TODO: Implementasikan AJAX call untuk mengambil data kantor dari server
    // fetch('/api/kantor')
    //     .then(response => response.json())
    //     .then(data => {
    //         daftarKantorArray = data.kantor || [];
    //         updateDaftarKantor();
    //         updateKantorDropdowns();
    //     });
}

// Fungsi untuk menampilkan pesan error
function showErrorMessage(message) {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <strong>❌ Error!</strong><br>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

// Fungsi untuk menampilkan pesan sukses
function showSuccessMessage(message) {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Fungsi untuk update tampilan daftar kantor
function updateDaftarKantor() {
    const container = document.getElementById('daftarKantor');
    
    if (daftarKantorArray.length === 0) {
        container.innerHTML = '<em class="text-muted">Belum ada kantor yang ditambahkan.</em>';
        return;
    }
    
    let html = '<div class="kantor-badges">';
    daftarKantorArray.forEach((kantor, index) => {
        html += `
            <span class="badge bg-primary me-2 mb-2 d-inline-flex align-items-center kantor-badge">
                <span class="kantor-name">${kantor}</span>
                <button type="button" class="btn-close btn-close-white ms-2" 
                        onclick="hapusKantor(${index})" 
                        style="font-size: 0.7em;"
                        title="Hapus kantor"></button>
            </span>
        `;
    });
    html += '</div>';
    
    // Tambahkan info total
    html += `<div class="mt-2"><small class="text-muted">Total: ${daftarKantorArray.length} kantor</small></div>`;
    
    container.innerHTML = html;
}

// Fungsi untuk menghapus kantor
function hapusKantor(index) {
    const namaKantor = daftarKantorArray[index];
    if (confirm(`Apakah Anda yakin ingin menghapus kantor "${namaKantor}"?`)) {
        daftarKantorArray.splice(index, 1);
        updateDaftarKantor();
        updateKantorDropdowns();
        
        // Update localStorage
        localStorage.setItem('daftarKantor', JSON.stringify(daftarKantorArray));
        
        showSuccessMessage(`Kantor "${namaKantor}" berhasil dihapus!`);
    }
}

// Fungsi untuk update semua dropdown kantor di tabel
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

// Event listener saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Load data kantor dari localStorage
    const savedKantor = localStorage.getItem('daftarKantor');
    if (savedKantor) {
        daftarKantorArray = JSON.parse(savedKantor);
        updateDaftarKantor();
        updateKantorDropdowns();
    }
    
    // Initialize search dropdown untuk input kantor
    initializeKantorSearch();
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

/* Dropdown Search Styles untuk Input Kantor */
.dropdown-search-container {
    position: relative;
}

.dropdown-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 8px 8px;
    max-height: 250px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-top: -1px;
}

.dropdown-list-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f1f1f1;
    font-size: 14px;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dropdown-list-item:hover {
    background-color: #f8f9fa;
    border-left: 3px solid #007bff;
    padding-left: 12px;
}

.dropdown-list-item:last-child {
    border-bottom: none;
    border-radius: 0 0 8px 8px;
}

.dropdown-list-item.text-muted {
    cursor: default;
    font-style: italic;
    justify-content: center;
}

.dropdown-list-item.text-muted:hover {
    background-color: transparent;
    border-left: none;
    padding-left: 15px;
}

.dropdown-list-item.already-added {
    background-color: #f8f9fa;
    color: #6c757d;
}

.dropdown-list-item strong {
    color: #007bff;
    font-weight: 600;
}

/* Styling untuk daftar kantor */
.kantor-list-container {
    max-height: 200px;
    overflow-y: auto;
}

.kantor-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.kantor-badge {
    font-size: 0.9em;
    padding: 8px 12px;
    transition: all 0.2s ease;
    cursor: default;
}

.kantor-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kantor-name {
    margin-right: 8px;
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
    
    .dropdown-list {
        max-height: 200px;
        font-size: 13px;
    }
    
    .dropdown-list-item {
        padding: 8px 12px;
        font-size: 13px;
    }
    
    .kantor-badge {
        font-size: 0.8em;
        padding: 6px 10px;
    }
}

/* Custom scrollbar untuk dropdown list */
.dropdown-list::-webkit-scrollbar {
    width: 6px;
}

.dropdown-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.dropdown-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.dropdown-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
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

/* Custom scrollbar untuk kantor list */
.kantor-list-container::-webkit-scrollbar {
    width: 6px;
}

.kantor-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.kantor-list-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.kantor-list-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation untuk toast notification */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.alert.position-fixed {
    animation: slideInRight 0.3s ease-out;
}

/* Validation Styles */
.validation-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-valid {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-valid:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}
</style>
@endsection