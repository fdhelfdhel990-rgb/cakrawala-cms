<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Bookshelf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShelfController extends Controller
{
    /**
     * Get user's shelf (Baca / Wishlist).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->query('type'); // 'reading', 'wishlist', or null for both

        $query = $user->bookshelves()->with('book.category');

        if ($type) {
            $query->where('type', $type);
        }

        $items = $query->latest('updated_at')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'reading' => $items->where('type', 'reading')->values(),
                'wishlist' => $items->where('type', 'wishlist')->values(),
            ],
        ]);
    }

    /**
     * Toggle book in user's wishlist ("Simpan ke Rak Saya").
     */
    public function toggleWishlist(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
        ]);

        $user = $request->user();
        $existing = Bookshelf::where('user_id', $user->id)
            ->where('book_id', $validated['book_id'])
            ->where('type', 'wishlist')
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message = 'Buku dihapus dari Wishlist Rak Saya';
        } else {
            Bookshelf::create([
                'user_id' => $user->id,
                'book_id' => $validated['book_id'],
                'type' => 'wishlist',
            ]);
            $inWishlist = true;
            $message = 'Buku berhasil disimpan ke Wishlist Rak Saya';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'is_in_wishlist' => $inWishlist,
        ]);
    }

    /**
     * Update reading progress for a book ("Baca Sekarang" & PDF reader).
     */
    public function updateProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'last_page_read' => ['required', 'integer', 'min:1'],
            'progress_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $user = $request->user();
        $book = Book::findOrFail($validated['book_id']);

        $lastPageRead = $validated['last_page_read'];
        if ($book->page_count > 0) {
            $lastPageRead = min($lastPageRead, $book->page_count);
        }

        $progressPercentage = $validated['progress_percentage'] ?? null;
        if ($progressPercentage === null && $book->page_count > 0) {
            $progressPercentage = (int) round(($lastPageRead / $book->page_count) * 100);
        }

        $shelf = Bookshelf::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
                'type' => 'reading',
            ],
            [
                'last_page_read' => $lastPageRead,
                'progress_percentage' => min(100, max(0, $progressPercentage ?? 0)),
                'last_read_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Progres membaca berhasil diperbarui',
            'data' => $shelf,
        ]);
    }

    /**
     * Remove item from shelf.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $shelf = $user->bookshelves()->findOrFail($id);
        $shelf->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Konten berhasil dihapus dari rak',
        ]);
    }
}
