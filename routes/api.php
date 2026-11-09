<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\JoinUsController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ShelfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EPerpus Cakrawala - Jetpack Compose Android REST API Routes
|--------------------------------------------------------------------------
*/

// Public Auth Endpoints
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google-login', [AuthController::class, 'googleLogin']);
});

// Public Discovery & Content Endpoints
Route::get('/home', [HomeController::class, 'index']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/books/{id}/cover', [BookController::class, 'streamCover']);
Route::get('/books/{id}/pdf', [BookController::class, 'streamPdf']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store']);
Route::post('/join-us', [JoinUsController::class, 'store']);

// Protected Endpoints (Bearer Token required via Laravel Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // User Profile & Logout
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Rak Saya (My Shelf)
    Route::get('/shelf', [ShelfController::class, 'index']);
    Route::post('/shelf/wishlist/toggle', [ShelfController::class, 'toggleWishlist']);
    Route::post('/shelf/progress', [ShelfController::class, 'updateProgress']);
    Route::delete('/shelf/{id}', [ShelfController::class, 'destroy']);

    // PDF Reader Bookmarks
    Route::get('/books/{bookId}/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy']);
});
