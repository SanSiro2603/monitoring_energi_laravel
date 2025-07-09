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
            <div class="row mb-3">
                <div class="col">
                    <label>Kantor</label>
                    <input name="kantor" class="form-control" required>
                </div>
                <div class="col">
                    <label>Bulan</label>
                    <input name="bulan" class="form-control" required>
                </div>
                <div class="col">
                    <label>Tahun</label>
                    <input name="tahun" type="number" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label>Listrik (kWh)</label>
                    <input name="listrik" type="number" class="form-control" required>
                </div>
                <div class="col">
                    <label>Daya Listrik (VA)</label>
                    <input name="daya_listrik" type="number" class="form-control" placeholder="Contoh: 1300">
                </div>
                <div class="col">
                    <label>Air (m³)</label>
                    <input name="air" type="number" class="form-control" required>
                </div>
                <div class="col">
                    <label>Kertas (rim)</label>
                    <input name="kertas" type="number" class="form-control" required>
                </div>
            </div>

            <!-- Jenis BBM Dinamis -->
            <label class="mb-2 fw-semibold">Jenis BBM & Jumlah (liter)</label>
            <div id="bbm-container" class="mb-3">
                <div class="row bbm-row align-items-center g-2 mb-2">
                    <div class="col-md-5">
                        <select name="jenis_bbm[]" class="form-select" required>
                            <option value="">-- Pilih Jenis BBM --</option>
                            <option value="Pertalite">Pertalite</option>
                            <option value="Pertamax">Pertamax</option>
                            <option value="Solar">Solar</option>
                            <option value="Dexlite">Dexlite</option>
                            <option value="Pertamina Dex">Pertamina Dex</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" name="jumlah_bbm[]" class="form-control" placeholder="Liter" required>
                    </div>
                    <div class="col-md-2 d-flex justify-content-end">
                        <button type="button" class="btn btn-light border shadow-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width:32px; height:32px;" onclick="hapusBBM(this)">
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tombol Tambah dan Simpan -->
            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <button type="submit" class="btn btn-success">💾 Simpan</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="tambahBBM()">+ Tambah BBM</button>
            </div>
        </form>
    </div>

    <hr>

    <h5>⬆️ Import Data Energi dari Excel</h5>
    <form method="POST" action="{{ url('/energi/import') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="fileexcel" accept=".xlsx, .xls" class="form-control mb-3" required>
        <button type="submit" class="btn btn-primary">Import Excel</button>
    </form>
</div>

<script>
function toggleForm() {
    const formDiv = document.getElementById("formManual");
    formDiv.style.display = (formDiv.style.display === "none") ? "block" : "none";
}

function tambahBBM() {
    const container = document.getElementById('bbm-container');
    const newRow = document.createElement('div');
    newRow.classList.add('row', 'bbm-row', 'align-items-center', 'g-2', 'mb-2');
    newRow.innerHTML = `
        <div class="col-md-5">
            <select name="jenis_bbm[]" class="form-select" required>
                <option value="">-- Pilih Jenis BBM --</option>
                <option value="Pertalite">Pertalite</option>
                <option value="Pertamax">Pertamax</option>
                <option value="Solar">Solar</option>
                <option value="Dexlite">Dexlite</option>
                <option value="Pertamina Dex">Pertamina Dex</option>
            </select>
        </div>
        <div class="col-md-5">
            <input type="number" name="jumlah_bbm[]" class="form-control" placeholder="Liter" required>
        </div>
        <div class="col-md-2 d-flex justify-content-end">
            <button type="button"
                class="btn btn-light border shadow-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                style="width:32px; height:32px;" onclick="hapusBBM(this)">
                <i class="fa fa-trash text-danger"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
}

function hapusBBM(button) {
    const row = button.closest('.bbm-row');
    row.remove();
}
</script>
@endsection
