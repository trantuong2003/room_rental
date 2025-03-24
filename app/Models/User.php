<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Subscription;
use App\Models\Favourite;

class User extends Authenticatable  implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'government_id',
        'proof',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Kiểm tra vai trò của user
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class,  'user_id');
    }

    public function favorites()
{
    return $this->hasMany(Favorite::class);
}

}
