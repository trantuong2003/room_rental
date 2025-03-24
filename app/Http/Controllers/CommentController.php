<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\LandlordPost;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Lưu bình luận hoặc trả lời bình luận
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $post = LandlordPost::findOrFail($postId);
        $user = Auth::user();
        $parentComment = $request->parent_id ? Comment::find($request->parent_id) : null;

        // Kiểm tra quyền bình luận
        if ($user->role === 'landlord' && !$request->parent_id) {
            return back()->with('error', 'Landlord không thể bình luận bài viết của chính mình.');
        }

        if ($user->role === 'landlord' && $parentComment && $parentComment->user->role !== 'customer') {
            return back()->with('error', 'Landlord chỉ có thể trả lời bình luận của Customer.');
        }

        Comment::create([
            'post_id' => $postId,
            'user_id' => $user->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Bình luận đã được thêm.');
    
    }

    /**
     * Cập nhật bình luận (chỉ user sở hữu bình luận có thể sửa)
     */
    public function update(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::findOrFail($commentId);
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return back()->with('error', 'Bạn không có quyền sửa bình luận này.');
        }

        $comment->update(['content' => $request->content]);

        return back()->with('success', 'Bình luận đã được cập nhật.');
    }

}
