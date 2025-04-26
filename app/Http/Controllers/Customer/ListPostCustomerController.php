<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use App\Models\Favorite;
use App\Models\Comment;
use App\Models\CustomerPost;
use Illuminate\Support\Facades\Auth;

class ListPostCustomerController extends Controller
{
     // Hiển thị danh sách bài đăng 
     public function index()
     {
         $posts = CustomerPost::with(['user', 'comments.user', 'favoritedby'])
             ->where('status', 'approved')
             ->withCount(['comments', 'favoritedby'])
             ->latest()
             ->get();
 
         // Thêm thông tin is_favorited thủ công nếu cần
         if (Auth::check()) {
             $posts->each(function ($post) {
                 $post->is_favorited = $post->favoritedby->contains('user_id', Auth::id());
             });
         }
 
         return view('customer.list_customer_post', compact('posts'));
     }
 
 
     //yeu thich
     // Yêu thích bài đăng
     public function toggleFavorite(Request $request)
     {
        $request->validate([
            'post_id' => 'required|integer',
            'post_type' => 'required|in:landlord,customer'
        ]);

        $userId = Auth::id();
        $postId = $request->post_id;
        $postType = $request->post_type;
        
        $model = $postType === 'customer' ? CustomerPost::class : LandlordPost::class;

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
 
     // Xử lý bình luận
     public function storeComment(Request $request, $postId)
     {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $post = CustomerPost::findOrFail($postId);

        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::id();
        $comment->commentable_id = $post->id;
        $comment->commentable_type = CustomerPost::class;
        $comment->parent_id = $request->parent_id;
        $comment->save();

        return redirect()->route('customer.roommates.index');
     }
 
     public function updateComment(Request $request, $commentId)
     {
         $request->validate([
             'content' => 'required|string|max:1000'
         ]);
 
         $comment = Comment::where('id', $commentId)
             ->where('user_id', Auth::id())
             ->firstOrFail();
 
         $comment->content = $request->content;
         $comment->save();
 
         return redirect()->route('customer.roommates.index');
     }
}
