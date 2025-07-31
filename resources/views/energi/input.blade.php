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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>🏢 Kelola Data Kantor</h5>
            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
        </div>
        <div class="card-body">
            @if($role === 'super_user')
                <!-- Form Tambah Kantor - Hanya untuk Super User -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-primary" role="alert">
                            <i class="fas fa-crown me-2"></i>
                            <strong>Super User Access:</strong> Anda dapat menambah kantor baru yang akan tersedia untuk semua user.
                        </div>
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Kantor Baru
                        </h6>
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="inputTambahKantor" class="form-label fw-bold">Nama Kantor Baru</label>
                                <input type="text" 
                                       id="inputTambahKantor" 
                                       class="form-control form-control-lg" 
                                       placeholder="Contoh: Kantor Pusat Jakarta"
                                       autocomplete="off">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Format: Huruf kapital hanya di awal kata. Contoh: "Kantor Cabang Surabaya"
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="tambahKantor()">
                                    <i class="fas fa-plus me-2"></i>Tambah Kantor
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Form Search Kantor untuk Super User -->
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-search me-2"></i>Cari Kantor yang Sudah Ada
                        </h6>
                        <div class="col-md-8">
                            <label for="namaKantor" class="form-label fw-bold">Cari Kantor</label>
                            <div class="dropdown-search-container">
                                <input type="text" 
                                       id="namaKantor" 
                                       class="form-control form-control-lg kantor-search-input" 
                                       placeholder="Ketik nama kantor untuk mencari..."
                                       autocomplete="off">
                                <div class="dropdown-list" id="kantorDropdownList" style="display: none;"></div>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-lightbulb me-1"></i>
                                Ketik untuk mencari kantor yang sudah ditambahkan sebelumnya
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Form Pilih Kantor untuk Non-Super User -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-user me-2"></i>
                            <strong>User Access:</strong> Anda dapat memilih kantor dari daftar yang tersedia. Untuk menambah kantor baru, hubungi Super User.
                        </div>
                        <h6 class="text-success mb-3">
                            <i class="fas fa-building me-2"></i>Pilih Kantor untuk Input Data
                        </h6>
                        <div class="col-md-8">
                            <label for="namaKantor" class="form-label fw-bold">Pilih Kantor</label>
                            <div class="dropdown-search-container">
                                <input type="text" 
                                       id="namaKantor" 
                                       class="form-control form-control-lg kantor-search-input" 
                                       placeholder="Ketik untuk mencari kantor..."
                                       autocomplete="off">
                                <div class="dropdown-list" id="kantorDropdownList" style="display: none;"></div>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-search me-1"></i>
                                Cari dan pilih kantor dari daftar yang tersedia. Suggestion akan muncul saat Anda mengetik.
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Kantor yang Dipilih -->
            <div class="row mb-3" id="selectedKantorSection" style="display: none;">
                <div class="col-12">
                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Kantor Dipilih:</strong> <span id="selectedKantorName"></span>
                        </div>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="clearSelectedKantor()">
                            <i class="fas fa-times me-1"></i>Ganti
                        </button>
                    </div>
                </div>
            </div>

            <!-- Daftar Kantor -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold">
                            <i class="fas fa-list me-2"></i>Daftar Kantor:
                        </label>
                        @if($role === 'super_user')
                            <small class="text-muted">
                                <i class="fas fa-edit me-1"></i>Klik x untuk menghapus kantor
                            </small>
                        @endif
                    </div>
                    <div id="daftarKantor" class="border rounded p-3 bg-light kantor-list-container" style="min-height: 120px;">
                        <!-- Kantor yang sudah ada akan muncul di sini -->
                    </div>
                    @if($role !== 'super_user')
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Anda hanya dapat melihat dan memilih kantor. Untuk menambah kantor baru, hubungi Super User.</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistik Kantor -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted mb-1">Total Kantor</h6>
                            <h4 class="text-primary mb-0" id="totalKantorCount">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted mb-1">Kantor Dipilih</h6>
                            <h4 class="text-success mb-0" id="selectedKantorCount">0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Toggle Form Manual -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-success btn-lg" onclick="toggleForm()">
            <i class="fas fa-plus-circle me-2"></i>Tambah Data Manual
        </button>
        <div class="text-muted">
            <small><i class="fas fa-info-circle me-1"></i>Pastikan sudah memilih kantor sebelum input data</small>
        </div>
    </div>

    <!-- Form Manual -->
    <div id="formManual" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-table me-2"></i>Input Data Manual</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ url("$prefix/energi") }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th><i class="fas fa-building me-1"></i>Kantor</th>
                                    <th><i class="fas fa-calendar me-1"></i>Bulan</th>
                                    <th><i class="fas fa-calendar-alt me-1"></i>Tahun</th>
                                    <th><i class="fas fa-gas-pump me-1"></i>Pilih BBM & Input</th>
                                    <th><i class="fas fa-bolt me-1"></i>Listrik (kWh)</th>
                                    <th><i class="fas fa-plug me-1"></i>Daya Listrik (VA)</th>
                                    <th><i class="fas fa-tint me-1"></i>Air (m³)</th>
                                    <th><i class="fas fa-file me-1"></i>Kertas (rim)</th>
                                    <th><i class="fas fa-cogs me-1"></i>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="data-table-body">
                                <!-- Baris akan ditambahkan secara dinamis -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Tambah dan Simpan -->
                    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Simpan Data
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-lg" onclick="tambahRow()">
                            <i class="fas fa-plus me-2"></i>Tambah Baris
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <!-- Import Excel Section -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-file-excel me-2"></i>Import Data Energi dari Excel</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Format Excel yang diharapkan:</strong><br>
                Kolom: Kantor | Bulan | Tahun | PERTALITE | PERTAMAX | SOLAR | DEXLITE | PERTAMINA DEX | Listrik (kWh) | Daya Listrik (VA) | Air (m³) | Kertas (rim)
            </div>
            <form method="POST" action="{{ url('/energi/import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="fileexcel" class="form-label fw-bold">Pilih File Excel</label>
                    <input type="file" name="fileexcel" id="fileexcel" accept=".xlsx, .xls" class="form-control form-control-lg" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-upload me-2"></i>Import Excel
                </button>
            </form>

            <!-- Template Excel Download -->
            <div class="mt-3">
                <a href="{{ url('/energi/template') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-download me-2"></i>Download Template Excel
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let daftarKantorArray = [];
let filteredKantorArray = [];
let selectedKantor = null;
let rowCounter = 0;
const userRole = '{{ Auth::user()->role }}';

