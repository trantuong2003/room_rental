<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\LandlordPost;
use App\Models\CustomerPost;
use App\Models\Comment;
use App\Models\BannedWord;

class FavoritePostController extends Controller
{
    public function showFavorites(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'all'); // Lấy tham số filter, mặc định là 'all'

        // Lấy danh sách yêu thích
        $query = Favorite::where('user_id', $user->id)
            ->with([
                'favoriteable' => function ($query) {
                    // Tùy chỉnh mối quan hệ theo loại bài đăng
                    $query->when($query->getModel() instanceof LandlordPost, function ($q) {
                        $q->with('images'); // Chỉ tải images cho LandlordPost
                    })->with('user', 'comments.user', 'favoritedby');
                }
            ]);

        // Lọc theo loại bài đăng
        if ($filter === 'landlord') {
            $query->where('favoriteable_type', LandlordPost::class);
        } elseif ($filter === 'customer') {
            $query->where('favoriteable_type', CustomerPost::class);
        }

        $favorites = $query->get()
            ->filter(function ($favorite) {
                return $favorite->favoriteable !== null; // Loại bỏ các bản ghi có favoriteable null
            })
            ->map(function ($favorite) {
                $post = $favorite->favoriteable;
                $post->isFavorited = true; // Gán isFavorited
                $post->post_type = $favorite->favoriteable_type === LandlordPost::class ? 'landlord' : 'customer';
                return $post;
            });

        return view('customer.favorite', compact('favorites', 'filter'));
    }

    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer',
            'post_type' => 'required|in:landlord,customer'
        ]);

        $userId = Auth::id();
        $postId = $request->post_id;
        $postType = $request->post_type;
        
        $model = $postType === 'landlord' ? LandlordPost::class : CustomerPost::class;

        // Kiểm tra xem bài đăng có tồn tại không
        $post = $model::find($postId);
        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Bài đăng không tồn tại.']);
        }

        $favorite = Favorite::where('user_id', $userId)
            ->where('favoriteable_id', $postId)
            ->where('favoriteable_type', $model)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed', 'likes_count' => Favorite::where('favoriteable_id', $postId)->where('favoriteable_type', $model)->count()]);
        } else {
            Favorite::create([
                'user_id' => $userId,
                'favoriteable_id' => $postId,
                'favoriteable_type' => $model
            ]);
            return response()->json(['status' => 'added', 'likes_count' => Favorite::where('favoriteable_id', $postId)->where('favoriteable_type', $model)->count()]);
        }
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $bannedWords = BannedWord::pluck('word')->toArray();
        $content = $request->content;

        $foundBannedWords = [];
        foreach ($bannedWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/ui';
            if (preg_match($pattern, $content)) {
                $foundBannedWords[] = $word;
            }
        }

        if (!empty($foundBannedWords)) {
            session()->flash('error', 'Nội dung chứa từ ngữ không phù hợp: ' . implode(', ', $foundBannedWords));
            return redirect()->back()
                ->withErrors(['content' => 'Nội dung chứa từ ngữ không phù hợp: ' . implode(', ', $foundBannedWords)])
                ->withInput()
                ->with('failed_post_id', $postId);
        }

        $post = CustomerPost::findOrFail($postId);

        $comment = new Comment();
        $comment->content = $content;
        $comment->user_id = Auth::id();
        $comment->commentable_id = $post->id;
        $comment->commentable_type = CustomerPost::class;
        $comment->parent_id = $request->parent_id;
        $comment->save();

        return redirect()->back()->with('success', 'Bình luận đã được gửi thành công!');
    }

    public function updateComment(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $bannedWords = BannedWord::pluck('word')->toArray();
        $content = $request->content;

        $foundBannedWords = [];
        foreach ($bannedWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/ui';
            if (preg_match($pattern, $content)) {
                $foundBannedWords[] = $word;
            }
        }

        if (!empty($foundBannedWords)) {
            return redirect()->back()
                ->withErrors(['content' => 'Nội dung chứa từ ngữ không phù hợp: ' . implode(', ', $foundBannedWords)])
                ->withInput()
                ->with('comment_id', $commentId);
        }

        $comment = Comment::where('id', $commentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $comment->content = $request->content;
        $comment->save();

        return redirect()->back()->with('success', 'Bình luận đã được cập nhật thành công!');
    }
}