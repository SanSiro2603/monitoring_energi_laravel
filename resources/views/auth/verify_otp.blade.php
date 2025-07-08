<!-- resources/views/auth/verify_otp.blade.php -->
@extends('dashboard.layout')


@section('content')
<div class="container">
    <h4>Verifikasi OTP</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('otp.verify') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="otp_code">Masukkan Kode OTP</label>
            <input type="text" name="otp_code" class="form-control" maxlength="6" required>
        </div>
        <button type="submit" class="btn btn-primary">Verifikasi</button>
    </form>

    <form action="{{ route('otp.resend') }}" method="POST" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-link">Kirim Ulang OTP</button>
    </form>
</div>
@endsection
