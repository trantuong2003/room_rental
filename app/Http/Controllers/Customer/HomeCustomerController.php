<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\CustomerPost;
// use App\Models\User;


class HomeCustomerController extends Controller
{

    public function home()
    {
        // Lấy cả landlord và customer posts (nếu cần)
        $landlordPosts = LandlordPost::with('images', 'user')
            ->where('status', 'approved')
            ->latest()
            ->get();
            
        $userId = Auth::id();
        
        $landlordPosts->each(function ($post) use ($userId) {
            $post->isFavorited = Favorite::where('user_id', $userId)
                ->where('favoriteable_id', $post->id)
                ->where('favoriteable_type', LandlordPost::class)
                ->exists();
                
            $post->type = 'landlord'; // Thêm thuộc tính type để phân biệt
        });
        
        return view('customer.home', ['posts' => $landlordPosts]);
    }
    

    public function toggleFavorite(Request $request)
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
    // public function home()
    // {
    //     $posts = LandlordPost::with('images','user')->where('status', 'approved')->latest()->get();
    //     $userId = Auth::id();
    //     $posts->each(function ($post) use ($userId) {
    //         $post->isFavorited = Favorite::where('user_id', $userId)
    //             ->where('post_id', $post->id)
    //             ->exists();
    //     });
        
    //     return view('customer.home', compact('posts'));
    // }
    

    // public function toggleFavorite(Request $request)
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
}
