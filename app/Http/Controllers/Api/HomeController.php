<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Get aggregate data for the Jetpack Compose Home screen.
     */
    public function index(): JsonResponse
    {
        $banners = Banner::active()->get();

        $recommendedBooks = Book::recommended()
            ->with('category')
            ->latest('id')
            ->take(6)
            ->get();

        $popularBooks = Book::popular()
            ->with('category')
            ->latest('id')
            ->take(6)
            ->get();

        $newBooks = Book::latestCollection()
            ->with('category')
            ->latest('id')
            ->take(6)
            ->get();

        $articles = Article::latest('published_at')
            ->take(5)
            ->get();

        $categories = Category::withCount('books')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'banners' => $banners,
                'recommended_books' => $recommendedBooks,
                'popular_books' => $popularBooks,
                'new_books' => $newBooks,
                'articles' => $articles,
                'categories' => $categories,
            ],
        ]);
    }
}
