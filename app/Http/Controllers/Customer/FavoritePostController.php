<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

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
        $postId = $request->input('post_id');
        $user = Auth::user();

        // Kiểm tra xem bài đăng đã được yêu thích chưa
        $favorite = Favorite::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        if ($favorite) {
            // Nếu đã yêu thích, xóa khỏi danh sách yêu thích
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            // Nếu chưa yêu thích, thêm vào danh sách yêu thích
            Favorite::create([
                'user_id' => $user->id,
                'post_id' => $postId,
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}
