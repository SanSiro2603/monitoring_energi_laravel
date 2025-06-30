<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Atau pakai: User::find(Auth::id());
        return view('dashboard.profil', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('dashboard.profil_edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'unit_bagian' => 'nullable|string|max:255',
            'level_gol' => 'nullable|string|max:50',
            'wilayah' => 'nullable|string|max:100',
            'unit_kerja' => 'nullable|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update data
        $user->name = $request->name;
        $user->unit_bagian = $request->unit_bagian;
        $user->level_gol = $request->level_gol;
        $user->wilayah = $request->wilayah;
        $user->unit_kerja = $request->unit_kerja;
        $user->username = $request->username;

        // Foto Profil
        if ($request->hasFile('foto')) {
            if ($user->foto && file_exists(public_path('assets/img/' . $user->foto))) {
                unlink(public_path('assets/img/' . $user->foto));
            }

            $file = $request->file('foto');
            $namaFile = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/img/'), $namaFile);

            $user->foto = $namaFile;
        }

        $user->save();

        return redirect()->route('profil.index')->with('success', '✅ Profil berhasil diperbarui!');
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:2048',
        ]);

        $user = User::find(Auth::id());

        // Hapus foto lama jika ada
        if ($user->foto && Storage::exists('public/uploads/' . $user->foto)) {
            Storage::delete('public/uploads/' . $user->foto);
        }

        // Simpan foto baru
        $file = $request->file('foto');
        $namaFile = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/uploads', $namaFile);

        $user->foto = $namaFile;
        $user->save();

        return redirect()->route('profil.index')->with('success', 'Foto berhasil diperbarui!');
    }
}
