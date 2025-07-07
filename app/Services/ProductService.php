<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class ProductService
{
    protected $cachePrefix = 'products:';
    protected $cacheTTL = 3600; // 1 hour

    /**
     * Get all products with caching
     */
    public function getAllProducts($page = 1, $perPage = 20)
    {
        $cacheKey = $this->cachePrefix . "all:page:{$page}:per_page:{$perPage}";
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($perPage) {
            return Product::with(['images', 'category', 'user'])
                ->latest()
                ->paginate($perPage);
        });
    }

    /**
     * Get products by category with caching
     */
    public function getProductsByCategory($categoryId, $page = 1, $perPage = 20)
    {
        $cacheKey = $this->cachePrefix . "category:{$categoryId}:page:{$page}:per_page:{$perPage}";
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($categoryId, $perPage) {
            return Product::byCategory($categoryId)
                ->withImages()
                ->with(['category', 'user'])
                ->latest()
                ->paginate($perPage);
        });
    }

    /**
     * Get products by user with caching
     */
    public function getUserProducts($userId, $page = 1, $perPage = 20)
    {
        $cacheKey = $this->cachePrefix . "user:{$userId}:page:{$page}:per_page:{$perPage}";
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($userId, $perPage) {
            return Product::byUser($userId)
                ->withImages()
                ->with(['category'])
                ->latest()
                ->paginate($perPage);
        });
    }

    /**
     * Get single product with caching
     */
    public function getProduct($id)
    {
        $cacheKey = $this->cachePrefix . "single:{$id}";
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($id) {
            return Product::with(['images', 'category', 'user', 'comments.user'])
                ->find($id);
        });
    }

    /**
     * Search products with caching
     */
    public function searchProducts($query, $filters = [], $page = 1, $perPage = 20)
    {
        $cacheKey = $this->cachePrefix . "search:" . md5($query . serialize($filters)) . ":page:{$page}";
        
        return Cache::remember($cacheKey, $this->cacheTTL / 2, function () use ($query, $filters, $perPage) {
            $productsQuery = Product::with(['images', 'category', 'user']);
            
            // Search in name and description
            if ($query) {
                $productsQuery->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                });
            }
            
            // Apply filters
            if (isset($filters['category_id'])) {
                $productsQuery->byCategory($filters['category_id']);
            }
            
            if (isset($filters['min_price'])) {
                $productsQuery->priceRange($filters['min_price'], $filters['max_price'] ?? null);
            }
            
            if (isset($filters['user_id'])) {
                $productsQuery->byUser($filters['user_id']);
            }
            
            return $productsQuery->latest()->paginate($perPage);
        });
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data, array $images = [])
    {
        return DB::transaction(function () use ($data, $images) {
            $product = Product::create($data);
            
            if (!empty($images)) {
                $this->handleImageUploads($product, $images);
            }
            
            // Clear related caches
            $this->clearProductCaches($product);
            
            return $product;
        });
    }

    /**
     * Update a product
     */
    public function updateProduct($id, array $data, array $images = [])
    {
        return DB::transaction(function () use ($id, $data, $images) {
            $product = Product::findOrFail($id);
            $product->update($data);
            
            if (!empty($images)) {
                $this->handleImageUploads($product, $images);
            }
            
            // Clear related caches
            $this->clearProductCaches($product);
            
            return $product;
        });
    }

    /**
     * Delete a product
     */
    public function deleteProduct($id)
    {
        return DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            
            // Delete associated images from storage
            foreach ($product->images as $image) {
                $imagePath = public_path('images/' . $image->image_path);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            // Delete product (images will be deleted via cascade)
            $product->delete();
            
            // Clear related caches
            $this->clearProductCaches($product);
            
            return true;
        });
    }

    /**
     * Handle image uploads
     */
    protected function handleImageUploads(Product $product, array $images)
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile && $image->isValid()) {
                // Additional security checks
                if ($image->getSize() > 2048 * 1024) { // 2MB
                    throw new \Exception('Image file too large');
                }
                
                // Sanitize filename
                $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '', $image->getClientOriginalName());
                $filename = uniqid() . '_' . time() . '_' . $originalName;
                
                // Move file
                $image->move(public_path('images'), $filename);
                
                // Create database record
                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $filename,
                ]);
            }
        }
    }

    /**
     * Clear product-related caches
     */
    protected function clearProductCaches(Product $product)
    {
        // Clear specific product cache
        Cache::forget($this->cachePrefix . "single:{$product->product_id}");
        
        // Clear category-related caches
        Cache::forget($this->cachePrefix . "category:{$product->category_id}:*");
        
        // Clear user-related caches
        Cache::forget($this->cachePrefix . "user:{$product->user_id}:*");
        
        // Clear general product caches
        Cache::forget($this->cachePrefix . "all:*");
        
        // Clear search caches (this is more aggressive but ensures consistency)
        $this->clearSearchCaches();
    }

    /**
     * Clear search caches
     */
    protected function clearSearchCaches()
    {
        // Get all cache keys that start with our search prefix
        $searchPrefix = $this->cachePrefix . "search:";
        
        // Note: This is a simplified approach. In production, you might want to use
        // cache tags or a more sophisticated cache invalidation strategy
        Cache::flush(); // This clears all cache - use with caution in production
    }

    /**
     * Get popular products
     */
    public function getPopularProducts($limit = 10)
    {
        $cacheKey = $this->cachePrefix . "popular:limit:{$limit}";
        
        return Cache::remember($cacheKey, $this->cacheTTL * 2, function () use ($limit) {
            return Product::select('products.*')
                ->leftJoin('order_items', 'products.product_id', '=', 'order_items.product_id')
                ->groupBy('products.product_id')
                ->orderByRaw('COUNT(order_items.id) DESC')
                ->with(['images', 'category', 'user'])
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get recent products
     */
    public function getRecentProducts($days = 7, $limit = 10)
    {
        $cacheKey = $this->cachePrefix . "recent:days:{$days}:limit:{$limit}";
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($days, $limit) {
            return Product::recent($days)
                ->withImages()
                ->with(['category', 'user'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }
} 