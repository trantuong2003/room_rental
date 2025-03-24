<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordPostImage extends Model
{
    use HasFactory;

    protected $fillable = ['landlord_post_id', 'image_path'];

    /**
     * Một ảnh thuộc về một bài đăng.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(LandlordPost::class, 'landlord_post_id');
    }
}
