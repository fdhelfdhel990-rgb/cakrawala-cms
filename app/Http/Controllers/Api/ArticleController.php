<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Get list of articles & kabar.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::query();

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        $search = $request->query('search') ?: $request->query('q');
        if ($search) {
            $searchTerm = '%'.strtolower($search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(content) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->boolean('paginate')) {
            $perPage = (int) $request->query('per_page', 10);
            $articles = $query->latest('published_at')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data artikel berhasil diambil',
                'data' => $articles,
            ]);
        }

        if ($request->filled('limit')) {
            $query->take((int) $request->query('limit'));
        } elseif ($request->filled('per_page')) {
            $query->take((int) $request->query('per_page'));
        }

        $articles = $query->latest('published_at')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data artikel berhasil diambil',
            'data' => $articles,
        ]);
    }

    /**
     * Get article detail.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);

        $related = Article::where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $articleData = $article->toArray();
        $articleData['related_articles'] = $related;

        return response()->json([
            'status' => 'success',
            'message' => 'Detail artikel berhasil diambil',
            'data' => $articleData,
        ]);
    }
}
