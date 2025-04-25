<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\LandlordPost;
use App\Models\CustomerPost;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Lưu bình luận hoặc trả lời bình luận
     */
    // public function store(Request $request,  LandlordPost $post)
    // {
    //     $request->validate([
    //         'content' => 'required|string|max:1000',
    //         'parent_id' => 'nullable|exists:comments,id'
    //     ]);

    //     $user = Auth::user();
    //     $parentComment = $request->parent_id ? Comment::find($request->parent_id) : null;

    //     // Kiểm tra quyền bình luận
    //     if ($user->role === 'landlord') {
    //         if (!$request->parent_id && $post->user_id === $user->id) {
    //             return back()->with('error', 'Bạn không thể bình luận vào bài viết của chính mình.');
    //         }

    //         if ($parentComment && $parentComment->user->role !== 'customer') {
    //             return back()->with('error', 'Bạn chỉ có thể trả lời bình luận của người thuê.');
    //         }
    //     }

    //     $comment = new Comment([
    //         'user_id' => $user->id,
    //         'content' => $request->content,
    //         'parent_id' => $request->parent_id,
    //     ]);

    //     $post->comments()->save($comment);

    //     return back()->with('success', 'Bình luận đã được thêm thành công.');
    // }

    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
            'post_type' => 'required|in:landlord,customer'
        ]);

        $user = Auth::user();
        $post = $request->post_type === 'landlord'
            ? LandlordPost::findOrFail($postId)
            : CustomerPost::findOrFail($postId);

        $parentComment = $request->parent_id ? Comment::find($request->parent_id) : null;

        // Kiểm tra quyền bình luận
        if ($user->role === 'landlord') {
            if (!$request->parent_id && $post->user_id === $user->id) {
                return back()->with('error', 'Bạn không thể bình luận vào bài viết của chính mình.');
            }

            if ($parentComment && $parentComment->user->role !== 'customer') {
                return back()->with('error', 'Bạn chỉ có thể trả lời bình luận của người thuê.');
            }
        }

        $comment = new Comment([
            'user_id' => $user->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        $post->comments()->save($comment);

        return back()->with('success', 'Bình luận đã được thêm thành công.');
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
