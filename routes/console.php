<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Resumen diario del casino para los administradores
Schedule::command('erp:resumen-diario')->dailyAt(config('erp.resumen_hora', '22:00'));
