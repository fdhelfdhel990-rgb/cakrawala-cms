<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Display a listing of books with search and filter.
     */
    public function index(Request $request): View
    {
        $query = Book::with('category');

        if ($request->filled('q')) {
            $searchTerm = '%'.strtolower($request->query('q')).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(author) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(publisher) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('badge')) {
            $badge = $request->query('badge');
            if ($badge === 'popular') {
                $query->where('is_popular', true);
            } elseif ($badge === 'recommended') {
                $query->where('is_recommended', true);
            } elseif ($badge === 'new') {
                $query->where('is_new_collection', true);
            }
        }

        $books = $query->latest('id')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.create', compact('categories'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'publication_year' => ['nullable', 'integer', 'min:1900', 'max:2099'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'file_size' => ['nullable', 'string', 'max:50'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'synopsis' => ['nullable', 'string'],
            'cover_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'pdf_file' => [
                'nullable',
                'file',
                'mimetypes:application/pdf,application/x-pdf,application/acrobat,applications/vnd.pdf,text/pdf,application/octet-stream',
                'extensions:pdf',
                'max:102400',
            ],
            'is_popular' => ['nullable', 'boolean'],
            'is_recommended' => ['nullable', 'boolean'],
            'is_new_collection' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Nama penulis buku wajib diisi.',
            'category_id.required' => 'Pilih kategori buku.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'cover_file.image' => 'File sampul harus berupa gambar (JPG, PNG, atau WEBP).',
            'cover_file.max' => 'Ukuran file gambar sampul maksimal 10 MB.',
            'pdf_file.file' => 'File PDF yang diunggah tidak valid.',
            'pdf_file.extensions' => 'Dokumen bacaan harus berupa file dengan ekstensi .pdf.',
            'pdf_file.mimetypes' => 'Format file dokumen harus berupa PDF.',
            'pdf_file.max' => 'Ukuran file PDF maksimal 100 MB.',
        ]);

        try {
            $coverPath = null;
            if ($request->hasFile('cover_file') && $request->file('cover_file')->isValid()) {
                $coverPath = $request->file('cover_file')->store('covers', 'public');
            }

            $pdfPath = null;
            if ($request->hasFile('pdf_file') && $request->file('pdf_file')->isValid()) {
                $pdfFile = $request->file('pdf_file');
                // Auto calculate file size if not provided
                if (empty($validated['file_size'])) {
                    try {
                        $bytes = $pdfFile->getSize();
                        $validated['file_size'] = round($bytes / (1024 * 1024), 1).' MB';
                    } catch (\Throwable $e) {
                        $validated['file_size'] = '25.6 MB';
                    }
                }
                $pdfPath = $pdfFile->store('books', 'public');
            }

            Book::create([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']).'-'.Str::random(5),
                'author' => $validated['author'],
                'publisher' => $validated['publisher'] ?? null,
                'category_id' => $validated['category_id'],
                'publication_year' => $validated['publication_year'] ?? null,
                'page_count' => $validated['page_count'] ?? 100,
                'file_size' => $validated['file_size'] ?? '25.6 MB',
                'rating' => $validated['rating'] ?? 4.3,
                'synopsis' => $validated['synopsis'] ?? null,
                'cover_image' => $coverPath,
                'file_path' => $pdfPath,
                'is_popular' => $request->boolean('is_popular'),
                'is_recommended' => $request->boolean('is_recommended'),
                'is_new_collection' => $request->boolean('is_new_collection'),
            ]);

            return redirect()->route('admin.books.index')
                ->with('success', 'Buku baru beserta dokumen PDF berhasil ditambahkan ke katalog!');
        } catch (\Throwable $e) {
            \Log::error('Gagal menambahkan buku baru: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['general' => 'Terjadi kesalahan sistem saat menyimpan data buku: '.$e->getMessage()]);
        }
    }

    /**
     * Display the specified book (redirect to edit form).
     */
    public function show(int $id): RedirectResponse
    {
        return redirect()->route('admin.books.edit', $id);
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(int $id): View
    {
        $book = Book::findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'publication_year' => ['nullable', 'integer', 'min:1900', 'max:2099'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'file_size' => ['nullable', 'string', 'max:50'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'synopsis' => ['nullable', 'string'],
            'cover_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'pdf_file' => [
                'nullable',
                'file',
                'mimetypes:application/pdf,application/x-pdf,application/acrobat,applications/vnd.pdf,text/pdf,application/octet-stream',
                'extensions:pdf',
                'max:102400',
            ],
            'is_popular' => ['nullable', 'boolean'],
            'is_recommended' => ['nullable', 'boolean'],
            'is_new_collection' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Nama penulis buku wajib diisi.',
            'category_id.required' => 'Pilih kategori buku.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'cover_file.image' => 'File sampul harus berupa gambar (JPG, PNG, atau WEBP).',
            'cover_file.max' => 'Ukuran file gambar sampul maksimal 10 MB.',
            'pdf_file.file' => 'File PDF yang diunggah tidak valid.',
            'pdf_file.extensions' => 'Dokumen bacaan harus berupa file dengan ekstensi .pdf.',
            'pdf_file.mimetypes' => 'Format file dokumen harus berupa PDF.',
            'pdf_file.max' => 'Ukuran file PDF maksimal 100 MB.',
        ]);

        try {
            $coverPath = $book->cover_image;
            if ($request->hasFile('cover_file') && $request->file('cover_file')->isValid()) {
                if ($book->cover_image && ! Str::startsWith($book->cover_image, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($book->cover_image);
                }
                $coverPath = $request->file('cover_file')->store('covers', 'public');
            }

            $pdfPath = $book->file_path;
            if ($request->hasFile('pdf_file') && $request->file('pdf_file')->isValid()) {
                if ($book->file_path && ! Str::startsWith($book->file_path, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($book->file_path);
                }
                $pdfFile = $request->file('pdf_file');
                if (empty($validated['file_size'])) {
                    try {
                        $bytes = $pdfFile->getSize();
                        $validated['file_size'] = round($bytes / (1024 * 1024), 1).' MB';
                    } catch (\Throwable $e) {
                        $validated['file_size'] = $book->file_size;
                    }
                }
                $pdfPath = $pdfFile->store('books', 'public');
            }

            $book->update([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'publisher' => $validated['publisher'] ?? null,
                'category_id' => $validated['category_id'],
                'publication_year' => $validated['publication_year'] ?? null,
                'page_count' => $validated['page_count'] ?? 100,
                'file_size' => $validated['file_size'] ?? $book->file_size,
                'rating' => $validated['rating'] ?? $book->rating,
                'synopsis' => $validated['synopsis'] ?? null,
                'cover_image' => $coverPath,
                'file_path' => $pdfPath,
                'is_popular' => $request->boolean('is_popular'),
                'is_recommended' => $request->boolean('is_recommended'),
                'is_new_collection' => $request->boolean('is_new_collection'),
            ]);

            return redirect()->route('admin.books.index')
                ->with('success', 'Data buku berhasil diperbarui!');
        } catch (\Throwable $e) {
            \Log::error('Gagal memperbarui buku: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['general' => 'Terjadi kesalahan sistem saat memperbarui data buku: '.$e->getMessage()]);
        }
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $book = Book::findOrFail($id);

        if ($book->cover_image && ! Str::startsWith($book->cover_image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($book->cover_image);
        }

        if ($book->file_path && ! Str::startsWith($book->file_path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($book->file_path);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus dari sistem.');
    }
}
