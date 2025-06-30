@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>👤 Profil Pengguna</h4>

    <div class="text-center mb-4">
        <div class="profile-pic-container position-relative d-inline-block">
            <img src="{{ $user->foto ? asset('storage/uploads/' . $user->foto) : asset('assets/img/default-profile.png') }}"
                 class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #198754;">
            <form method="POST" action="{{ route('profil.upload') }}" enctype="multipart/form-data">
                @csrf
                <label class="upload-btn position-absolute bottom-0 end-0 bg-success text-white rounded-circle px-2 py-1"
                       title="Ganti Foto" style="cursor: pointer;">
                    <input type="file" name="foto" accept="image/*" style="display:none" onchange="this.form.submit()">
                    ✏️
                </label>
            </form>
        </div>
        @if(session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
    </div>

  <div class="mb-3 text-end">
       <a href="{{ route('profil.edit') }}" class="btn btn-success">✏️ Edit Profil</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <tr><th>Nama Lengkap</th><td>{{ $user->name }}</td></tr>
            <tr><th>Unit Bagian</th><td>{{ $user->unit_bagian }}</td></tr>
            <tr><th>Level / Golongan</th><td>{{ $user->level_gol }}</td></tr>
            <tr><th>Wilayah</th><td>{{ $user->wilayah }}</td></tr>
            <tr><th>Unit Kerja</th><td>{{ $user->unit_kerja }}</td></tr>
            <tr><th>Username</th><td>{{ $user->username }}</td></tr>
        </table>
    </div>
</div>
@endsection
