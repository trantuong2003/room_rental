<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Favorite extends Model
{
    use HasFactory;

    // protected $fillable = ['user_id', 'post_id'];
    protected $fillable = [
        'user_id',
        'favoriteable_id',
        'favoriteable_type'
    ];

    public function post()
    {
        return $this->belongsTo(LandlordPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ đa hình (dùng cho cả landlord_post và customer_post)
    public function favoriteable()
    {
        return $this->morphTo();
    }
    // // Kiểm tra xem user đã thích bài đăng chưa
    // public static function isFavorited($userId, $postId, $postType)
    // {
    //     $model = $postType === 'landlord' ? LandlordPost::class : CustomerPost::class;

    //     return static::where('user_id', $userId)
    //         ->where('favoriteable_id', $postId)
    //         ->where('favoriteable_type', $model)
    //         ->exists();
    // }
}
