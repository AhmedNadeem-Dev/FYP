<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'total_rating',
        'rating_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'total_rating' => 'decimal:2',
        'rating_count' => 'integer',
    ];

    /**
     * Add a rating to this user
     */
    public function addRating($rating)
    {
        $this->total_rating += $rating;
        $this->rating_count += 1;
        $this->save();
        
        return $this;
    }

    /**
     * Get the average rating for this user
     */
    public function getAverageRatingAttribute()
    {
        if ($this->rating_count > 0) {
            return round($this->total_rating / $this->rating_count, 1);
        }
        return 0;
    }

    /**
     * Get products belonging to this user
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Check if user can rate another user
     */
    public function canRate($userId)
    {
        return $this->id != $userId;
    }

    /**
     * Get custom requests created by this user
     */
    public function customRequests()
    {
        return $this->hasMany(CustomRequest::class);
    }

    /**
     * Get custom requests accepted by this artist
     */
    public function acceptedRequests()
    {
        return $this->hasMany(CustomRequest::class, 'artist_id');
    }

    /**
     * Get orders placed by this user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get cart items for this user
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get wishlist items for this user
     */
    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Scope for filtering users by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for getting artists
     */
    public function scopeArtists($query)
    {
        return $query->where('role', 'artist');
    }

    /**
     * Scope for getting scrap sellers
     */
    public function scopeScrapSellers($query)
    {
        return $query->where('role', 'scrapSeller');
    }

    /**
     * Check if user is an artist
     */
    public function isArtist()
    {
        return $this->role === 'artist';
    }

    /**
     * Check if user is a scrap seller
     */
    public function isScrapSeller()
    {
        return $this->role === 'scrapSeller';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}