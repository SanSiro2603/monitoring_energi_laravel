@extends('dashboard.layout')

@section('content')

  {{-- ✅ Notifikasi sukses --}}
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
      ✅ {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  @endif

  {{-- ✅ Kotak Selamat Datang --}}
  <div class="card mt-6 shadow-sm">
    <div class="bg-white shadow-sm rounded p-4" style="max-width: 900px; width: 100%;">
      <h4 class="mb-2">👋 Selamat Datang, {{ Auth::user()->name }}</h4>
      <p class="mb-0">Anda masuk ke sistem pemantauan penggunaan energi pada kantor Bank Lampung.</p>
    </div>
  </div>

  <p class="mt-6">Sebagai pengguna umum, Anda dapat mencatat data konsumsi energi dan mengakses laporan penggunaannya.</p>

@endsection