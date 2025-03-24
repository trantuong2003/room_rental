<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
// use App\Models\User;


class HomeCustomerController extends Controller
{
    public function home()
    {
        $posts = LandlordPost::with('images','user')->where('status', 'approved')->latest()->get();
        $userId = Auth::id();
        $posts->each(function ($post) use ($userId) {
            $post->isFavorited = Favorite::where('user_id', $userId)
                ->where('post_id', $post->id)
                ->exists();
        });
        
        return view('customer.home', compact('posts'));
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
