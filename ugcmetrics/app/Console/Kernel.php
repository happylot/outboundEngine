<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Los comandos Artisan proporcionados por tu aplicación.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\GenerateInstagramReports::class,
        \App\Console\Commands\GenerateTwitchReports::class,
    ];

    /**
     * Define el cronograma de comandos de la aplicación.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Programar comandos para ejecutar en horarios específicos
        $schedule->command('instagram:generate-reports')->monthlyOn(30, '23:50');
        $schedule->command('twitch:generate-reports')->monthlyOn(30, '23:55');
    }

    /**
     * Registra los comandos para la aplicación.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // Si quieres incluir rutas de comando específicas:
        // require base_path('routes/console.php');
    }
}