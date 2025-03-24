<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;

class ModerationPostController extends Controller
{
    public function showPost(){
        {
            $posts = LandlordPost::with('images')->latest()->get();
            return view('admin.moderation_post', compact('posts'));
        }
    }

    public function approve($id)
    {   
        $post = LandlordPost::findOrFail($id);
        $post->update(['status' => 'approved']);
        return back()->with('success', 'Bài đăng đã được duyệt.');
    }
    
    public function reject($id)
    {
        $post = LandlordPost::findOrFail($id);
        $post->update(['status' => 'rejected']);
        return back()->with('error', 'Bài đăng đã bị từ chối.');
    }
    
}
