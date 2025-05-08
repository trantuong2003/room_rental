<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerPost;
use App\Models\Favorite;
use App\Models\Comment;
use App\Models\BannedWord;
use Illuminate\Support\Facades\Auth;

class CustomerPostController extends Controller
{
    // Hiển thị lịch sử bài đăng của người dùng
    public function history()
    {
        $posts = CustomerPost::with(['user', 'comments.user', 'favoritedby'])
        ->where('user_id', Auth::id())
        ->withCount(['comments', 'favoritedby as likes_count' => function ($query) {
            $query->where('favoriteable_type', CustomerPost::class);
        }])
        ->latest()
        ->get();

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

        return redirect()->route('customer.roommates.history')->with('success', 'Create post success!');
    }

    // Hiển thị form chỉnh sửa bài đăng
    public function edit($id)
    {
        $post = CustomerPost::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return redirect()->route('customer.roommates.history')
                ->with('error', 'You dont edit this post');
        }

        return view('customer.editpost_customer', compact('post'));
    }

    // Cập nhật bài đăng
    public function update(Request $request, CustomerPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('customer.roommates.history')
                ->with('error', 'You dont edit this post');
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
            ->with('success', 'Updated successfull!');
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
            ->with('success', 'Deleted post success!');
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

        // Lấy từ cấm từ database để đảm bảo luôn cập nhật
        $bannedWords = BannedWord::pluck('word')->toArray();
        $content = $request->content;

        // Kiểm tra từng từ cấm trong nội dung
        $foundBannedWords = [];
        foreach ($bannedWords as $word) {
            // Sử dụng regex để tìm chính xác từ cấm trong nội dung
            $pattern = '/\b' . preg_quote($word, '/') . '\b/ui'; // 'u' flag for UTF-8, 'i' for case-insensitive
            if (preg_match($pattern, $content)) {
                $foundBannedWords[] = $word;
            }
        }

        // Nếu tìm thấy từ cấm, trả về lỗi và KHÔNG lưu vào database
        if (!empty($foundBannedWords)) {
            session()->flash('error', 'Content contains inappropriate language: ' . implode(', ', $foundBannedWords));

            return redirect()->back()
                ->withErrors(['content' => 'Content contains inappropriate language: ' . implode(', ', $foundBannedWords)])
                ->withInput()
                ->with('failed_post_id', $postId);
        }

        // Chỉ lưu vào database nếu không có từ cấm
        $post = CustomerPost::findOrFail($postId);

        $comment = new Comment();
        $comment->content = $content;
        $comment->user_id = Auth::id();
        $comment->commentable_id = $post->id;
        $comment->commentable_type = CustomerPost::class;
        $comment->parent_id = $request->parent_id;
        $comment->save();

        return redirect()->back()->with('success', 'Comment sent successfully!');
    }
    private function checkForBannedWords($content, $bannedWords)
    {
        $foundWords = [];
        $content = mb_strtolower($content, 'UTF-8');

        foreach ($bannedWords as $word) {
            $lowerWord = mb_strtolower($word, 'UTF-8');
            if (preg_match('/\b' . preg_quote($lowerWord, '/') . '\b/u', $content)) {
                $foundWords[] = $word;
            }
        }

        return $foundWords;
    }
    public function updateComment(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // Lấy từ cấm từ database
        $bannedWords = BannedWord::pluck('word')->toArray();
        $content = $request->content;

        // Kiểm tra từng từ cấm
        $foundBannedWords = [];
        foreach ($bannedWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/ui';
            if (preg_match($pattern, $content)) {
                $foundBannedWords[] = $word;
            }
        }

        // Nếu tìm thấy từ cấm, KHÔNG update comment
        if (!empty($foundBannedWords)) {
            return redirect()->back()
                ->withErrors(['content' => 'Content contains inappropriate language: ' . implode(', ', $foundBannedWords)])
                ->withInput()
                ->with('comment_id', $commentId);
        }

        // Chỉ update comment nếu không có từ cấm
        $comment = Comment::where('id', $commentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $comment->content = $request->content;
        $comment->save();

        return redirect()->back()->with('success', 'Comment sent successfully!');
    }
}
