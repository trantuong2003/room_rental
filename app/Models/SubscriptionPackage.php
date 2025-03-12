<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $fillable = ['package_name', 'price', 'duration_days', 'post_limit', 'description'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'package_id');
    }
}
