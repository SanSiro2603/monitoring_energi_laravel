<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email - Hemat Energi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow" style="max-width: 500px;">
        <h4 class="mb-3 text-success text-center">📧 Verifikasi Email Anda</h4>
        <p class="mb-3">Kami telah mengirimkan link verifikasi ke email Anda. Silakan cek email dan klik tautan verifikasinya.</p>

        @if (session('resent'))
            <div class="alert alert-success">Link verifikasi baru telah dikirim ke email Anda.</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-success w-100">Kirim Ulang Email Verifikasi</button>
        </form>

        <form action="{{ route('logout') }}" method="POST" class="mt-3 text-center">
            @csrf
            <button type="submit" class="btn btn-link">Keluar</button>
        </form>
    </div>
</body>
</html>
