<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('debt:billing')
    ->cron('0 8 25 * *')
    ->timezone('Asia/Bangkok')
    ->withoutOverlapping();
