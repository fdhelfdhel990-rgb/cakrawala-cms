<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories.
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('books')->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    /**
     * Get single category with books.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $category = Category::withCount('books')->findOrFail($id);

        $perPage = (int) $request->query('per_page', 10);
        $books = $category->books()->latest('id')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => $category,
                'books' => $books,
            ],
        ]);
    }
}
