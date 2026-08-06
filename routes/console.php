<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Console\Commands\SendInvoiceReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendInvoiceReminders::class)->dailyAt('10:21');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
