<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * Get list of public reviews.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Review::visible()->with(['user', 'book']);

        if ($request->filled('book_id')) {
            $query->where('book_id', $request->query('book_id'));
        }

        $reviews = $query->latest('id')->take(20)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data ulasan berhasil diambil',
            'data' => $reviews,
        ]);
    }

    /**
     * Store user review ("Bagikan pengalaman membaca Anda").
     * Accepts both mobile payload (user_name, user_email, rating, book_title, review_text)
     * and standard authenticated payload (book_id, rating, comment).
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_name' => ['nullable', 'string', 'max:255'],
            'user_email' => ['nullable', 'email', 'max:255'],
            'book_title' => ['nullable', 'string', 'max:255'],
            'book_id' => ['nullable', 'exists:books,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string', 'max:1000'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user('sanctum');

        if (! $user && ! empty($validated['user_email'])) {
            $user = User::firstOrCreate(
                ['email' => $validated['user_email']],
                [
                    'name' => $validated['user_name'] ?: 'Pembaca',
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'member',
                    'membership_status' => 'approved',
                ]
            );
        }

        if (! $user) {
            $user = User::where('role', 'member')->first() ?: User::first();
        }

        $bookId = $validated['book_id'] ?? null;
        if (! $bookId && ! empty($validated['book_title'])) {
            $book = Book::whereRaw('LOWER(title) LIKE ?', ['%'.strtolower($validated['book_title']).'%'])->first();
            $bookId = $book?->id;
        }

        $comment = $validated['review_text'] ?? $validated['comment'] ?? null;

        $review = Review::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
            'rating' => (int) round($validated['rating']),
            'comment' => $comment,
            'is_visible' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ulasan dan rating berhasil dikirim',
            'data' => $review->load(['user', 'book']),
        ], 201);
    }
}
