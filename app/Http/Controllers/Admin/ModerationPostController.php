<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;

class ModerationPostController extends Controller
{
    public function showPost()
    { {
            $posts = LandlordPost::with('images')->latest()->get();
            return view('admin.moderation_post', compact('posts'));
        }
    }

    public function approve($id)
    {
        $post = LandlordPost::findOrFail($id);

        // Nếu bài đăng đã bị từ chối trước đó, hãy giảm số lượng bài đăng
        if ($post->status === 'rejected') {
            try {
                $post->decrementPostCount();
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        $post->update(['status' => 'approved', 'rejection_reason' => null]);

        return back()->with('success', 'Bài đăng đã được duyệt.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        $post = LandlordPost::findOrFail($id);

        // Nếu bài đăng chưa bị từ chối, hãy khôi phục số lượng bài đăng
        if ($post->status !== 'rejected') {
            $post->restorePostCount();
        }

        $post->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'Bài đăng đã bị từ chối với lý do: ' . $request->rejection_reason);
    }
}