<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    protected $defaultTTL = 3600; // 1 hour
    protected $longTTL = 86400; // 24 hours
    protected $shortTTL = 300; // 5 minutes

    /**
     * Get cache with automatic fallback
     */
    public function remember($key, $callback, $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTTL;
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Cache user session data
     */
    public function cacheUserSession($userId, $data, $ttl = null)
    {
        $ttl = $ttl ?? $this->longTTL;
        $key = "user_session:{$userId}";
        
        return Cache::put($key, $data, $ttl);
    }

    /**
     * Get cached user session
     */
    public function getUserSession($userId)
    {
        $key = "user_session:{$userId}";
        return Cache::get($key);
    }

    /**
     * Cache API response
     */
    public function cacheApiResponse($endpoint, $params, $data, $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTTL;
        $key = "api:" . md5($endpoint . serialize($params));
        
        return Cache::put($key, $data, $ttl);
    }

    /**
     * Get cached API response
     */
    public function getCachedApiResponse($endpoint, $params)
    {
        $key = "api:" . md5($endpoint . serialize($params));
        return Cache::get($key);
    }

    /**
     * Cache database query results
     */
    public function cacheQuery($query, $bindings, $result, $ttl = null)
    {
        $ttl = $ttl ?? $this->shortTTL;
        $key = "query:" . md5($query . serialize($bindings));
        
        return Cache::put($key, $result, $ttl);
    }

    /**
     * Get cached query result
     */
    public function getCachedQuery($query, $bindings)
    {
        $key = "query:" . md5($query . serialize($bindings));
        return Cache::get($key);
    }

    /**
     * Clear cache by pattern
     */
    public function clearByPattern($pattern)
    {
        if (config('cache.default') === 'redis') {
            $redis = Redis::connection();
            $keys = $redis->keys($pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // For other cache drivers, we'll need to clear all cache
            // This is not ideal but necessary for drivers that don't support pattern matching
            Cache::flush();
        }
    }

    /**
     * Clear user-related caches
     */
    public function clearUserCaches($userId)
    {
        $patterns = [
            "user_session:{$userId}",
            "products:user:{$userId}:*",
            "orders:user:{$userId}:*",
            "cart:user:{$userId}:*",
            "wishlist:user:{$userId}:*"
        ];

        foreach ($patterns as $pattern) {
            $this->clearByPattern($pattern);
        }
    }

    /**
     * Clear product-related caches
     */
    public function clearProductCaches($productId = null, $categoryId = null)
    {
        $patterns = [
            "products:all:*",
            "products:popular:*",
            "products:recent:*",
            "products:search:*"
        ];

        if ($productId) {
            $patterns[] = "products:single:{$productId}";
        }

        if ($categoryId) {
            $patterns[] = "products:category:{$categoryId}:*";
        }

        foreach ($patterns as $pattern) {
            $this->clearByPattern($pattern);
        }
    }

    /**
     * Warm up cache with frequently accessed data
     */
    public function warmUpCache()
    {
        // This would typically be run via a scheduled job
        
        // Warm up popular products
        $this->remember('products:popular:limit:10', function () {
            return app(ProductService::class)->getPopularProducts(10);
        }, $this->longTTL);
        
        // Warm up recent products
        $this->remember('products:recent:days:7:limit:10', function () {
            return app(ProductService::class)->getRecentProducts(7, 10);
        }, $this->longTTL);
        
        // Warm up categories
        $this->remember('categories:all', function () {
            return \App\Models\Category::all();
        }, $this->longTTL);
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats()
    {
        if (config('cache.default') === 'redis') {
            $redis = Redis::connection();
            
            return [
                'driver' => 'redis',
                'memory_usage' => $redis->info('memory')['used_memory_human'] ?? 'N/A',
                'total_keys' => $redis->dbsize(),
                'hits' => $redis->info('stats')['keyspace_hits'] ?? 0,
                'misses' => $redis->info('stats')['keyspace_misses'] ?? 0,
            ];
        }
        
        return [
            'driver' => config('cache.default'),
            'info' => 'Cache statistics not available for this driver'
        ];
    }

    /**
     * Check cache health
     */
    public function checkCacheHealth()
    {
        try {
            $testKey = 'cache_health_test';
            $testValue = 'test_' . time();
            
            // Test write
            Cache::put($testKey, $testValue, 60);
            
            // Test read
            $retrieved = Cache::get($testKey);
            
            // Test delete
            Cache::forget($testKey);
            
            return [
                'status' => 'healthy',
                'write' => true,
                'read' => $retrieved === $testValue,
                'delete' => !Cache::has($testKey)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }
} 