<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use Illuminate\Support\Facades\Http;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class DetailPostController extends Controller
{
    public function detailPost($id){
        $post = LandlordPost::with('images')->find($id);

        $userId = Auth::id();
        $post->isFavorited = Favorite::where('user_id', $userId)
        ->where('post_id', $post->id)
        ->exists();

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
        return view('customer.detail', compact('post'));
    }

    public function toggleFavorite(Request $request)
    {
        $userId = Auth::id();
        $postId = $request->post_id;
    
        $favorite = Favorite::where('user_id', $userId)->where('post_id', $postId)->first();
    
        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create(['user_id' => $userId, 'post_id' => $postId]);
            return response()->json(['status' => 'added']);
        }
    }

    
}
