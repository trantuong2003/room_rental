<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\LandlordPost;

class Comment extends Model
{
    use HasFactory;

    // protected $fillable = ['post_id', 'user_id', 'content', 'parent_id'];
    protected $fillable = [
        'user_id',
        'content',
        'parent_id',
        'commentable_id',
        'commentable_type'
    ];

    // Lấy thông tin người bình luận
    // public function post()
    // {
    //     return $this->belongsTo(LandlordPost::class, 'post_id');
    // }
    // Mối quan hệ với người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ đa hình với bài đăng
    public function commentable()
    {
        return $this->morphTo();
    }

    // Mối quan hệ với các trả lời (nếu có)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    // Mối quan hệ với bình luận cha (nếu là trả lời)
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Scope để lọc theo loại bài đăng
    public function scopeForPost($query, $postType, $postId)
    {
        $model = $postType === 'landlord' ? LandlordPost::class : CustomerPost::class;
        return $query->where('commentable_id', $postId)
            ->where('commentable_type', $model);
    }
}
