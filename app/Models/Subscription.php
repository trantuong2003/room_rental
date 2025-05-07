<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'package_id', 'payment_id', 'start_date', 'end_date', 'remaining_posts', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'package_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')->orWhere('end_date', '<', now());
    }

    // Kiểm tra xem người dùng có thể đăng bài hay không
    public function canPost()
    {
        return $this->remaining_posts > 0 && $this->status == 'active' && $this->end_date >= now();
    }

    // Giảm số lượt đăng bài
    public function decrementRemainingPosts()
    {
        $this->decrement('remaining_posts');
        if ($this->remaining_posts <= 0) {
            $this->update(['status' => 'expired']);
        }
    }

    // Tăng số lượt đăng bài
    public function incrementRemainingPosts()
    {
        $this->increment('remaining_posts');
        // Đảm bảo trạng-thái được cập nhật thành active nếu cần
        if ($this->status == 'expired' && $this->remaining_posts > 0 && $this->end_date >= now()) {
            $this->update(['status' => 'active']);
        }
    }
}
