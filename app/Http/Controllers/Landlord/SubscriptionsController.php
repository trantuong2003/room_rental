<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Auth;

class SubscriptionsController extends Controller
{
    public function create($packageId)
    {
        $user = Auth::user();
        $package = SubscriptionPackage::findOrFail($packageId); // Lấy gói đăng ký

        // Kiểm tra nếu user đã có subscription active
        $existingSubscription = Subscription::where('user_id', $user->id)->where('status', 'active')->first();
        if ($existingSubscription) {
            return redirect('/landlord')->with('error', 'You already have an active subscription.');
        }

        // Tạo subscription mới dựa trên package
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'start_date' => now(),
            'end_date' => now()->addDays($package->duration),
            'post_remaining' => $package->post_limit,
            'status' => 'pending',
        ]);

        return response()->json(['subscription' => $subscription]);
    }
}
