<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
    
    public function create()
    {
        // Cek apakah sudah mencapai limit 50 user
        if (User::count() >= 50) {
            return redirect()->route('users.index')->with('error', 'Maksimal 50 user sudah tercapai. Tidak dapat menambah user baru.');
        }
        
        return view('users.create');
    }

    // ✅ Simpan user baru
    public function store(Request $request)
    {
        // Cek limit sebelum validasi
        if (User::count() >= 50) {
            return redirect()->back()->with('error', 'Maksimal 50 user sudah tercapai. Tidak dapat menambah user baru.');
        }

        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        // Double check limit setelah validasi untuk mencegah race condition
        if (User::count() >= 50) {
            return redirect()->back()->with('error', 'Maksimal 50 user sudah tercapai. Tidak dapat menambah user baru.');
        }

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

    // Hapus user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function indexDivisi(Request $request)
    {
        $query = User::where('role', 'user_umum');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        $users = $query->orderBy('name')->paginate($perPage)->appends($request->only('search', 'per_page'));

        return view('divisi.users.index', compact('users'));
    }

    public function createDivisi()
    {
        // Cek apakah sudah mencapai limit 50 user
        if (User::count() >= 50) {
            return redirect('/divisi/users')->with('error', 'Maksimal 50 user sudah tercapai. Tidak dapat menambah user baru.');
        }
        
        return view('divisi.users.create');
    }

    public function storeDivisi(Request $request)
    {
        // Cek limit sebelum validasi
        if (User::count() >= 50) {
            return redirect()->back()->with('error', 'Maksimal 50 user sudah tercapai. Tidak dapat menambah user baru.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Double check limit setelah validasi untuk mencegah race condition
        if (User::count() >= 50) {
            return redirect()->back()->with('error', 'Maksimal 50 user sudah tercapai. Tidak dapat menambah user baru.');
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unit_kerja' => $request->unit_kerja,
            'role' => 'user_umum', // dipaksa tetap user_umum
        ]);

        return redirect('/divisi/users')->with('success', 'User berhasil ditambahkan.');
    }

    // Helper method untuk mengecek apakah masih bisa menambah user
    public function canAddUser()
    {
        return User::count() < 50;
    }

    // Helper method untuk mendapatkan sisa slot user
    public function getRemainingUserSlots()
    {
        return 50 - User::count();
    }

    public function editDivisi($id)
    {
        $user = User::findOrFail($id);
        
        // Pastikan hanya user dengan role user_umum yang bisa diedit
        if ($user->role !== 'user_umum') {
            return redirect()->route('divisi.users.index')
                ->with('error', 'Anda hanya bisa mengedit user umum.');
        }
        
        return view('divisi.users.edit', compact('user'));
    }

    /**
     * Update user (untuk divisi)
     */
    public function updateDivisi(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Pastikan hanya user dengan role user_umum yang bisa diupdate
        if ($user->role !== 'user_umum') {
            return redirect()->route('divisi.users.index')
                ->with('error', 'Anda hanya bisa mengupdate user umum.');
        }

        // Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'unit_kerja' => 'nullable|string|max:255',
        ]);

        // Update data user
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->unit_kerja = $request->unit_kerja;
        $user->role = 'user_umum'; // tetap user_umum

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('divisi.users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    /**
     * Delete user (untuk divisi)
     */
    public function destroyDivisi($id)
    {
        $user = User::findOrFail($id);
        
        // Pastikan hanya user dengan role user_umum yang bisa dihapus
        if ($user->role !== 'user_umum') {
            return redirect()->route('divisi.users.index')
                ->with('error', 'Anda hanya bisa menghapus user umum.');
        }
        
        $user->delete();

        return redirect()->route('divisi.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}