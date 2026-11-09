<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Get user bookmarks for a specific book.
     */
    public function index(Request $request, int $bookId): JsonResponse
    {
        $user = $request->user();
        $bookmarks = $user->bookmarks()
            ->where('book_id', $bookId)
            ->orderBy('page_number', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookmarks,
        ]);
    }

    /**
     * Store or toggle bookmark for a page.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'page_number' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        $bookmark = Bookmark::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $validated['book_id'],
                'page_number' => $validated['page_number'],
            ],
            [
                'note' => $validated['note'] ?? null,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Bookmark halaman berhasil disimpan',
            'data' => $bookmark,
        ], 201);
    }

    /**
     * Delete a bookmark.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $bookmark = $user->bookmarks()->findOrFail($id);
        $bookmark->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bookmark berhasil dihapus',
        ]);
    }
}
