@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>✏️ Edit Data Energi</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="/admin/energi/{{ $item->id }}">
        @csrf @method('PUT')

        <div class="row mb-3">
            <div class="col"><label>Kantor</label><input value="{{ $item->kantor }}" name="kantor" class="form-control"></div>
            <div class="col"><label>Bulan</label><input value="{{ $item->bulan }}" name="bulan" class="form-control"></div>
            <div class="col"><label>Tahun</label><input value="{{ $item->tahun }}" name="tahun" class="form-control"></div>
        </div>

        <div class="row mb-3">
            <div class="col"><label>Listrik</label><input value="{{ $item->listrik }}" name="listrik" class="form-control"></div>
            <div class="col"><label>Air</label><input value="{{ $item->air }}" name="air" class="form-control"></div>
            <div class="col"><label>BBM</label><input value="{{ $item->bbm }}" name="bbm" class="form-control"></div>
            <div class="col"><label>Kertas</label><input value="{{ $item->kertas }}" name="kertas" class="form-control"></div>
        </div>

        <button class="btn btn-success">💾 Update</button>
    </form>
</div>
@endsection
