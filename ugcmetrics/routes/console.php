<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('instagram:generate-reports')
    ->monthlyOn(30, '23:50'); // o cambia el día/hora si lo prefieres

// Comandos para Twitter
Schedule::command('twitter:fetch-data')
    ->hourly(); // Actualización más frecuente para Twitter

Schedule::command('twitter:generate-reports')
    ->monthlyOn(1, '00:00'); // Generar informes al inicio del mes