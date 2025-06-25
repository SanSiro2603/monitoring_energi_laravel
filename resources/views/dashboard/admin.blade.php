@extends('dashboard.layout')
@section('content')
    @if(request()->is('/')) {{-- Hanya muncul di homepage --}}
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Selamat datang!</strong> Klik menu dashboard untuk memulai.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <p>Sebagai Super User, Anda dapat mengelola data dan pengguna.</p>
@endsection