<h2>Register (User Umum)</h2>
@if($errors->any()) <p>{{ $errors->first() }}</p> @endif

<form method="POST" action="/register">
    @csrf
    <input type="text" name="name" placeholder="Nama" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required><br>
    <button type="submit">Register</button>
</form>
