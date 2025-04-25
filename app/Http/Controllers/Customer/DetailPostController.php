<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use App\Models\CustomerPost;
use Illuminate\Support\Facades\Http;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class DetailPostController extends Controller
{
    public function detailPost($id)
    // {
    //     $post = LandlordPost::with('images')->find($id);

    //     $userId = Auth::id();
    //     $post->isFavorited = Favorite::where('user_id', $userId)
    //     ->where('post_id', $post->id)
    //     ->exists();

    //     if (!$post->latitude || !$post->longitude) {
    //         $apiKey = config('services.google_maps.api_key');
    //         $address = urlencode($post->address); // Chuyển địa chỉ thành URL-friendly
    //         $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";

    //         $response = Http::get($url)->json();

    //         if (isset($response['status']) && $response['status'] === 'OK') {
    //             $location = $response['results'][0]['geometry']['location'];
    //             $post->latitude = $location['lat'];
    //             $post->longitude = $location['lng'];
    //             $post->save();
    //         }
    //     }
    //     return view('customer.detail', compact('post'));
    // }
    {
        $post = LandlordPost::with(['images', 'comments.user', 'user'])->findOrFail($id);
        $userId = Auth::id();
        
        // Kiểm tra favorite
        $post->isFavorited = Favorite::where('user_id', $userId)
            ->where('favoriteable_id', $post->id)
            ->where('favoriteable_type', LandlordPost::class)
            ->exists();

        // Thêm type để phân biệt
        $post->type = 'landlord';

        if (!$post->latitude || !$post->longitude) {
            $this->setPostLocation($post);
        }

        return view('customer.detail', compact('post'));
    }
    public function toggleFavorite(Request $request)
    // {
    //     $userId = Auth::id();
    //     $postId = $request->post_id;
    
    //     $favorite = Favorite::where('user_id', $userId)->where('post_id', $postId)->first();
    
    //     if ($favorite) {
    //         $favorite->delete();
    //         return response()->json(['status' => 'removed']);
    //     } else {
    //         Favorite::create(['user_id' => $userId, 'post_id' => $postId]);
    //         return response()->json(['status' => 'added']);
    //     }
    // }
    {
        $request->validate([
            'post_id' => 'required|integer',
            'post_type' => 'required|in:landlord,customer'
        ]);

        $userId = Auth::id();
        $postId = $request->post_id;
        $postType = $request->post_type;
        
        $model = $postType === 'landlord' ? LandlordPost::class : CustomerPost::class;

        $favorite = Favorite::where('user_id', $userId)
            ->where('favoriteable_id', $postId)
            ->where('favoriteable_type', $model)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create([
                'user_id' => $userId,
                'favoriteable_id' => $postId,
                'favoriteable_type' => $model
            ]);
            return response()->json(['status' => 'added']);
        }
    }
    private function setPostLocation($post)
    {
        $apiKey = config('services.google_maps.api_key');
        $address = urlencode($post->address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";

        $response = Http::get($url)->json();

        if (isset($response['status']) && $response['status'] === 'OK') {
            $location = $response['results'][0]['geometry']['location'];
            $post->latitude = $location['lat'];
            $post->longitude = $location['lng'];
            $post->save();
        }
    }
}
