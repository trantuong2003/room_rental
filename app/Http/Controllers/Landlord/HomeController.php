<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\User;

class HomeController extends Controller
{
    // public function home()
    // {
    //     return view('landord.home');
    // }

    public function home()
    {
        $user = Auth::user();
        // Lấy tất cả các gói còn hiệu lực của user
        $subscriptions = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();
        // Tính tổng số lượt đăng bài còn lại
        $remainingPosts = $subscriptions->sum(function ($subscription) {
            return (int) $subscription->remaining_posts;
        });

        // var_dump($remainingPosts);
        
        $message = $remainingPosts > 0 ? null : 'Bạn không có gói đăng ký hoặc gói đăng ký đã hết hạn.';

        return view('landord.home', compact('remainingPosts', 'message'));
    }
}
