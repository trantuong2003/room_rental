<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\User;
use App\Models\LandlordPost;
use App\Models\Message;
use App\Models\Comment;
use App\Models\Payment;
use Carbon\Carbon;
class HomeController extends Controller
{
    // public function home()
    // {
    //     return view('landord.home');
    // }

    public function home()
    {
        $user = Auth::user();

        // Get all active subscriptions for the user
        $subscriptions = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();

        // Calculate total remaining posts
        $remainingPosts = $subscriptions->sum(function ($subscription) {
            return (int) $subscription->remaining_posts;
        });

        // Get total posts by the user
        $totalPosts = LandlordPost::where('user_id', $user->id)->count();

        // Get subscription expiry date (latest end_date from active subscriptions)
        $latestSubscription = $subscriptions->sortByDesc('end_date')->first();
        $subscriptionExpiry = $latestSubscription ? Carbon::parse($latestSubscription->end_date)->format('Y-m-d') : null;

        // Get recent messages (last 5 received messages)
        $recentMessages = Message::where('receiver_id', $user->id)
            ->with('sender')
            ->latest('created_at')
            ->take(5)
            ->get();

        // Get recent comments on user's posts (last 5)
        $recentComments = Comment::whereIn('commentable_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('landlord_posts')
                    ->where('user_id', $user->id);
            })
            ->where('commentable_type', LandlordPost::class)
            ->with('user')
            ->latest('created_at')
            ->take(5)
            ->get();

        // Get recent payments (last 5)
        $recentTransactions = Payment::where('user_id', $user->id)
            ->latest('created_at')
            ->take(5)
            ->get();

        $message = $remainingPosts > 0 ? null : 'Bạn không có gói đăng ký hoặc gói đăng ký đã hết hạn.';

        return view('landord.home', compact(
            'remainingPosts',
            'totalPosts',
            'subscriptionExpiry',
            'recentMessages',
            'recentComments',
            'recentTransactions',
            'message'
        ));
    }
}
