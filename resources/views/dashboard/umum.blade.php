@extends('dashboard.layout')

@section('content')

  {{-- ✅ Notifikasi sukses --}}
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
      ✅ {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  @endif

  <p class="mt-4">Sebagai user umum, Anda dapat mencatat data energi serta memantau laporan penggunaannya.</p>

@endsection
