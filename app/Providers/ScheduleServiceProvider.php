<?php

namespace App\Providers;

use App\Jobs\CheckExpiredSubscriptions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Đăng ký các tác vụ lập lịch.
     */
    public function boot(Schedule $schedule): void
    {
        // Chạy job kiểm tra gói đăng ký hết hạn mỗi ngày
        $schedule->job(new CheckExpiredSubscriptions)->daily();
    }
}
