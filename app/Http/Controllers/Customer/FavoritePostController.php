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
use Illuminate\Support\Facades\Log;
use Exception;

class FavoritePostController extends Controller
{
    public function showFavorites(Request $request)
    {
        try {
            $user = Auth::user();
            $filter = $request->query('filter', 'landlord');

            Log::debug('showFavorites started', [
                'user_id' => $user->id,
                'filter' => $filter
            ]);

            $query = Favorite::where('user_id', $user->id)
                ->with([
                    'favoriteable' => function ($query) {
                        $query->with([
                            'user',
                            'comments' => function ($q) {
                                $q->whereNull('parent_id')->with(['user', 'replies.user']);
                            },
                            'favoritedby.user'
                        ]);
                    }
                ]);

            if ($filter === 'landlord') {
                $query->where('favoriteable_type', LandlordPost::class);
            } elseif ($filter === 'customer') {
                $query->where('favoriteable_type', CustomerPost::class);
            }

            $favorites = $query->get()
                ->filter(function ($favorite) {
                    return $favorite->favoriteable !== null;
                })
                ->map(function ($favorite) {
                    $post = $favorite->favoriteable;
                    $post->isFavorited = true;
                    $post->post_type = $favorite->favoriteable_type === LandlordPost::class ? 'landlord' : 'customer';
                    Log::debug('Favorite post loaded', [
                        'post_id' => $post->id,
                        'post_type' => $post->post_type,
                        'favoritedby' => $post->favoritedby ? $post->favoritedby->pluck('user_id')->toArray() : [],
                        'comments_count' => $post->comments->count()
                    ]);
                    return $post;
                });

            return view('customer.favorite', compact('favorites', 'filter'));
        } catch (Exception $e) {
            Log::error('Error in showFavorites', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('customer.favorite', ['favorites' => collect(), 'filter' => $filter]);
        }
    }

    public function toggleFavorite(Request $request)
    {
        try {
            $request->validate([
                'post_id' => 'required|integer',
                'post_type' => 'required|in:landlord,customer'
            ]);

            $userId = Auth::id();
            $postId = $request->post_id;
            $postType = $request->post_type;

            $model = $postType === 'landlord' ? LandlordPost::class : CustomerPost::class;

            Log::debug('toggleFavorite attempt', [
                'user_id' => $userId,
                'post_id' => $postId,
                'post_type' => $postType,
                'model' => $model
            ]);

            $favorite = Favorite::where('user_id', $userId)
                ->where('favoriteable_id', $postId)
                ->where('favoriteable_type', $model)
                ->first();

            if ($favorite) {
                $favorite->delete();
                Log::debug('Favorite removed', ['post_id' => $postId, 'user_id' => $userId]);
                return response()->json(['status' => 'removed']);
            } else {
                Favorite::create([
                    'user_id' => $userId,
                    'favoriteable_id' => $postId,
                    'favoriteable_type' => $model
                ]);
                Log::debug('Favorite added', ['post_id' => $postId, 'user_id' => $userId]);
                return response()->json(['status' => 'added']);
            }
        } catch (Exception $e) {
            Log::error('Error in toggleFavorite', [
                'error' => $e->getMessage(),
                'post_id' => $request->post_id,
                'post_type' => $request->post_type,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Unable to perform favorite action: ' . $e->getMessage()], 500);
        }
    }

    public function toggleLike(Request $request)
    {
        try {
            $request->validate([
                'post_id' => 'required|integer',
                'post_type' => 'required|in:customer'
            ]);

            $userId = Auth::id();
            $postId = $request->post_id;

            $post = CustomerPost::find($postId);
            if (!$post) {
                throw new Exception('Bài đăng không tồn tại');
            }

            $model = CustomerPost::class;

            Log::debug('toggleLike attempt', [
                'user_id' => $userId,
                'post_id' => $postId,
                'model' => $model
            ]);

            $favorite = Favorite::where('user_id', $userId)
                ->where('favoriteable_id', $postId)
                ->where('favoriteable_type', $model)
                ->first();

            if ($favorite) {
                $favorite->delete();
                $status = 'removed';
            } else {
                Favorite::create([
                    'user_id' => $userId,
                    'favoriteable_id' => $postId,
                    'favoriteable_type' => $model
                ]);
                $status = 'added';
            }

            $likesCount = Favorite::where('favoriteable_id', $postId)
                ->where('favoriteable_type', $model)
                ->count();

            Log::debug('toggleLike processed', [
                'post_id' => $postId,
                'user_id' => $userId,
                'status' => $status,
                'likes_count' => $likesCount
            ]);

            return response()->json([
                'status' => $status,
                'likes_count' => $likesCount
            ]);
        } catch (Exception $e) {
            Log::error('Error in toggleLike', [
                'error' => $e->getMessage(),
                'post_id' => $request->post_id,
                'post_type' => $request->post_type,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Unable to perform favorite action: ' . $e->getMessage()], 500);
        }
    }

    public function storeComment(Request $request, $postId)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
                'parent_id' => 'nullable|exists:comments,id',
                'post_type' => 'required|in:customer'
            ]);

            $bannedWords = BannedWord::pluck('word')->toArray();
            $content = $request->content;

            Log::debug('storeComment attempt', [
                'post_id' => $postId,
                'user_id' => Auth::id(),
                'content' => $content,
                'parent_id' => $request->parent_id
            ]);

            $foundBannedWords = $this->checkForBannedWords($content, $bannedWords);

            if (!empty($foundBannedWords)) {
                Log::warning('Banned words detected in comment', ['words' => $foundBannedWords]);
                return redirect()->back()
                    ->withErrors(['content' => 'Content contains inappropriate language: ' . implode(', ', $foundBannedWords)])
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

            Log::debug('Comment stored', ['comment_id' => $comment->id, 'post_id' => $postId]);

            return redirect()->back()->with('success', 'Comment sent successfully!');
        } catch (Exception $e) {
            Log::error('Error in storeComment', [
                'error' => $e->getMessage(),
                'post_id' => $postId,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['content' => 'Unable to save comment: ' . $e->getMessage()]);
        }
    }

    public function updateComment(Request $request, $commentId)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000'
            ]);

            $bannedWords = BannedWord::pluck('word')->toArray();
            $content = $request->content;

            Log::debug('updateComment attempt', [
                'comment_id' => $commentId,
                'user_id' => Auth::id(),
                'content' => $content
            ]);

            $foundBannedWords = $this->checkForBannedWords($content, $bannedWords);

            if (!empty($foundBannedWords)) {
                Log::warning('Banned words detected in comment update', ['words' => $foundBannedWords]);
                return redirect()->back()
                    ->withErrors(['content' => 'Content contains inappropriate language: ' . implode(', ', $foundBannedWords)])
                    ->withInput()
                    ->with('comment_id', $commentId);
            }

            $comment = Comment::where('id', $commentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $comment->content = $content;
            $comment->save();

            Log::debug('Comment updated', ['comment_id' => $commentId]);

            return redirect()->back()->with('success', 'Comment sent successfully!');
        } catch (Exception $e) {
            Log::error('Error in updateComment', [
                'error' => $e->getMessage(),
                'comment_id' => $commentId,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['content' => 'Unable to save comment: ' . $e->getMessage()]);
        }
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
}