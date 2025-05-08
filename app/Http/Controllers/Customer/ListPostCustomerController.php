<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use App\Models\Favorite;
use App\Models\Comment;
use App\Models\CustomerPost;
use App\Models\BannedWord;
use Illuminate\Support\Facades\Auth;

class ListPostCustomerController extends Controller
{
     // Hiển thị danh sách bài đăng     
     public function index()
     {
        $posts = CustomerPost::with(['user', 'comments.user', 'favoritedby'])
        ->where('status', 'approved')
        ->withCount(['comments', 'favoritedby as likes_count' => function ($query) {
            $query->where('favoriteable_type', CustomerPost::class);
        }])
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

        return redirect()->back()->with('success', 'Comment updated successfully!');
    }
}
