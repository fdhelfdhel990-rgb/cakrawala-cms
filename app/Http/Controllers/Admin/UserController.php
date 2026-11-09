<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of mobile users.
     */
    public function index(Request $request): View
    {
        $query = User::withCount([
            'bookshelves as reading_count' => fn ($q) => $q->where('type', 'reading'),
            'bookshelves as wishlist_count' => fn ($q) => $q->where('type', 'wishlist'),
            'reviews',
        ]);

        if ($request->filled('q')) {
            $searchTerm = '%'.strtolower($request->query('q')).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user's reading activity.
     */
    public function show(int $id): View
    {
        $user = User::with([
            'bookshelves.book.category',
            'bookmarks.book',
            'reviews.book',
        ])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }
}
