<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 🧹 Hapus OTP kadaluarsa setiap 1 menit
        $schedule->call(function () {
            DB::table('users')
                ->whereNotNull('otp')
                ->where('otp_expires_at', '<', now())
                ->update([
                    'otp' => null,
                    'otp_expires_at' => null,
                ]);
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
