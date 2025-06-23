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

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:2048',
        ]);

        $user = \App\Models\User::find(Auth::id());

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