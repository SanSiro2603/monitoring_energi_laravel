@extends('dashboard.layout')

@section('content')
<div class="container">
    <h3 class="mb-4">Tambah User Umum</h3>

    {{-- ✅ Tampilkan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ✅ Form untuk tambah user --}}
    <form action="{{ url('/divisi/users') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" value="{{ old('username') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Unit Kerja</label>
            <input type="text" name="unit_kerja" value="{{ old('unit_kerja') }}" class="form-control">
        </div>

        {{-- ✅ Role user diset otomatis ke user_umum --}}
        <input type="hidden" name="role" value="user_umum">

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ url('/divisi/users') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
