<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Subscription;
use Carbon\Carbon;

class CheckExpiredSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function handle(): void
    {
        $now = Carbon::now();

        Subscription::where('expires_at', '<', $now)
            ->where('remaining_posts', '>', 0)
            ->update(['remaining_posts' => 0]);
    }
}
