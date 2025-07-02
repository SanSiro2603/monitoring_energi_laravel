<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    /**
     * Tampilkan form registrasi.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Tangani proses registrasi.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'unit_bagian' => ['nullable', 'string', 'max:255'],
            'level_gol' => ['nullable', 'string', 'max:255'],
            'wilayah' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only([
            'name', 'username', 'email', 'unit_kerja', 'unit_bagian', 'level_gol', 'wilayah'
        ]);

        $data['password'] = Hash::make($request->password);
        $data['role'] = 'user_umum'; // Default role
        $data['foto'] = null;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto_users', 'public');
        }

        $user = User::create($data);

        // ✅ Langsung login (jika diinginkan), dan arahkan ke dashboard
        //auth()->login($user);

        // ✅ Kirim flash message ke dashboard
        //return redirect('/dashboard')->with('success', 'Registrasi berhasil. Selamat datang!');
        
        // ⛔️ Jika tidak ingin langsung login, ganti ke ini:
         return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
