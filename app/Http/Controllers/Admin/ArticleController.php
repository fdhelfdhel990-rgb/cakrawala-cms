<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles & news.
     */
    public function index(Request $request): View
    {
        $query = Article::query();

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('q')) {
            $searchTerm = '%'.strtolower($request->query('q')).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(author) LIKE ?', [$searchTerm]);
            });
        }

        $articles = $query->latest('published_at')->paginate(10)->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create(): View
    {
        return view('admin.articles.create');
    }

    /**
     * Store a newly created article.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:kabar,artikel'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['required', 'date'],
            'status' => ['nullable', 'in:published,draft'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('articles', 'public');
        }

        Article::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.Str::random(5),
            'type' => $validated['type'] ?? 'artikel',
            'author' => $validated['author'] ?? (auth()->user()->name ?? 'Admin'),
            'status' => $validated['status'] ?? 'published',
            'published_at' => $validated['published_at'],
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel / Kabar baru berhasil diterbitkan!');
    }

    /**
     * Display the specified article (redirect to edit form).
     */
    public function show(int $id): RedirectResponse
    {
        return redirect()->route('admin.articles.edit', $id);
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(int $id): View
    {
        $article = Article::findOrFail($id);

        return view('admin.articles.edit', compact('article'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:kabar,artikel'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['required', 'date'],
            'status' => ['nullable', 'in:published,draft'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $imagePath = $article->image;
        if ($request->hasFile('image_file')) {
            if ($article->image && ! Str::startsWith($article->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($article->image);
            }
            $imagePath = $request->file('image_file')->store('articles', 'public');
        }

        $article->update([
            'title' => $validated['title'],
            'type' => $validated['type'] ?? ($article->type ?? 'artikel'),
            'author' => $validated['author'] ?? ($article->author ?? 'Admin'),
            'status' => $validated['status'] ?? ($article->status ?? 'published'),
            'published_at' => $validated['published_at'],
            'excerpt' => $validated['excerpt'] ?? ($article->excerpt ?? Str::limit(strip_tags($validated['content']), 150)),
            'content' => $validated['content'],
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel / Kabar berhasil diperbarui!');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);

        if ($article->image && ! Str::startsWith($article->image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }
}
