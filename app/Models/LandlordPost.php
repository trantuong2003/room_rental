<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LandlordPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'address',
        'latitude', // Thêm vĩ độ
        'longitude', // Thêm kinh độ
        'acreage', // Đổi từ 'area' thành 'acreage'
        'bedrooms',
        'bathrooms',
        'electricity_price',
        'internet_price',
        'water_price', // Thêm giá tiền nước
        'service_price',
        'furniture',
        'utilities',
        'rejection_reason',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(LandlordPostImage::class, 'landlord_post_id');
    }

    // public function favoritedBy()
    // {
    //     return $this->hasMany(Favorite::class, 'post_id');
    // }

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class, 'post_id')->whereNull('parent_id')->latest();
    // }

    // Quan hệ lượt thích (Polymorphic)
    public function favoritedby()
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    // Quan hệ bình luận (Polymorphic)
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    /**
     * Tạo bài đăng mới và giảm số lượt đăng bài còn lại.
     */
    public static function createPost(array $data)
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if (!$subscription || !$subscription->canPost()) {
            throw new \Exception('Bạn không có lượt đăng bài hoặc gói đăng ký đã hết hạn.');
        }

        // Giảm số lượng bài đăng ngay lập tức khi tạo bài đăng
        $subscription->decrementRemainingPosts();

        $post = self::create($data);

        return $post;
    }

    /**
     * Khôi phục số bài đăng khi bài đăng bị từ chối.
     */
    public function restorePostCount()
    {
        $subscription = Subscription::where('user_id', $this->user_id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if ($subscription) {
            $subscription->incrementRemainingPosts();
        }
    }

    /**
     * tăng số bài đăng khi bài đăng bị từ chối được chấp thuận.
     */
    public function decrementPostCount()
    {
        $subscription = Subscription::where('user_id', $this->user_id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if ($subscription && $subscription->canPost()) {
            $subscription->decrementRemainingPosts();
        } else {
            throw new \Exception('Không thể duyệt bài đăng vì người dùng không còn lượt đăng bài.');
        }
    }
}
