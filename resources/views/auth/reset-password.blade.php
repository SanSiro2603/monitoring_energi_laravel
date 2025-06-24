@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Reset Password</h4>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password baru" required>
        <input type="password" name="password_confirmation" class="form-control mb-3" placeholder="Konfirmasi password" required>

        <button type="submit" class="btn btn-success">Reset Password</button>
    </form>
</div>
@endsection
