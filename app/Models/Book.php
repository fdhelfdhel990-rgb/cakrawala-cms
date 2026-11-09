<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'author',
        'publisher',
        'publication_year',
        'page_count',
        'file_size',
        'rating',
        'synopsis',
        'cover_image',
        'file_path',
        'is_popular',
        'is_recommended',
        'is_new_collection',
    ];

    protected $appends = [
        'cover_url',
        'file_url',
        'pdf_url',
        'pages',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'page_count' => 'integer',
            'publication_year' => 'integer',
            'is_popular' => 'boolean',
            'is_recommended' => 'boolean',
            'is_new_collection' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Book $book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title).'-'.Str::random(5);
            }
        });
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (Str::startsWith($this->cover_image, ['http://', 'https://'])) {
            return $this->cover_image;
        }

        return url('/api/books/'.$this->id.'/cover');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        if (Str::startsWith($this->file_path, ['http://', 'https://'])) {
            return $this->file_path;
        }

        return \App\Services\StorageService::getFileUrl($this->file_path);
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->file_url;
    }

    public function getPagesAttribute(): string
    {
        return $this->page_count > 0 ? "{$this->page_count} Halaman" : '124 Halaman';
    }

    public function getSizeAttribute(): string
    {
        return $this->file_size ?: '25.6 MB';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookshelves(): HasMany
    {
        return $this->hasMany(Bookshelf::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->where('is_popular', true);
    }

    public function scopeRecommended(Builder $query): Builder
    {
        return $query->where('is_recommended', true);
    }

    public function scopeLatestCollection(Builder $query): Builder
    {
        return $query->where('is_new_collection', true);
    }
}
