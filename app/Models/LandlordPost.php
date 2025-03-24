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

    public function favoritedBy()
    {
        return $this->hasMany(Favorite::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id')->whereNull('parent_id')->latest();
    }
    /**
     * Tạo bài đăng mới và giảm số lượt đăng bài còn lại.
     */
    public static function createPost(array $data)
    {
        $user = Auth::user();
        // $subscription = $user->subscriptions()->active()->first();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();


        if (!$subscription || !$subscription->canPost()) {
            throw new \Exception('Bạn không có lượt đăng bài hoặc gói đăng ký đã hết hạn.');
        }

        $post = self::create($data);
        $subscription->decrementRemainingPosts();

        return $post;
    }
}
