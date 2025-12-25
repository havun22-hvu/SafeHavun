<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled tasks for crypto data fetching
Schedule::command('crypto:fetch-prices')->everyFifteenMinutes();
Schedule::command('crypto:fetch-fear-greed')->hourly();
Schedule::command('crypto:fetch-gold')->everyThirtyMinutes();
Schedule::command('crypto:fetch-whales')->hourly();
Schedule::command('crypto:generate-signals')->everyFifteenMinutes();
