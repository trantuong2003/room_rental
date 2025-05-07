<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class CustomerPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
        'rejection_reason',
        'approved_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class);
    // }

    // public function likes()
    // {
    //     return $this->hasMany(Like::class);
    // }
    public function favoritedby()
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
