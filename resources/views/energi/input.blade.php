@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📝 Input Data Konsumsi Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tombol Toggle -->
    <button class="btn btn-success mb-3" onclick="toggleForm()">➕ Tambah Data Manual</button>

    <!-- Form Manual -->
    <div id="formManual" style="display: none;">
        <form method="POST" action="/energi/store">
            @csrf
            <div class="row mb-3">
                <div class="col"><label>Kantor</label><input name="kantor" class="form-control" required></div>
                <div class="col"><label>Bulan</label><input name="bulan" class="form-control" required></div>
                <div class="col"><label>Tahun</label><input name="tahun" type="number" class="form-control" required></div>
            </div>
            <div class="row mb-3">
                <div class="col"><label>Listrik (kWh)</label><input name="listrik" type="number" class="form-control" required></div>
                <div class="col"><label>Air (m³)</label><input name="air" type="number" class="form-control" required></div>
                <div class="col"><label>BBM (liter)</label><input name="bbm" type="number" class="form-control" required></div>
                <div class="col"><label>Kertas (rim)</label><input name="kertas" type="number" class="form-control" required></div>
            </div>
            <button type="submit" class="btn btn-success">💾 Simpan</button>
        </form>
    </div>

    <hr>

    <h5>⬆️ Import Data Energi dari Excel</h5>
    <form method="POST" action="/energi/import" enctype="multipart/form-data">
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
</script>
@endsection
