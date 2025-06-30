<div class="mb-3">
    <label>Nama</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" required>
</div>
<div class="mb-3">
    <label>Username</label>
    <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}" class="form-control" required>
</div>
<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
</div>
@if (!isset($user))
<div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
</div>
@endif
<div class="mb-3">
    <label>Unit Kerja</label>
    <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $user->unit_kerja ?? '') }}" class="form-control">
</div>
<div class="mb-3">
    <label>Role</label>
    <select name="role" class="form-control" required>
        @foreach (['super_user', 'divisi_user', 'user_umum'] as $role)
            <option value="{{ $role }}" {{ (old('role', $user->role ?? '') == $role) ? 'selected' : '' }}>
                {{ ucfirst(str_replace('_', ' ', $role)) }}
            </option>
        @endforeach
    </select>
</div>