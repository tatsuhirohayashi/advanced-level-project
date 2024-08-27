<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Mail\ReservationReminder;
use Illuminate\Support\Facades\Mail;
use App\Models\Reservation;
use App\Models\SchedulerSetting;

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
        $setting = SchedulerSetting::first();
        $scheduleTime = $setting ? \Carbon\Carbon::parse($setting->schedule_time)->format('H:i') : '07:00';

        $schedule->call(function () {
            $reservations = Reservation::where('reservation_date', now()->toDateString())->get();

            foreach ($reservations as $reservation) {
                Mail::to($reservation->user->email)->send(new ReservationReminder($reservation));
            }
        })->dailyAt($scheduleTime);
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
