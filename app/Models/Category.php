<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = ['name'];

    /**
     * Get all products belonging to this category
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    /**
     * Scope for getting categories with product count
     */
    public function scopeWithProductCount($query)
    {
        return $query->withCount('products');
    }

    /**
     * Scope for getting categories that have products
     */
    public function scopeHasProducts($query)
    {
        return $query->has('products');
    }
}
