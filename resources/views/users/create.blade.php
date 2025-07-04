@extends('dashboard.layout')

@section('content')
<div class="container">
    <h3>Tambah User</h3>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        @include('users.form')
        <button type="submit" class="btn btn-success">Simpan</form>
</div>
@endsection