<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    /**
     * Get recommended products for the homepage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendedProducts(Request $request)
    {
        // Get limit from request or default to 8 products
        $limit = $request->input('limit', 8);
        
        // If user is authenticated, get personalized recommendations
        if (Auth::check()) {
            return $this->getPersonalizedRecommendations($limit);
        }
        
        // For guest users, return popular products
        return $this->getPopularProducts($limit);
    }
    
    /**
     * Get personalized product recommendations for logged-in users
     * 
     * @param int $limit
     * @return \Illuminate\Http\JsonResponse
     */
    private function getPersonalizedRecommendations($limit)
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Get user's cart categories
        $cartCategories = $this->getUserCartCategories($userId);
        
        // Get user's wishlist categories
        $wishlistCategories = $this->getUserWishlistCategories($userId);
        
        // Combine categories from both sources
        $userCategories = array_unique(array_merge($cartCategories, $wishlistCategories));
        
        // If no history found, return popular products
        if (empty($userCategories)) {
            return $this->getPopularProducts($limit);
        }
        
        // Get products from the same categories, excluding ones the user already has in cart/wishlist
        $recommendedProducts = Product::with(['category', 'images'])
            ->whereIn('category_id', $userCategories)
            ->whereNotIn('product_id', function($query) use ($userId) {
                $query->select('product_id')
                    ->from('carts')
                    ->where('user_id', $userId);
            })
            ->whereNotIn('product_id', function($query) use ($userId) {
                $query->select('product_id')
                    ->from('wishlists')
                    ->where('user_id', $userId);
            })
            ->inRandomOrder() // Randomize results for variety
            ->take($limit)
            ->get();
            
        // If not enough products found, fill with popular products
        if ($recommendedProducts->count() < $limit) {
            $additionalProductsNeeded = $limit - $recommendedProducts->count();
            $existingProductIds = $recommendedProducts->pluck('product_id')->toArray();
            
            $additionalProducts = $this->getAdditionalProducts($userId, $existingProductIds, $additionalProductsNeeded);
            $recommendedProducts = $recommendedProducts->concat($additionalProducts);
        }
        
        return $this->formatProductResponse($recommendedProducts);
    }
    
    /**
     * Get user's cart product categories
     * 
     * @param int $userId
     * @return array
     */
    private function getUserCartCategories($userId)
    {
        try {
            return DB::table('products')
                ->join('carts', 'products.product_id', '=', 'carts.product_id')
                ->where('carts.user_id', $userId)
                ->pluck('products.category_id')
                ->unique()
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error fetching user cart categories: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user's wishlist product categories
     * 
     * @param int $userId
     * @return array
     */
    private function getUserWishlistCategories($userId)
    {
        try {
            return DB::table('products')
                ->join('wishlists', 'products.product_id', '=', 'wishlists.product_id')
                ->where('wishlists.user_id', $userId)
                ->pluck('products.category_id')
                ->unique()
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error fetching user wishlist categories: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get additional products when not enough recommendations found
     * 
     * @param int $userId
     * @param array $existingProductIds
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAdditionalProducts($userId, $existingProductIds, $limit)
    {
        return Product::with(['category', 'images'])
            ->whereNotIn('product_id', $existingProductIds)
            ->whereNotIn('product_id', function($query) use ($userId) {
                $query->select('product_id')
                    ->from('carts')
                    ->where('user_id', $userId);
            })
            ->whereNotIn('product_id', function($query) use ($userId) {
                $query->select('product_id')
                    ->from('wishlists')
                    ->where('user_id', $userId);
            })
            ->orderBy(DB::raw('RAND()')) // Random order
            ->take($limit)
            ->get();
    }
    
    /**
     * Get popular products based on various metrics
     * 
     * @param int $limit
     * @return \Illuminate\Http\JsonResponse
     */
    private function getPopularProducts($limit)
    {
        $popStrategy = rand(1, 3); // Randomly choose one of three strategies for variety
        
        switch ($popStrategy) {
            case 1:
                // Strategy 1: Products from popular categories
                $popularCategoryIds = DB::table('products')
                    ->join('carts', 'products.product_id', '=', 'carts.product_id')
                    ->select('products.category_id', DB::raw('COUNT(*) as count'))
                    ->groupBy('products.category_id')
                    ->orderBy('count', 'desc')
                    ->limit(3)
                    ->pluck('category_id')
                    ->toArray();
                
                if (!empty($popularCategoryIds)) {
                    $popularProducts = Product::with(['category', 'images'])
                        ->whereIn('category_id', $popularCategoryIds)
                        ->inRandomOrder()
                        ->take($limit)
                        ->get();
                        
                    if ($popularProducts->count() == $limit) {
                        return $this->formatProductResponse($popularProducts);
                    }
                }
                // Fall through to next strategy if not enough products found
                
            case 2:
                // Strategy 2: Most wishlisted products
                $wishlistedProductIds = DB::table('wishlists')
                    ->select('product_id', DB::raw('COUNT(*) as count'))
                    ->groupBy('product_id')
                    ->orderBy('count', 'desc')
                    ->limit($limit * 2) // Get more than needed to ensure enough unique products
                    ->pluck('product_id')
                    ->toArray();
                
                if (!empty($wishlistedProductIds)) {
                    $popularProducts = Product::with(['category', 'images'])
                        ->whereIn('product_id', $wishlistedProductIds)
                        ->inRandomOrder()
                        ->take($limit)
                        ->get();
                        
                    if ($popularProducts->count() == $limit) {
                        return $this->formatProductResponse($popularProducts);
                    }
                }
                // Fall through to default strategy if not enough products found
                
            default:
                // Strategy 3: Mix of recent and random products for discovery
                $recentLimit = ceil($limit / 2); // Half of the products will be recent
                $randomLimit = $limit - $recentLimit; // Rest will be random selections
                
                // Get recent products
                $recentProducts = Product::with(['category', 'images'])
                    ->orderBy('created_at', 'desc')
                    ->take($recentLimit)
                    ->get();
                
                // Get random products, excluding recent ones
                $recentIds = $recentProducts->pluck('product_id')->toArray();
                $randomProducts = Product::with(['category', 'images'])
                    ->whereNotIn('product_id', $recentIds)
                    ->inRandomOrder()
                    ->take($randomLimit)
                    ->get();
                
                // Combine the collections
                $popularProducts = $recentProducts->concat($randomProducts);
                
                return $this->formatProductResponse($popularProducts);
        }
    }
    
    /**
     * Format product data for frontend consumption
     * 
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return \Illuminate\Http\JsonResponse
     */
    private function formatProductResponse($products)
    {
        $response = $products->map(function ($product) {
            return [
                'id' => $product->product_id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'category' => $product->category ? $product->category->name : null,
                'images' => $product->images->map(function ($image) {
                    return asset('storage/' . $image->image_path);
                }),
            ];
        });
        
        return response()->json($response, 200);
    }
} 