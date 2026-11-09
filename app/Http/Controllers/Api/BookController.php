<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * Get list of books with search & category filters.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Book::with('category');

        $search = $request->query('search') ?: $request->query('q');
        if ($search) {
            $searchTerm = '%'.strtolower($search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(author) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(publisher) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->filled('category')) {
            $cat = $request->query('category');
            $query->whereHas('category', function ($q) use ($cat) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($cat).'%'])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ['%'.strtolower($cat).'%']);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('category_slug')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->query('category_slug'));
            });
        }

        if ($request->boolean('popular')) {
            $query->popular();
        }

        if ($request->boolean('recommended')) {
            $query->recommended();
        }

        if ($request->boolean('new')) {
            $query->latestCollection();
        }

        if ($request->boolean('paginate')) {
            $perPage = (int) $request->query('per_page', 10);
            $books = $query->latest('id')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diambil',
                'data' => $books,
            ]);
        }

        if ($request->filled('limit')) {
            $query->take((int) $request->query('limit'));
        } elseif ($request->filled('per_page')) {
            $query->take((int) $request->query('per_page'));
        }

        $books = $query->latest('id')->get();

        $formattedBooks = $books->map(function (Book $book) {
            return $this->formatBookData($book);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil',
            'data' => $formattedBooks,
        ]);
    }

    /**
     * Get single book detail.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $book = Book::with(['category', 'reviews.user'])->findOrFail($id);

        $bookData = $this->formatBookData($book);

        $user = auth('sanctum')->user();
        if ($user) {
            $shelf = $user->bookshelves()->where('book_id', $book->id)->get();
            $wishlist = $shelf->firstWhere('type', 'wishlist');
            $reading = $shelf->firstWhere('type', 'reading');

            $bookData['user_status'] = [
                'is_in_wishlist' => $wishlist !== null,
                'is_reading' => $reading !== null,
                'progress_percentage' => $reading?->progress_percentage ?? 0,
                'last_page_read' => $reading?->last_page_read ?? 1,
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail buku berhasil diambil',
            'data' => $bookData,
        ]);
    }

    /**
     * Format a book model for Android mobile consumption.
     *
     * @param  Book  $book
     * @return array<string, mixed>
     */
    protected function formatBookData(Book $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'slug' => $book->slug,
            'author' => $book->author,
            'publisher' => $book->publisher,
            'publication_year' => $book->publication_year,
            'category' => $book->category?->name ?? 'Umum',
            'category_id' => $book->category_id,
            'rating' => (float) $book->rating,
            'pages' => $book->pages,
            'size' => $book->size,
            'synopsis' => $book->synopsis,
            'cover_url' => $book->cover_url,
            'pdf_url' => $book->pdf_url ?: url('/api/books/'.$book->id.'/pdf'),
            'file_url' => $book->pdf_url ?: url('/api/books/'.$book->id.'/pdf'),
            'is_popular' => (bool) $book->is_popular,
            'is_recommended' => (bool) $book->is_recommended,
            'is_new_collection' => (bool) $book->is_new_collection,
        ];
    }

    /**
     * Download or stream book PDF for the Android reader.
     * Supports Cloudflare R2 temporary signed URLs and direct streaming.
     */
    public function streamPdf(int $id): Response
    {
        $book = Book::findOrFail($id);

        return StorageService::deliverPdf($book);
    }
}
