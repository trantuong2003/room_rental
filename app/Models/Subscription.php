<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscription_packages';
    protected $fillable = ['package_name', 'price', 'duration_days', 'post_limit', 'description'];
}
