@extends('dashboard.layout')

@section('content')
<div class="container">
    <h3>Tambah User</h3>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        @include('users.form')
        <button type="submit" class="btn btn-success">Simpan</button>
        @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    </form>
</div>
@endsection