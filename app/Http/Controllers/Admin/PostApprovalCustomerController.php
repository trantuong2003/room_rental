<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerPost;

class PostApprovalCustomerController extends Controller
{
    public function index()
    {
        $posts = CustomerPost::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.approval_customer_post', compact('posts'));
    }

    public function approve($id)
    {
        $post = CustomerPost::findOrFail($id);
        $post->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null
        ]);
        
        return back()->with('success', 'Bài đăng đã được duyệt');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        $post = CustomerPost::findOrFail($id);
        $post->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);
        
        return back()->with('success', 'Bài đăng đã bị từ chối');
    }
}
