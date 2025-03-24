<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Subscription;
use Carbon\Carbon;


class UpdateExpiredSubscriptionsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
          // Lấy tất cả các gói đăng ký đã hết hạn
          $expiredSubscriptions = Subscription::where('end_date', '<', Carbon::now())
          ->where('status', '!=', 'expired')
          ->get();

      // Cập nhật trạng thái của các gói đăng ký hết hạn
      foreach ($expiredSubscriptions as $subscription) {
          $subscription->update(['status' => 'expired']);
      }

      info('Expired subscriptions updated successfully.');
    }
}
