<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Review;
use App\Models\Wishlist;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

public function orders(): HasMany
{
    return $this->hasMany(Order::class)->latest();
}

public function cart(): HasOne
{
    return $this->hasOne(Cart::class)->latestOfMany();
}

public function addresses(): HasMany
{
    return $this->hasMany(Address::class);
}

public function defaultAddress(): HasOne
{
    return $this->hasOne(Address::class)->where('is_default', true);
}

public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}

public function wishlist(): HasMany
{
    return $this->hasMany(Wishlist::class);
}
}
