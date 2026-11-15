<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookshelf extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'type',
        'is_visible',
        'progress_percentage',
        'last_page_read',
        'last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'last_page_read' => 'integer',
            'last_read_at' => 'datetime',
            'is_visible' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
