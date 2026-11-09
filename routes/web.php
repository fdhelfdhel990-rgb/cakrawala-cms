<?php

use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IdentityController;
use App\Http\Controllers\Admin\JoinUsController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - EPerpus Cakrawala Admin Panel
|--------------------------------------------------------------------------
*/

// Redirect root to admin panel
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Panel Routes
    Route::middleware('admin')->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Manajemen Buku (E-Books & PDF)
        Route::resource('books', BookController::class)->names('admin.books');

        // Manajemen Kategori
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // Manajemen Artikel & Kabar Literasi
        Route::resource('articles', ArticleController::class)->names('admin.articles');

        // Manajemen Banner Slider Beranda
        Route::get('/banners', [BannerController::class, 'index'])->name('admin.banners.index');
        Route::post('/banners', [BannerController::class, 'store'])->name('admin.banners.store');
        Route::put('/banners/{id}', [BannerController::class, 'update'])->name('admin.banners.update');
        Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');

        // Manajemen Join Us & Keanggotaan Komunitas
        Route::get('/join-us', [JoinUsController::class, 'index'])->name('admin.join-us.index');
        Route::post('/join-us/{id}/approve', [JoinUsController::class, 'approve'])->name('admin.join-us.approve');
        Route::post('/join-us/{id}/reject', [JoinUsController::class, 'reject'])->name('admin.join-us.reject');

        // Manajemen Identitas Komunitas & Konten Informasi
        Route::get('/identity', [IdentityController::class, 'index'])->name('admin.identity.index');
        Route::put('/identity', [IdentityController::class, 'update'])->name('admin.identity.update');

        // Manajemen Akun Admin & Izin Akses
        Route::get('/admins', [AdminManagementController::class, 'index'])->name('admin.admins.index');
        Route::post('/admins', [AdminManagementController::class, 'store'])->name('admin.admins.store');
        Route::put('/admins/{id}', [AdminManagementController::class, 'update'])->name('admin.admins.update');
        Route::delete('/admins/{id}', [AdminManagementController::class, 'destroy'])->name('admin.admins.destroy');

        // Monitoring Pengguna Mobile & Aktivitas Membaca
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');

        // Manajemen Ulasan & Rating Aplikasi
        Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::post('/reviews/{id}/reply', [ReviewController::class, 'reply'])->name('admin.reviews.reply');
        Route::patch('/reviews/{id}/toggle', [ReviewController::class, 'toggleVisibility'])->name('admin.reviews.toggle');
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    });
});
