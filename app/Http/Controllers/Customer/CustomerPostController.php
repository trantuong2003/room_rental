<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerPost;
use App\Models\Favorite;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CustomerPostController extends Controller
{
    // Hiển thị lịch sử bài đăng của người dùng
    public function history()
    {
        $posts = CustomerPost::with(['user', 'comments.user', 'favoritedby'])
            ->where('user_id', Auth::id())  
            ->withCount(['comments', 'favoritedby'])
            ->latest()
            ->get();

         // Thêm thông tin is_favorited thủ công nếu cần
         if (Auth::check()) {
            $posts->each(function ($post) {
                $post->is_favorited = $post->favoritedby->contains('user_id', Auth::id());
            });
        }


        return view('customer.customer_history_post', compact('posts'));
    }

    // Hiển thị form tạo bài đăng mới
    public function create()
    {
        return view('customer.customer_create_post');
    }

    // Lưu bài đăng mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        CustomerPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('customer.roommates.history')->with('success', 'Bài đăng đã được tạo thành công!');
    }

    // Hiển thị form chỉnh sửa bài đăng
    public function edit($id)
    {
        $post = CustomerPost::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return redirect()->route('customer.roommates.history')
                ->with('error', 'Bạn không có quyền chỉnh sửa bài đăng này');
        }

        return view('customer.editpost_customer', compact('post'));
    }

    // Cập nhật bài đăng
    public function update(Request $request, CustomerPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('customer.roommates.history')
                ->with('error', 'Bạn không có quyền chỉnh sửa bài đăng này');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('customer.roommates.history')
            ->with('success', 'Bài đăng đã được cập nhật thành công!');
    }

    // Xóa bài đăng
    public function destroy(CustomerPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false, 
                'message' => 'Unauthorized'
            ], 403);
        }

        $post->delete();
        
        return redirect()->route('customer.roommates.history')
            ->with('success', 'Bài đăng đã được cập nhật thành công!');
    }

         // Xử lý like bài đăng (chỉ cho customer posts)
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer',
        ]);

        $userId = Auth::id();
        $postId = $request->post_id;
        
        $favorite = Favorite::where('user_id', $userId)
            ->where('favoriteable_id', $postId)
            ->where('favoriteable_type', CustomerPost::class)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create([
                'user_id' => $userId,
                'favoriteable_id' => $postId,
                'favoriteable_type' => CustomerPost::class
            ]);
            return response()->json(['status' => 'added']);
        }
    }

    // Xử lý bình luận (chỉ cho customer posts)
    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $post = CustomerPost::findOrFail($postId);

        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::id();
        $comment->commentable_id = $post->id;
        $comment->commentable_type = CustomerPost::class;
        $comment->parent_id = $request->parent_id;
        $comment->save();

        return redirect()->route('customer.roommates.history');
            
    }

    // Cập nhật bình luận
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

        return redirect()->route('customer.roommates.history');
    }
}