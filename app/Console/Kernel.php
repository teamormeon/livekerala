<?php

namespace App\Console;

use App\Console\Commands\GatewayCurrencyUpdate;
use App\Models\Deposit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{


    protected $commands = [
        GatewayCurrencyUpdate::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('expiryDate:cron')->everySixHours();
        $schedule->command('model:prune', [
            '--model' => [Deposit::class],
        ])->daily();
        $schedule->command('app:gateway-currency-update')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
