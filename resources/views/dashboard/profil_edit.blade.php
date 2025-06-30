@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📝 Edit Profil</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- Spoofing method PUT karena route menggunakan Route::put() --}}

        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="unit_bagian" class="form-label">Unit Bagian</label>
            <input type="text" name="unit_bagian" value="{{ old('unit_bagian', $user->unit_bagian) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="level_gol" class="form-label">Level / Golongan</label>
            <select name="level_gol" class="form-control" required>
                <option value="">-- Pilih Level --</option>
                @for($i = 1; $i <= 4; $i++)
                    <option value="{{ $i }}" {{ old('level_gol', $user->level_gol) == $i ? 'selected' : '' }}>Level {{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label for="wilayah" class="form-label">Wilayah</label>
            <input type="text" name="wilayah" value="{{ old('wilayah', $user->wilayah) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="unit_kerja" class="form-label">Unit Kerja</label>
            <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $user->unit_kerja) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">Ganti Foto Profil (Opsional)</label>
            <input type="file" name="foto" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">💾 Simpan Perubahan</button>
        <a href="{{ route('profil.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
