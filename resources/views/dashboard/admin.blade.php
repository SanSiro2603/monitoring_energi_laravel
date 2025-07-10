@extends('dashboard.layout')

@section('content')
  @if(request()->is('dashboard') || request()->is('admin/dashboard') || request()->is('divisi/dashboard') || request()->is('umum/dashboard'))
    <div class="card shadow-sm p-4 mb-4">
      <h4 class="fw-bold text-success mb-2">
        Selamat Datang, {{ Auth::user()->name }}! 👋
      </h4>
      <p class="mb-0">
        Anda masuk ke sistem pemantauan penggunaan energi pada kantor Bank Lampung.
      </p>
    </div>
  @endif

  <p>Sebagai Super User, Anda dapat mengelola data dan pengguna.</p>
@endsection
