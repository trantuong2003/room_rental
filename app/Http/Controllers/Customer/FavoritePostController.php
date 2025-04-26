<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\LandlordPost;
use App\Models\CustomerPost;

class FavoritePostController extends Controller
{
    public function showFavorites()
    {

        $user = Auth::user();

        // Lấy danh sách bài đăng yêu thích cùng với hình ảnh
        $favorites = Favorite::where('user_id', $user->id)
            ->with('post.images') // Lấy bài đăng và hình ảnh liên quan
            ->get()
            ->pluck('post'); // Chỉ lấy thông tin bài đăng

        // Lấy danh sách ID các bài đăng mà user đã yêu thích
        $favoritePostIds = Favorite::where('user_id', $user->id)
            ->pluck('post_id')
            ->toArray();

        // Gán trạng thái yêu thích cho từng bài viết
        $favorites->each(function ($post) use ($favoritePostIds) {
            $post->isFavorited = in_array($post->id, $favoritePostIds);
        });

        return view('customer.favorite', compact('favorites'));
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
}