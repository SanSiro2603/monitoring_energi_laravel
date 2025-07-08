<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class OtpController extends Controller
{
    public function showForm()
    {
        return view('auth.otp'); // Pastikan view auth/otp.blade.php ada
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        if (
            $user->otp_code === $request->otp_code &&
            Carbon::parse($user->otp_expires_at)->isFuture()
        ) {
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            // Login otomatis
            auth()->login($user);

            return redirect()->route('dashboard')->with('success', 'OTP berhasil diverifikasi.');
        }

        return redirect()->back()->with('error', 'Kode OTP tidak valid atau sudah kadaluarsa.');
    }

    public function resend(Request $request)
    {
        $userId = session('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('register')->with('error', 'User tidak ditemukan.');
        }

        $user->otp_code = rand(100000, 999999);
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::raw("Kode OTP baru Anda: {$user->otp_code}", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Kode OTP Baru - Monitoring Energi');
        });

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}