<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Console\Commands\ArchiveOldVisits;
use Illuminate\Support\Facades\Schedule;

// Archive visits >1yr; soft-delete archived visits >2yr. Runs daily at 01:00.
Schedule::command(ArchiveOldVisits::class)->dailyAt('01:00');