// Daftar kantor default (simulasi data dari database)
const defaultKantorList = [
    'Kantor Pusat Jakarta',
    'Kantor Cabang Surabaya',
    'Kantor Cabang Bandung',
    'Kantor Cabang Medan',
    'Kantor Cabang Semarang',
    'Kantor Wilayah Barat',
    'Kantor Wilayah Timur'
];

// Mapping BBM untuk kemudahan
const bbmData = {
    'pertalite': { name: 'PERTALITE', unit: 'L' },
    'pertamax': { name: 'PERTAMAX', unit: 'L' },
    'solar': { name: 'SOLAR', unit: 'L' },
    'dexlite': { name: 'DEXLITE', unit: 'L' },
    'pertamina_dex': { name: 'PERTAMINA DEX', unit: 'L' }
};

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadInitialData();
    initializeKantorSearch();
    updateStatistics();
});

// Load data awal
function loadInitialData() {
    // Load dari localStorage atau gunakan default
    const savedKantor = localStorage.getItem('daftarKantor');
    if (savedKantor) {
        daftarKantorArray = JSON.parse(savedKantor);
    } else {
        // Gunakan data default untuk simulasi
        daftarKantorArray = [...defaultKantorList];
        saveToLocalStorage();
    }
    
    updateDaftarKantor();
    updateKantorDropdowns();
    updateStatistics();
}

