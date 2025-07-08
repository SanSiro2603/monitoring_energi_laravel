<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;

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
            'jabatan' => ['nullable', 'string', 'max:255'],
            'wilayah' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $otp = rand(100000, 999999);

        $data = $request->only([
            'name', 'username', 'email', 'unit_kerja', 'unit_bagian', 'jabatan', 'wilayah'
        ]);

        $data['password'] = Hash::make($request->password);
        $data['role'] = 'user_umum';
        $data['foto'] = null;
        $data['otp_code'] = $otp;
        $data['otp_expires_at'] = now()->addMinutes(10);
        $data['email_verified_at'] = null; // OTP belum diverifikasi

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto_users', 'public');
        }

        $user = User::create($data);

        // Kirim email OTP
        Mail::raw("Kode OTP Anda adalah: $otp\nMasa berlaku 10 menit.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Kode OTP Verifikasi - Monitoring Energi Bank Lampung');
        });

        // Simpan user ID ke session
        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.form')->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }
}
