<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomRequestController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ArtistProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RecommendationController;

/*
|--------------------------------------------------------------------------
| Backward Compatibility Routes (without v1 prefix)
|--------------------------------------------------------------------------
*/

// Authentication routes for backward compatibility
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/signup', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->middleware('throttle:5,1');
Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1');

// Password reset routes for backward compatibility
Route::prefix('password')->group(function () {
    Route::post('/email', [PasswordResetController::class, 'sendResetCode'])->middleware('throttle:3,1');
    Route::post('/forgot', [PasswordResetController::class, 'sendResetCode'])->middleware('throttle:3,1');
    Route::post('/verify-code', [PasswordResetController::class, 'verifyResetCode'])->middleware('throttle:5,1');
    Route::post('/reset', [PasswordResetController::class, 'resetPassword'])->middleware('throttle:3,1');
});

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Public Routes (No Authentication Required)
    |--------------------------------------------------------------------------
    */
    
    // Public product browsing
    Route::prefix('products')->group(function () {
        Route::get('/all', [ProductController::class, 'getAllProducts']);
        Route::get('/{id}', [ProductController::class, 'show']);
    });
    
    // Public user profiles
    Route::get('/users/{id}', [AuthController::class, 'showProfile']);
    Route::get('/users/{userId}/products', [ProductController::class, 'getUserProducts']);
    Route::get('/users/artists', [AuthController::class, 'getArtists']);
    
    // Statistics routes
    Route::prefix('statistics')->group(function () {
        Route::get('/artists', [AuthController::class, 'getArtistCount']);
        Route::get('/sellers', [AuthController::class, 'getSellerCount']);
        Route::get('/projects', [ProjectController::class, 'getCompletedProjectCount']);
        Route::get('/products', [ProductController::class, 'getProductsSoldCount']);
    });
    
    /*
    |--------------------------------------------------------------------------
    | Protected Routes (Require Sanctum Authentication)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        
        // Current user info
        Route::get('/user', function (Request $request) {
            return response()->json($request->user());
        });
        
        // Authentication management
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // User ratings and profiles
        Route::prefix('users/{id}')->group(function () {
            Route::get('/ratings', [AuthController::class, 'getUserRatings']);
            Route::post('/rate', [AuthController::class, 'rateUser']);
            Route::get('/products', [AuthController::class, 'getUserProducts']);
        });
        
        // Product management
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });
        
        // Custom requests
        Route::prefix('custom-requests')->group(function () {
            Route::get('/', [CustomRequestController::class, 'index']);
            Route::post('/', [CustomRequestController::class, 'store']);
            Route::get('/accepted', [CustomRequestController::class, 'getAcceptedRequests']);
            Route::get('/{id}', [CustomRequestController::class, 'show']);
            Route::post('/{id}/accept', [CustomRequestController::class, 'accept']);
            Route::post('/{id}/decline', [CustomRequestController::class, 'decline']);
            Route::post('/{id}/comments', [CustomRequestController::class, 'addComment']);
            Route::delete('/comments/{commentId}', [CustomRequestController::class, 'deleteComment']);
        });
        
        // Order management
        Route::prefix('orders')->group(function () {
            // Customer routes
            Route::get('/', [OrderController::class, 'getUserOrders']);
            Route::get('/{orderId}', [OrderController::class, 'getOrderDetails']);
            Route::post('/checkout', [OrderController::class, 'checkout']);
            Route::put('/{orderId}/cancel', [OrderController::class, 'cancelOrder']);
            Route::post('/{orderId}/cancel', [OrderController::class, 'cancelOrder']); // Backward compatibility
            
            // Seller routes
            Route::get('/seller', [OrderController::class, 'getSellerOrders']);
            Route::put('/{orderId}/status', [OrderController::class, 'updateOrderItemStatus']);
        });
        
        // Cart management
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'getCart']);
            Route::post('/add/{productId}', [CartController::class, 'addToCart']);
            Route::put('/update/{productId}', [CartController::class, 'updateCartItem']);
            Route::delete('/remove/{productId}', [CartController::class, 'removeFromCart']);
            Route::delete('/clear', [CartController::class, 'clearCart']);
        });
        
        // Wishlist management
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'getWishlist']);
            Route::post('/add/{productId}', [WishlistController::class, 'addToWishlist']);
            Route::delete('/remove/{productId}', [WishlistController::class, 'removeFromWishlist']);
        });
        
        // Project management
        Route::prefix('projects')->group(function () {
            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/', [ProjectController::class, 'store']);
            Route::get('/completed', [PortfolioController::class, 'completedProjects']);
            Route::get('/{id}', [ProjectController::class, 'show']);
            Route::delete('/{id}', [ProjectController::class, 'destroy']);
            Route::get('/{id}/available-artists', [ProjectController::class, 'getAvailableArtists']);
            Route::post('/{id}/complete', [ProjectController::class, 'completeProject']);
            Route::post('/{id}/feedback', [ProjectController::class, 'submitFeedback']);
            Route::get('/{id}/feedback', [ProjectController::class, 'getFeedback']);
            Route::post('/{projectId}/invite', [InvitationController::class, 'invite']);
            Route::post('/{projectId}/request-join', [InvitationController::class, 'requestJoin']);
        });
        
        // Invitation management
        Route::prefix('invitations')->group(function () {
            Route::get('/my-invitations', [InvitationController::class, 'indexPendingInvitations']);
            Route::get('/my-sent-invitations', [InvitationController::class, 'indexSentInvitations']);
            Route::post('/{id}/respond', [InvitationController::class, 'respond']);
            Route::post('/{id}/owner-respond', [InvitationController::class, 'ownerRespondToRequest']);
            Route::delete('/{id}', [InvitationController::class, 'destroy']);
        });
        
        // Artist profile management
        Route::prefix('artist')->group(function () {
            Route::get('/my-owned-projects', [ArtistProfileController::class, 'getOwnedProjects']);
            Route::get('/my-collaborations', [ArtistProfileController::class, 'getCollaborationProjects']);
        });
        
        // Recommendations and purchase history
        Route::get('/recommendations', [RecommendationController::class, 'getRecommendations']);
        Route::get('/recommendations/debug', [RecommendationController::class, 'debugRecommendations']);
        Route::get('/purchase-history', [PurchaseController::class, 'purchaseHistory']);
        
        // Reviews
        Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
        
        // Admin routes
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::get('/feedback', [AdminController::class, 'getAllFeedback']);
            Route::get('/products', [AdminController::class, 'index']);
            Route::put('/products/{id}', [AdminController::class, 'update']);
            Route::delete('/products/{id}', [AdminController::class, 'destroy']);
        });
    });
});

// Backward compatibility routes (without v1 prefix)
Route::prefix('products')->group(function () {
    Route::get('/all', [ProductController::class, 'getAllProducts']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

// Cart routes for backward compatibility
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('/add/{productId}', [CartController::class, 'addToCart']);
        Route::put('/update/{productId}', [CartController::class, 'updateCartItem']);
        Route::delete('/remove/{productId}', [CartController::class, 'removeFromCart']);
        Route::delete('/clear', [CartController::class, 'clearCart']);
    });
    
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'getWishlist']);
        Route::post('/add/{productId}', [WishlistController::class, 'addToWishlist']);
        Route::delete('/remove/{productId}', [WishlistController::class, 'removeFromWishlist']);
    });
    
    // Order routes for backward compatibility
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'getUserOrders']);
        Route::get('/{orderId}', [OrderController::class, 'getOrderDetails']);
        Route::post('/checkout', [OrderController::class, 'checkout']);
        Route::put('/{orderId}/cancel', [OrderController::class, 'cancelOrder']);
        Route::post('/{orderId}/cancel', [OrderController::class, 'cancelOrder']); // Backward compatibility
        Route::get('/seller', [OrderController::class, 'getSellerOrders']);
        Route::put('/{orderId}/status', [OrderController::class, 'updateOrderItemStatus']);
    });
    
    Route::get('/recommendations', [RecommendationController::class, 'getRecommendations']);
    Route::get('/recommendations/debug', [RecommendationController::class, 'debugRecommendations']);
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
});



// Backward compatibility routes (without v1 prefix)
Route::middleware(['auth:sanctum'])->group(function () {
    // Project routes for backward compatibility
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/completed', [PortfolioController::class, 'completedProjects']);
        Route::get('/{id}', [ProjectController::class, 'show']);
        Route::delete('/{id}', [ProjectController::class, 'destroy']);
        Route::get('/{id}/available-artists', [ProjectController::class, 'getAvailableArtists']);
        Route::post('/{id}/complete', [ProjectController::class, 'completeProject']);
        Route::post('/{id}/feedback', [ProjectController::class, 'submitFeedback']);
        Route::get('/{id}/feedback', [ProjectController::class, 'getFeedback']);
        Route::post('/{projectId}/invite', [InvitationController::class, 'invite']);
        Route::post('/{projectId}/request-join', [InvitationController::class, 'requestJoin']);
    });
    
    // Portfolio routes for backward compatibility
    Route::get('/portfolio', [PortfolioController::class, 'index']);
    Route::get('/portfolio/{userId}', [PortfolioController::class, 'showUserPortfolio']);
    Route::put('/portfolio/{id}', [PortfolioController::class, 'updatePortfolioProject']);
    Route::delete('/portfolio/{id}', [PortfolioController::class, 'removeFromPortfolio']);
    
    // Invitation routes for backward compatibility  
    Route::get('/my-invitations', [InvitationController::class, 'indexPendingInvitations']);
    Route::get('/my-sent-invitations', [InvitationController::class, 'indexSentInvitations']);
    Route::prefix('invitations')->group(function () {
        Route::post('/{id}/respond', [InvitationController::class, 'respond']);
        Route::post('/{id}/owner-respond', [InvitationController::class, 'ownerRespondToRequest']);
        Route::delete('/{id}', [InvitationController::class, 'destroy']);
    });
    
    // Artist profile routes for backward compatibility
    Route::prefix('artist')->group(function () {
        Route::get('/my-owned-projects', [ArtistProfileController::class, 'getOwnedProjects']);
        Route::get('/my-collaborations', [ArtistProfileController::class, 'getCollaborationProjects']);
    });
    
    // User profile and product routes for backward compatibility  
    Route::get('/user/products', [AuthController::class, 'getUserProducts']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    // Categories for backward compatibility
    Route::get('/categories', [CategoryController::class, 'index']);
    
    // Additional backward compatibility routes
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});

// Fallback for API versioning
Route::fallback(function () {
    return response()->json(['message' => 'API endpoint not found'], 404);
});