// Inisialisasi pencarian kantor
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
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstOption = dropdownList.querySelector('.dropdown-list-item:not(.text-muted)');
            if (firstOption) {
                firstOption.click();
            }
        }
    });
    
    // Event listener untuk tambah kantor input (super_user only)
    if (inputTambahKantor && userRole === 'super_user') {
        inputTambahKantor.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                tambahKantor();
            }
        });
        
        inputTambahKantor.addEventListener('input', function() {
            validateKantorInput(this);
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-search-container')) {
            dropdownList.style.display = 'none';
        }
    });
}

// Validasi input kantor real-time
function validateKantorInput(input) {
    const namaKantor = input.value;
    const validationErrors = validateKantorName(namaKantor);
    
    // Remove existing validation feedback
    const existingFeedback = input.parentNode.querySelector('.validation-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    if (namaKantor.trim() !== '' && validationErrors.length > 0) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        const feedback = document.createElement('div');
        feedback.className = 'validation-feedback invalid-feedback';
        feedback.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>' + validationErrors.join('<br>');
        input.parentNode.appendChild(feedback);
    } else {
        input.classList.remove('is-invalid');
        if (namaKantor.trim() !== '') {
            input.classList.add('is-valid');
        } else {
            input.classList.remove('is-valid');
        }
    }
}

// Tampilkan dropdown kantor
function showKantorDropdown() {
    const inputKantor = document.getElementById('namaKantor');
    const searchTerm = inputKantor.value.toLowerCase();
    filterKantorOptions(searchTerm);
}

// Filter opsi kantor berdasarkan pencarian
function filterKantorOptions(searchTerm) {
    const dropdownList = document.getElementById('kantorDropdownList');
    
    filteredKantorArray = daftarKantorArray.filter(kantor => 
        kantor.toLowerCase().includes(searchTerm.toLowerCase())
    );
    
    dropdownList.innerHTML = '';
    
    if (filteredKantorArray.length === 0) {
        if (searchTerm.trim() === '') {
            dropdownList.innerHTML = `
                <div class="dropdown-list-item text-muted">
                    <i class="fas fa-search me-2"></i>
                    ${daftarKantorArray.length === 0 ? 'Belum ada kantor yang tersedia' : 'Ketik untuk mencari kantor...'}
                </div>
            `;
        } else {
            dropdownList.innerHTML = `
                <div class="dropdown-list-item text-muted">
                    <i class="fas fa-search-minus me-2"></i>
                    Tidak ada kantor yang cocok dengan "${searchTerm}"
                </div>
            `;
        }
    } else {
        const displayKantor = filteredKantorArray.slice(0, 10);
        
        displayKantor.forEach(kantor => {
            const item = document.createElement('div');
            item.className = 'dropdown-list-item kantor-option';
            
            let displayText = kantor;
            if (searchTerm.trim() !== '') {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                displayText = kantor.replace(regex, '<strong>$1</strong>');
            }
            
            item.innerHTML = `
                <div>
                    <i class="fas fa-building me-2 text-primary"></i>
                    ${displayText}
                </div>
                <span class="badge bg-success">
                    <i class="fas fa-check"></i>
                </span>
            `;
            
            item.onclick = function() {
                selectKantor(kantor);
            };
            
            dropdownList.appendChild(item);
        });
        
        if (filteredKantorArray.length > 10) {
            const moreInfo = document.createElement('div');
            moreInfo.className = 'dropdown-list-item text-muted text-center';
            moreInfo.innerHTML = `
                <small>
                    <i class="fas fa-ellipsis-h me-1"></i>
                    Dan ${filteredKantorArray.length - 10} kantor lainnya...
                </small>
            `;
            dropdownList.appendChild(moreInfo);
        }
    }
    
    dropdownList.style.display = 'block';
}

