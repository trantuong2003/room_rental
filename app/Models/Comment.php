<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\LandlordPost;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'user_id', 'content', 'parent_id'];

    // Lấy thông tin người bình luận
    public function post()
    {
        return $this->belongsTo(LandlordPost::class, 'post_id');
    }
    // Mối quan hệ với người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
