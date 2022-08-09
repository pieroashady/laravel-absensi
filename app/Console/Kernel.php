<?php

namespace App\Console;

use App\Models\AbsenSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $todayAbsen = AbsenSiswa::select('siswa_id')->whereDate('created_at', date('Y-m-d'))->get();
            $userList = $todayAbsen->pluck('siswa_id')->all();
            $userAlpaList = Siswa::select('id')->whereNotIn('id', $userList)->get();
            foreach ($userAlpaList as $key => $value) {
                AbsenSiswa::create([
                    'siswa_id' => $value->id,
                    'tanggal' => date('Y-m-d'),
                    'keterangan' => 'Alpa'
                ]);
            };
        })->timezone('Asia/Jakarta')->weekdays()->at('08:00');

        // $schedule->command('inspire')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