// Pilih kantor
function selectKantor(namaKantor) {
    selectedKantor = namaKantor;
    document.getElementById('namaKantor').value = namaKantor;
    document.getElementById('kantorDropdownList').style.display = 'none';
    
    // Update tampilan kantor yang dipilih
    document.getElementById('selectedKantorName').textContent = namaKantor;
    document.getElementById('selectedKantorSection').style.display = 'block';
    
    // Update dropdown di form manual
    updateKantorDropdowns();
    updateStatistics();
    
    showSuccessMessage(`Kantor "${namaKantor}" berhasil dipilih!`);
}

// Clear kantor yang dipilih
function clearSelectedKantor() {
    selectedKantor = null;
    document.getElementById('namaKantor').value = '';
    document.getElementById('selectedKantorSection').style.display = 'none';
    updateStatistics();
}

// Validasi nama kantor
function validateKantorName(namaKantor) {
    const errors = [];
    
    if (!namaKantor || namaKantor.trim() === '') {
        errors.push('Nama kantor tidak boleh kosong');
        return errors;
    }
    
    // Cek duplikasi
    const formattedName = formatKantorName(namaKantor);
    if (daftarKantorArray.includes(formattedName)) {
        errors.push(`Kantor "${formattedName}" sudah ada dalam daftar`);
    }
    
    // Cek format kata
    const words = namaKantor.trim().split(' ');
    for (let i = 0; i < words.length; i++) {
        const word = words[i];
        if (word.length > 1) {
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

// Format nama kantor
function formatKantorName(namaKantor) {
    return namaKantor.trim()
        .split(' ')
        .filter(word => word.length > 0)
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
}

// Tambah kantor baru (super_user only)
function tambahKantor() {
    if (userRole !== 'super_user') {
        showErrorMessage('Anda tidak memiliki akses untuk menambah kantor baru!');
        return;
    }
    
    const inputKantor = document.getElementById('inputTambahKantor');
    if (!inputKantor) return;
    
    const namaKantor = inputKantor.value.trim();
    const validationErrors = validateKantorName(namaKantor);
    
    if (validationErrors.length > 0) {
        showErrorMessage(validationErrors.join('<br>'));
        return;
    }
    
    const formattedName = formatKantorName(namaKantor);
    
    // Tambah ke array
    daftarKantorArray.push(formattedName);
    daftarKantorArray.sort(); // Sort alfabetical
    
    // Update tampilan
    updateDaftarKantor();
    updateKantorDropdowns();
    updateStatistics();
    
    // Reset input
    inputKantor.value = '';
    inputKantor.classList.remove('is-valid', 'is-invalid');
    
    // Save to localStorage
    saveToLocalStorage();
    
    showSuccessMessage(`Kantor "${formattedName}" berhasil ditambahkan!`);
}

// Update tampilan daftar kantor
function updateDaftarKantor() {
    const container = document.getElementById('daftarKantor');
    
    if (daftarKantorArray.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-building fa-3x mb-3 opacity-50"></i>
                <p class="mb-0">Belum ada kantor yang ditambahkan.</p>
                ${userRole === 'super_user' ? '<small>Tambahkan kantor baru menggunakan form di atas.</small>' : '<small>Hubungi Super User untuk menambah kantor baru.</small>'}
            </div>
        `;
        return;
    }
    
    let html = '<div class="kantor-badges">';
    daftarKantorArray.forEach((kantor, index) => {
        const isSelected = selectedKantor === kantor;
        const badgeClass = isSelected ? 'bg-success' : 'bg-primary';
        const selectedIcon = isSelected ? '<i class="fas fa-check me-1"></i>' : '';
        
        html += `
            <span class="badge ${badgeClass} me-2 mb-2 d-inline-flex align-items-center kantor-badge ${isSelected ? 'selected' : ''}" 
                  onclick="selectKantor('${kantor}')" style="cursor: pointer;">
                ${selectedIcon}
                <span class="kantor-name">${kantor}</span>
                ${userRole === 'super_user' ? `
                    <button type="button" class="btn-close btn-close-white ms-2" 
                            onclick="event.stopPropagation(); hapusKantor(${index})" 
                            style="font-size: 0.7em;"
                            title="Hapus kantor"></button>
                ` : ''}
            </span>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

// Hapus kantor (super_user only)
function hapusKantor(index) {
    if (userRole !== 'super_user') {
        showErrorMessage('Anda tidak memiliki akses untuk menghapus kantor!');
        return;
    }
    
    const namaKantor = daftarKantorArray[index];
    if (confirm(`Apakah Anda yakin ingin menghapus kantor "${namaKantor}"?\n\nHati-hati: Data yang sudah diinput untuk kantor ini mungkin akan terpengaruh.`)) {
        daftarKantorArray.splice(index, 1);
        
        // Clear selection jika kantor yang dihapus sedang dipilih
        if (selectedKantor === namaKantor) {
            clearSelectedKantor();
        }
        
        updateDaftarKantor();
        updateKantorDropdowns();
        updateStatistics();
        
        saveToLocalStorage();
        showSuccessMessage(`Kantor "${namaKantor}" berhasil dihapus!`);
    }
}

// Update dropdown kantor di form manual
function updateKantorDropdowns() {
    const dropdowns = document.querySelectorAll('.kantor-dropdown');
    
    dropdowns.forEach(dropdown => {
        const currentValue = dropdown.value;
        dropdown.innerHTML = '<option value="">-- Pilih Kantor --</option>';
        
        // Jika ada kantor yang dipilih, prioritaskan itu
        if (selectedKantor) {
            const option = document.createElement('option');
            option.value = selectedKantor;
            option.textContent = selectedKantor;
            option.selected = true;
            dropdown.appendChild(option);
        }
        
        // Tambahkan kantor lainnya
        daftarKantorArray.forEach(kantor => {
            if (kantor !== selectedKantor) {
                const option = document.createElement('option');
                option.value = kantor;
                option.textContent = kantor;
                if (kantor === currentValue) {
                    option.selected = true;
                }
                dropdown.appendChild(option);
            }
        });
    });
}

// Update statistik
function updateStatistics() {
    document.getElementById('totalKantorCount').textContent = daftarKantorArray.length;
    document.getElementById('selectedKantorCount').textContent = selectedKantor ? '1' : '0';
}

// Save to localStorage
function saveToLocalStorage() {
    localStorage.setItem('daftarKantor', JSON.stringify(daftarKantorArray));
}

// Fungsi untuk toggle BBM input
function toggleBBMInput(rowId, bbmType, checkbox) {
    const inputContainer = document.getElementById(`bbm-input-${rowId}-${bbmType}`);
    const hiddenInput = document.getElementById(`hidden-${rowId}-${bbmType}`);
    
    if (checkbox.checked) {
        inputContainer.style.display = 'block';
        hiddenInput.disabled = false;
        hiddenInput.focus();
    } else {
        inputContainer.style.display = 'none';
        hiddenInput.disabled = true;
        hiddenInput.value = '';
    }
}

// Buat BBM section
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

// Buat baris baru untuk tabel
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
            <input name="tahun[]" type="number" class="form-control form-control-sm" required min="2020" max="2030" placeholder="2024">
        </td>
        <td style="min-width: 300px;">
            ${createBBMSection(rowId)}
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input name="listrik[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required placeholder="100">
                <span class="input-group-text">kWh</span>
            </div>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input name="daya_listrik[]" type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="1300">
                <span class="input-group-text">VA</span>
            </div>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input name="air[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required placeholder="10">
                <span class="input-group-text">m³</span>
            </div>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input name="kertas[]" type="number" class="form-control form-control-sm" step="0.01" min="0" required placeholder="5">
                <span class="input-group-text">rim</span>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="hapusRow(this)" title="Hapus baris">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
}

// Toggle form manual
function toggleForm() {
    const formDiv = document.getElementById("formManual");
    const toggleBtn = event.target;
    const isVisible = formDiv.style.display !== "none";
    
    if (!isVisible) {
        // Cek apakah sudah memilih kantor
        if (!selectedKantor && daftarKantorArray.length > 0) {
            showErrorMessage('Silakan pilih kantor terlebih dahulu sebelum menambah data manual!');
            return;
        }
        
        // Jika form akan ditampilkan, pastikan ada minimal 1 baris
        const tbody = document.getElementById('data-table-body');
        if (tbody.children.length === 0) {
            tambahRow();
        }
        
        formDiv.style.display = "block";
        toggleBtn.innerHTML = '<i class="fas fa-minus-circle me-2"></i>Tutup Form Manual';
        toggleBtn.className = 'btn btn-outline-secondary btn-lg';
    } else {
        formDiv.style.display = "none";
        toggleBtn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Tambah Data Manual';
        toggleBtn.className = 'btn btn-success btn-lg';
    }
}

// Tambah baris ke tabel
function tambahRow() {
    const tbody = document.getElementById('data-table-body');
    const newRow = document.createElement('tr');
    newRow.innerHTML = createNewRow();
    tbody.appendChild(newRow);
    
    // Update dropdown kantor untuk row baru
    updateKantorDropdowns();
    
    // Smooth scroll ke row baru
    newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Hapus baris dari tabel
function hapusRow(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('data-table-body');
    
    if (tbody.children.length > 1) {
        if (confirm('Apakah Anda yakin ingin menghapus baris ini?')) {
            row.remove();
            updateRowNumbers();
        }
    } else {
        showErrorMessage('Minimal harus ada 1 baris data!');
    }
}

// Update nomor baris setelah penghapusan
function updateRowNumbers() {
    const tbody = document.getElementById('data-table-body');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach((row, index) => {
        const numberBadge = row.querySelector('.badge');
        if (numberBadge) {
            numberBadge.textContent = index + 1;
        }
    });
}

// Fungsi untuk menampilkan pesan error
function showErrorMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
            <div>
                <strong>Error!</strong><br>
                ${message}
            </div>
        </div>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Fungsi untuk menampilkan pesan sukses
function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2 fa-lg"></i>
            <div>${message}</div>
        </div>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
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
/* Base Styles */
.table-responsive {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table th, .table td {
    white-space: nowrap;
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.form-control-sm, .form-select-sm {
    min-width: 100px;
}

.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: none;
    border-radius: 12px;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0 !important;
    padding: 1rem 1.5rem;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* BBM Selection Styles */
.bbm-selection-container {
    max-height: 300px;
    overflow-y: auto;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    position: relative;
}

.bbm-selection-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545, #6f42c1);
    border-radius: 8px 8px 0 0;
}

.bbm-item {
    padding: 8px 0;
    border-bottom: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.bbm-item:last-child {
    border-bottom: none;
}

.bbm-item:hover {
    background-color: rgba(0,123,255,0.05);
    border-radius: 4px;
    padding-left: 5px;
}

.bbm-input-container {
    margin-top: 8px;
    margin-left: 25px;
    transition: all 0.3s ease;
}

.form-check-inline {
    margin-bottom: 5px;
}

.input-group-sm {
    width: 140px;
}

/* Dropdown Search Styles */
.dropdown-search-container {
    position: relative;
}

.kantor-search-input {
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
}

.kantor-search-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    transform: translateY(-1px);
}

.dropdown-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #007bff;
    border-top: none;
    border-radius: 0 0 12px 12px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 8px 25px rgba(0,123,255,0.15);
    margin-top: -2px;
}

.dropdown-list-item {
    padding: 12px 18px;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f4;
    font-size: 14px;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dropdown-list-item:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    border-left: 4px solid #007bff;
    padding-left: 14px;
    transform: translateX(2px);
}

.dropdown-list-item:last-child {
    border-bottom: none;
    border-radius: 0 0 12px 12px;
}

.dropdown-list-item.text-muted {
    cursor: default;
    font-style: italic;
    justify-content: center;
    background-color: #f8f9fa;
}

.dropdown-list-item.text-muted:hover {
    background-color: #f8f9fa;
    border-left: none;
    padding-left: 18px;
    transform: none;
}

.dropdown-list-item.kantor-option {
    position: relative;
}

.dropdown-list-item strong {
    color: #007bff;
    font-weight: 600;
    background: linear-gradient(135deg, #007bff, #0056b3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Kantor List Container */
.kantor-list-container {
    max-height: 250px;
    overflow-y: auto;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.kantor-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.kantor-badge {
    font-size: 0.9em;
    padding: 10px 15px;
    transition: all 0.3s ease;
    cursor: pointer;
    border-radius: 25px;
    position: relative;
    overflow: hidden;
}

.kantor-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.kantor-badge:hover::before {
    left: 100%;
}

.kantor-badge:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.kantor-badge.selected {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

.kantor-name {
    margin-right: 8px;
}

/* Statistics Cards */
.card.bg-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.card.bg-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Toast Notifications */
.alert.position-fixed {
    animation: slideInRight 0.4s ease-out;
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 12px;
}

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

/* Validation Styles */
.validation-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
    padding: 8px 12px;
    border-radius: 6px;
    border-left: 4px solid #dc3545;
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    animation: shake 0.5s ease-in-out;
}

.form-control.is-valid {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Button Enhancements */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-lg {
    padding: 12px 24px;
    font-size: 1.1rem;
}

/* Responsive Design */
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
        padding: 10px;
    }
    
    .bbm-input-container {
        margin-left: 15px;
    }
    
    .input-group-sm {
        width: 110px;
    }
    
    .dropdown-list {
        max-height: 200px;
        font-size: 13px;
    }
    
    .dropdown-list-item {
        padding: 10px 15px;
        font-size: 13px;
    }
    
    .kantor-badge {
        font-size: 0.8em;
        padding: 8px 12px;
    }
    
    .card-header {
        padding: 0.75rem 1rem;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 1rem;
    }
    
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
}

/* Custom Scrollbars */
.dropdown-list::-webkit-scrollbar,
.bbm-selection-container::-webkit-scrollbar,
.kantor-list-container::-webkit-scrollbar {
    width: 8px;
}

.dropdown-list::-webkit-scrollbar-track,
.bbm-selection-container::-webkit-scrollbar-track,
.kantor-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.dropdown-list::-webkit-scrollbar-thumb,
.bbm-selection-container::-webkit-scrollbar-thumb,
.kantor-list-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 4px;
}

.dropdown-list::-webkit-scrollbar-thumb:hover,
.bbm-selection-container::-webkit-scrollbar-thumb:hover,
.kantor-list-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
}

/* Loading States */
.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Focus States */
.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* Table Enhancements */
.table-primary th {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.table-bordered {
    border: 2px solid #dee2e6;
}

.table-bordered td,
.table-bordered th {
    border: 1px solid #dee2e6;
}

/* Input Group Enhancements */
.input-group-text {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-color: #dee2e6;
    font-weight: 500;
    color: #495057;
}

/* Alert Enhancements */
.alert {
    border: none;
    border-radius: 12px;
    border-left: 4px solid;
}

.alert-primary {
    border-left-color: #007bff;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
}

.alert-info {
    border-left-color: #17a2b8;
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
}

.alert-success {
    border-left-color: #28a745;
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
}

.alert-danger {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
}
</style>
@endsection