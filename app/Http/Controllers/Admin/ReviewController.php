<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display a listing of user reviews.
     */
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'book']);

        if ($request->filled('rating')) {
            $query->where('rating', $request->query('rating'));
        }

        $reviews = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Toggle review visibility.
     */
    public function toggleVisibility(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['is_visible' => ! $review->is_visible]);

        $status = $review->is_visible ? 'ditampilkan' : 'disembunyikan';

        return back()->with('success', "Status ulasan berhasil diubah menjadi {$status}.");
    }

    /**
     * Reply to the specified user review.
     */
    public function reply(Request $request, int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);

        $request->validate([
            'reply' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'reply' => $request->input('reply'),
        ]);

        return back()->with('success', 'Balasan untuk ulasan pembaca berhasil disimpan.');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
