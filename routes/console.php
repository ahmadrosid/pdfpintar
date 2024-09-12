<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command('backup:clean')->daily()->at('07:00');
Schedule::command('backup:run --only-db')->daily()->at('07:30');
Schedule::command('cleanup:temp-pdfs --days=1')->daily()->at('07:30');
