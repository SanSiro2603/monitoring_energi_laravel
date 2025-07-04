@extends('dashboard.layout')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Manajemen User</h3>
        @if(Auth::user()->role === 'super_user')
            <a href="{{ route('users.create') }}" class="btn btn-success">Tambah User +</a>
        @elseif(Auth::user()->role === 'divisi_user')
            <a href="{{ url('/divisi/users/create') }}" class="btn btn-success">Tambah User +</a>
        @endif
    </div>

    {{-- ✅ Search dan Per Page --}}
    <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center mb-3">
        <div class="col-md-6">
            <div class="input-group rounded-pill shadow-sm">
                <input type="text" name="search" class="form-control border-success rounded-start-pill" placeholder="🔍 Cari user..." value="{{ request('search') }}">
                <button class="btn btn-success rounded-end-pill px-4" type="submit">Cari</button>
            </div>
        </div>
        <div class="col-md-2 ms-auto">
            <select name="per_page" class="form-select" onchange="this.form.submit()">
                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 </option>
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 </option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 </option>
            </select>
        </div>
    </form>

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
                                @if(Auth::user()->role === 'super_user')
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @elseif(Auth::user()->role === 'divisi_user' && $user->role === 'user_umum')
                                    {{-- Tombol edit dan delete untuk user_umum saja --}}
                                    <a href="{{ url('/divisi/users/'.$user->id.'/edit') }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ url('/divisi/users/'.$user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data user ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

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
