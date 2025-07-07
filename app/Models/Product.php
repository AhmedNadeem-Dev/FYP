<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; 
    protected $primaryKey = 'product_id'; 

    protected $fillable = [
        'name', 'description', 'price', 'category_id', 'user_id', 'additional_info'
    ];
   protected $appends = ['category_name'];
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Add relationship to category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'product_id', 'product_id');
    }
      public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get all order items containing this product
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }
    // Accessor to get category name easily
    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->name : null;
    }

    /**
     * Scope for filtering products by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for filtering products by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering products by price range
     */
    public function scopePriceRange($query, $minPrice, $maxPrice = null)
    {
        $query->where('price', '>=', $minPrice);
        
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        
        return $query;
    }

    /**
     * Scope for getting products with images
     */
    public function scopeWithImages($query)
    {
        return $query->with('images');
    }

    /**
     * Scope for getting recently created products
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
