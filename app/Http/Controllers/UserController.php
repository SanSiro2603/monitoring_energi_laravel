<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ✅ Tampilkan daftar user dengan fitur search dan pagination dinamis
    public function index(Request $request)
    {
        $query = User::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%')
                  ->orWhere('unit_kerja', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        // Ambil data dengan pagination
        $perPage = $request->input('per_page', 10); // default 10
        $users = $query->orderBy('name')->paginate($perPage)->appends($request->only('search', 'per_page'));

        return view('users.index', compact('users'));
    }

    // ✅ Form tambah user
    public function create()
    {
        return view('users.create');
    }

    // ✅ Simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unit_kerja' => $request->unit_kerja,
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    // ✅ Form edit user
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // ✅ Update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'unit_kerja' => $request->unit_kerja,
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    // ✅ Hapus user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
