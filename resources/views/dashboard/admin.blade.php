@extends('dashboard.layout')

@section('content')

  {{-- CSS untuk animasi hover di kartu welcome --}}
  <style>
    .welcome-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .welcome-card:hover {
      transform: scale(1.02);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
  </style>

  @if(request()->is('dashboard') || request()->is('admin/dashboard') || request()->is('divisi/dashboard') || request()->is('umum/dashboard'))
    <div class="card shadow-sm p-4 mb-4 welcome-card border-start border-4 border-success">
      <h4 class="fw-bold text-success mb-2">
        Selamat Datang, {{ Auth::user()->name }}! 👋
      </h4>
      <p class="mb-0 text-secondary">
        Anda masuk ke sistem pemantauan penggunaan energi pada kantor Bank Lampung.
      </p>
    </div>
  @endif

  <p class="text-muted">Sebagai <strong>Super User</strong>, Anda dapat mengelola data dan pengguna.</p>

@endsection
