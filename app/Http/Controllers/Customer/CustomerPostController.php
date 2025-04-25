<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerPost;

use Illuminate\Support\Facades\Auth;

class CustomerPostController extends Controller
{
    // Hiển thị danh sách bài đăng 
    // public function index()
    // {
    //     $posts = CustomerPost::with('user')->where('status', 'approved')->latest()->paginate(10);;
    //     return view('customer.list_customer_post', compact('posts'));

    // }

    // public function history()
    // {
    //     $posts = CustomerPost::where('user_id', Auth::id())->latest()->paginate(10);;
    //     return view('customer.customer_history_post', compact('posts'));
    // }
 

    public function history()
    {
        $posts = CustomerPost::where('user_id', Auth::id())
            ->withCount(['comments', 'favoritedby'])
            ->latest()
            ->paginate(10);

        return view('customer.customer_history_post', compact('posts'));
    }

    // Hiển thị form tạo bài đăng
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

        return redirect()->route('customer.roommates.history')->with('success', 'Bài đăng đã được tạo!');
    }

    // Hiển thị form sửa bài đăng
    public function edit($id)
    {
        $post = CustomerPost::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return redirect()->route('customer.roommates.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa bài đăng này');
        }

        return view('customer.editpost_customer', compact('post'));
    }

    // Cập nhật bài đăng
    public function update(Request $request, CustomerPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('customer.roommates.index')->with('error', 'Bạn không có quyền chỉnh sửa bài này!');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('customer.roommates.history')->with('success', 'Bài đăng đã được cập nhật!');
    }


    // Xóa bài đăng
    public function destroy(CustomerPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $post->delete();
        return redirect()->route('customer.roommates.history')->with('success', 'Bài đăng đã được xóa!');
    }


}
