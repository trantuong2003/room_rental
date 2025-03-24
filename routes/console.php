<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\UpdateExpiredSubscriptionsJob;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lập lịch cho job chạy hàng ngày
return function (Schedule $schedule) {
    $schedule->job(new UpdateExpiredSubscriptionsJob)->daily();
};