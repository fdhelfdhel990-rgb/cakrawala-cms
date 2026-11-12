<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class StorageService
{
    /**
     * Check if Cloudflare R2 credentials are configured in the environment.
     */
    public static function isR2Configured(): bool
    {
        $key = config('filesystems.disks.r2.key');
        $secret = config('filesystems.disks.r2.secret');
        $bucket = config('filesystems.disks.r2.bucket');

        return ! empty($key) && ! empty($secret) && ! empty($bucket);
    }

    /**
     * Get the active disk for books and media files.
     * Prefers 'r2' if configured; otherwise gracefully falls back to 'public'.
     */
    public static function getBookDisk(): string
    {
        return self::isR2Configured() ? 'r2' : 'public';
    }

    /**
     * Store an uploaded PDF document.
     * Returns the relative storage path (e.g., 'books/uuid.pdf').
     */
    public static function storePdf(UploadedFile $file): string
    {
        $disk = self::getBookDisk();
        $filename = Str::uuid()->toString().'.pdf';

        try {
            return $file->storeAs('books', $filename, $disk);
        } catch (\Throwable $e) {
            Log::error("Gagal menyimpan PDF ke disk [{$disk}]: ".$e->getMessage());

            // If R2 upload fails, fallback to local public disk
            if ($disk !== 'public') {
                Log::warning('Mencoba menyimpan PDF ke disk lokal [public] sebagai fallback...');
                return $file->storeAs('books', $filename, 'public');
            }

            throw $e;
        }
    }

    /**
     * Store an uploaded cover image.
     */
    public static function storeCover(UploadedFile $file): string
    {
        $disk = self::getBookDisk();
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::uuid()->toString().'.'.$extension;

        try {
            return $file->storeAs('covers', $filename, $disk);
        } catch (\Throwable $e) {
            Log::error("Gagal menyimpan cover ke disk [{$disk}]: ".$e->getMessage());

            if ($disk !== 'public') {
                return $file->storeAs('covers', $filename, 'public');
            }

            throw $e;
        }
    }

    /**
     * Safely delete a file from all potential disks (R2 and local public).
     */
    public static function deleteFile(?string $path): void
    {
        if (empty($path) || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        // Try deleting from R2
        if (self::isR2Configured()) {
            try {
                if (Storage::disk('r2')->exists($path)) {
                    Storage::disk('r2')->delete($path);
                    Log::info("File [{$path}] berhasil dihapus dari Cloudflare R2.");
                }
            } catch (\Throwable $e) {
                Log::warning("Notice deleting file from R2 [{$path}]: ".$e->getMessage());
            }
        }

        // Try deleting from public disk
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info("File [{$path}] berhasil dihapus dari disk lokal public.");
            }
        } catch (\Throwable $e) {
            Log::warning("Notice deleting file from local public [{$path}]: ".$e->getMessage());
        }
    }

    /**
     * Stream or deliver cover image directly from active storage (R2 or local).
     */
    public static function deliverCover(Book $book): Response
    {
        $path = $book->cover_image;

        if (empty($path)) {
            return response()->json(['message' => 'Cover tidak tersedia.'], 404);
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return redirect()->away($path);
        }

        return self::deliverFileByPath($path, 'image/jpeg');
    }

    /**
     * Deliver any file safely by path from R2 or local disk.
     */
    public static function deliverFileByPath(string $path, ?string $fallbackMime = null): Response
    {
        // 1. Try from Cloudflare R2
        if (self::isR2Configured()) {
            try {
                $r2 = Storage::disk('r2');
                if ($r2->exists($path)) {
                    $mime = $fallbackMime ?: ($r2->mimeType($path) ?: 'image/jpeg');
                    $content = $r2->get($path);

                    return response($content, 200, [
                        'Content-Type' => $mime,
                        'Cache-Control' => 'public, max-age=86400',
                        'Access-Control-Allow-Origin' => '*',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal membaca file dari R2 [{$path}]: ".$e->getMessage());
            }
        }

        // 2. Try from local public disk
        try {
            $pub = Storage::disk('public');
            if ($pub->exists($path)) {
                $fullPath = $pub->path($path);
                $mime = $fallbackMime ?: (mime_content_type($fullPath) ?: 'image/jpeg');

                return response(file_get_contents($fullPath), 200, [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=86400',
                    'Access-Control-Allow-Origin' => '*',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal membaca file dari public [{$path}]: ".$e->getMessage());
        }

        return response()->json(['message' => 'File tidak ditemukan.'], 404);
    }

    /**
     * Generate a public or secure temporary URL for a file.
     */
    public static function getFileUrl(?string $path, int $expirationMinutes = 30): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // If R2 is configured and file exists there
        if (self::isR2Configured()) {
            try {
                if (Storage::disk('r2')->exists($path)) {
                    $r2Url = config('filesystems.disks.r2.url');
                    if (! empty($r2Url)) {
                        return rtrim($r2Url, '/').'/'.ltrim($path, '/');
                    }

                    // Attempt temporary signed URL
                    try {
                        return Storage::disk('r2')->temporaryUrl($path, now()->addMinutes($expirationMinutes));
                    } catch (\Throwable $e) {
                        // Return API stream URL as proxy
                        return url('/api/books/file/'.ltrim($path, '/'));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Error generating R2 URL for [{$path}]: ".$e->getMessage());
            }
        }

        // Default to local Storage URL
        return Storage::disk('public')->url($path);
    }

    /**
     * Stream or redirect to secure signed URL for a book PDF.
     */
    public static function deliverPdf(Book $book): Response
    {
        $path = $book->file_path;

        // 1. Direct external HTTP URL
        if (! empty($path) && Str::startsWith($path, ['http://', 'https://'])) {
            return redirect()->away($path);
        }

        // 2. Cloudflare R2 Delivery (Direct streaming from R2 via authenticated SDK)
        if (self::isR2Configured() && ! empty($path)) {
            try {
                $r2Disk = Storage::disk('r2');
                if ($r2Disk->exists($path)) {
                    $stream = $r2Disk->readStream($path);
                    $fileSize = $r2Disk->size($path);

                    return response()->stream(function () use ($stream) {
                        fpassthru($stream);
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="'.($book->slug ?: 'dokumen-buku').'.pdf"',
                        'Content-Length' => $fileSize,
                        'Accept-Ranges' => 'bytes',
                        'Access-Control-Allow-Origin' => '*',
                        'Cache-Control' => 'private, max-age=900',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal mengakses file PDF dari R2 untuk buku [{$book->id}]: ".$e->getMessage());
            }
        }

        // 3. Local Storage Candidates
        $resolvedPath = null;
        $candidates = [
            $path ? Storage::disk('public')->path($path) : null,
            $path ? public_path($path) : null,
            $path ? public_path('book/'.basename($path)) : null,
            public_path('book/Filosofi Teras.pdf'),
            public_path('book/filosofi_teras.pdf'),
            storage_path('app/public/books/filosofi_teras.pdf'),
            Storage::disk('public')->path('books/sample_pulang_pergi.pdf'),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate && file_exists($candidate) && is_file($candidate)) {
                $resolvedPath = $candidate;
                break;
            }
        }

        // Search glob fallback
        if (! $resolvedPath) {
            $publicBookFiles = glob(public_path('book/*.pdf'));
            if (! empty($publicBookFiles)) {
                $resolvedPath = $publicBookFiles[0];
            } else {
                $storageBookFiles = glob(storage_path('app/public/books/*.pdf'));
                if (! empty($storageBookFiles)) {
                    $resolvedPath = $storageBookFiles[0];
                }
            }
        }

        if (! $resolvedPath || ! file_exists($resolvedPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File dokumen PDF buku belum tersedia.',
            ], 404);
        }

        return response(file_get_contents($resolvedPath), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.($book->slug ?: 'dokumen-buku').'.pdf"',
            'Access-Control-Allow-Origin' => '*',
            'Content-Length' => filesize($resolvedPath),
            'Cache-Control' => 'private, max-age=900',
        ]);
    }
}
