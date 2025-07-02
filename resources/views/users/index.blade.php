@extends('dashboard.layout')

@section('content')
<div class="container">
    {{-- ✅ Header dengan Tambah User & Search di satu baris --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Manajemen User</h3>
        <div class="d-flex align-items-center gap-2">
            {{-- Form Search --}}
            <form method="GET" action="{{ route('users.index') }}" class="me-2">
                <div class="input-group rounded-pill shadow-sm">
                    <input type="text" name="search" class="form-control border-success rounded-start-pill" placeholder="🔍 Cari user..." value="{{ request('search') }}">
                    <button class="btn btn-success rounded-end-pill px-4" type="submit">Cari</button>
                </div>
            </form>
            {{-- Tombol Tambah User --}}
            <a href="{{ route('users.create') }}" class="btn btn-success">Tambah User +</a>
        </div>
    </div>

    {{-- ✅ Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Unit Kerja</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->unit_kerja }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data user ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- ✅ Info & Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari total {{ $users->total() }} user
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
