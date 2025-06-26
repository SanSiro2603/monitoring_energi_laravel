@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>✏️ Edit Data Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="/admin/energi/{{ $item->id }}">
        @csrf 
        @method('PUT')

        <div class="row mb-3">
            <div class="col">
                <label>Kantor</label>
                <input value="{{ $item->kantor }}" name="kantor" class="form-control" required>
            </div>
            <div class="col">
                <label>Bulan</label>
                <input value="{{ $item->bulan }}" name="bulan" class="form-control" required>
            </div>
            <div class="col">
                <label>Tahun</label>
                <input value="{{ $item->tahun }}" name="tahun" type="number" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label>Listrik (kWh)</label>
                <input value="{{ $item->listrik }}" name="listrik" type="number" class="form-control" required>
            </div>
            
            <div class="col">
                <label>Air (m³)</label>
                <input value="{{ $item->air }}" name="air" type="number" class="form-control" required>
            </div>
            <div class="col">
              <label>Daya Listrik (VA)</label>
              <input name="daya_listrik" type="number" class="form-control" value="{{ $item->daya_listrik }}">
            </div>

            <div class="col">
                <label>BBM (liter)</label>
                <input value="{{ $item->bbm }}" name="bbm" type="number" class="form-control" required>
            </div>
            <div class="col">
                <label>Kertas (rim)</label>
                <input value="{{ $item->kertas }}" name="kertas" type="number" class="form-control" required>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col">
                <label>Jenis BBM</label>
                <select name="jenis_bbm" class="form-control" required>
                    <option value="">-- Pilih Jenis BBM --</option>
                    <option value="Pertalite" {{ $item->jenis_bbm == 'Pertalite' ? 'selected' : '' }}>Pertalite</option>
                    <option value="Pertamax" {{ $item->jenis_bbm == 'Pertamax' ? 'selected' : '' }}>Pertamax</option>
                    <option value="Solar" {{ $item->jenis_bbm == 'Solar' ? 'selected' : '' }}>Solar</option>
                    <option value="Dexlite" {{ $item->jenis_bbm == 'Dexlite' ? 'selected' : '' }}>Dexlite</option>
                    <option value="Pertamina Dex" {{ $item->jenis_bbm == 'Pertamina Dex' ? 'selected' : '' }}>Pertamina Dex</option>
                </select>
            </div>
        </div>

        <button class="btn btn-success">💾 Update</button>
    </form>
</div>
@endsection
