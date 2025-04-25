<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use Illuminate\Support\Facades\Auth;
use App\Models\LandlordPostImage;
use Illuminate\Support\Facades\Http;
use App\Models\Subscription;


class PostController extends Controller
{
    public function index()
    {

        // $posts = LandlordPost::where('user_id', Auth::id())
        //     ->with('images') // Lấy kèm hình ảnh
        //     ->latest()
        //     ->get();

        // return view('landord.post', compact('posts'));

        $user = Auth::user();

        // Lấy danh sách bài đăng của người dùng
        $posts = LandlordPost::where('user_id', $user->id)
            ->with('images')
            ->latest()
            ->get();
    
        // Lấy tất cả các gói còn hiệu lực của user
        $subscriptions = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();
    
        // Tính tổng số lượt đăng bài còn lại
        $remainingPosts = $subscriptions->sum(function ($subscription) {
            return (int) $subscription->remaining_posts;
        });
    
        $message = $remainingPosts > 0 ? null : 'Bạn không có gói đăng ký hoặc gói đăng ký đã hết hạn.';
    
        return view('landord.post', compact('posts', 'remainingPosts', 'message'));
    }
    public function detail($id)
    {
        // Lấy danh sách bài đăng của người dùng hiện tại
        // $posts = LandlordPost::where('user_id', Auth::id())->latest()->get();
        $post = LandlordPost::with('images')->find($id);

        if (!$post->latitude || !$post->longitude) {
            $apiKey = config('services.google_maps.api_key');
            $address = urlencode($post->address); // Chuyển địa chỉ thành URL-friendly
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";

            $response = Http::get($url)->json();

            if (isset($response['status']) && $response['status'] === 'OK') {
                $location = $response['results'][0]['geometry']['location'];
                $post->latitude = $location['lat'];
                $post->longitude = $location['lng'];
                $post->save();
            }
        }
        return view('landord.detail_post', compact('post'));
    }
    // {
    //     $post = LandlordPost::with(['images', 'comments.user'])->findOrFail($id);
    //     $userId = Auth::id();
        
    //     // Kiểm tra favorite (nếu cần)
    //     $post->isFavorited = Favorite::where('user_id', $userId)
    //         ->where('favoriteable_id', $post->id)
    //         ->where('favoriteable_type', LandlordPost::class)
    //         ->exists();

    //     // Thêm type để phân biệt
    //     $post->type = 'landlord';

    //     if (!$post->latitude || !$post->longitude) {
    //         $this->setPostLocation($post);
    //     }

    //     return view('landord.detail_post', compact('post'));
    // }
}
