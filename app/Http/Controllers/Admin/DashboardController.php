<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Book;
use App\Models\Bookshelf;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard with statistics and recent activity.
     */
    public function index(): View
    {
        $totalBooks = Book::count();
        $totalCategories = Category::count();
        $totalArticles = Article::count();
        $totalBanners = Banner::where('is_active', true)->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalUsers = User::where('role', 'member')->count();
        $activeReaders = Bookshelf::where('type', 'reading')->distinct('user_id')->count('user_id');
        $totalReviews = Review::count();
        $averageRating = Review::avg('rating') ?? 5.0;

        $recentReadings = Bookshelf::where('type', 'reading')
            ->with(['user', 'book'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $recentReviews = Review::with(['user', 'book'])
            ->latest('id')
            ->take(5)
            ->get();

        $popularBooks = Book::popular()
            ->with('category')
            ->latest('id')
            ->take(4)
            ->get();

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalCategories',
            'totalArticles',
            'totalBanners',
            'totalAdmins',
            'totalUsers',
            'activeReaders',
            'totalReviews',
            'averageRating',
            'recentReadings',
            'recentReviews',
            'popularBooks'
        ));
    }
}
