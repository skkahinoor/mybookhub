<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class BookCoverUploadService
{
    /**
     * Upload book cover to S3 with a unique filename.
     */
    public function uploadBookCover(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = rand(111, 99999) . '.' . $extension;
        
        try {
            Storage::disk('s3')->putFileAs('book_covers', $file, $filename);
            // Cache the upload flag so we know it belongs to S3 only on successful upload
            Cache::forever('s3_uploaded_' . $filename, true);
        } catch (\Exception $e) {
            \Log::warning("S3 upload failed for {$filename}, falling back to local: " . $e->getMessage());
        }
        
        return $filename;
    }

    /**
     * Upload book cover to S3 with its original name.
     */
    public function uploadBookCoverWithOriginalName(UploadedFile $file): string
    {
        $filename = $file->getClientOriginalName();
        
        try {
            Storage::disk('s3')->putFileAs('book_covers', $file, $filename);
            // Cache the upload flag so we know it belongs to S3 only on successful upload
            Cache::forever('s3_uploaded_' . $filename, true);
        } catch (\Exception $e) {
            \Log::warning("S3 upload failed for {$filename}, falling back to local: " . $e->getMessage());
        }
        
        return $filename;
    }

    /**
     * Delete book cover from S3 and local storage.
     */
    public function deleteBookCover(string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $deleted = false;
        $filename = basename($path);
        
        // Delete S3 upload cache flag
        Cache::forget('s3_uploaded_' . $filename);

        // Delete from S3
        $s3Path = 'book_covers/' . $filename;
        try {
            if (Storage::disk('s3')->exists($s3Path)) {
                Storage::disk('s3')->delete($s3Path);
                Cache::forget('s3_exists_' . $filename);
                $deleted = true;
            }
        } catch (\Exception $e) {
            \Log::warning("S3 delete failed for {$filename}: " . $e->getMessage());
        }

        // Delete from local
        $localPath = public_path('book_covers/' . $filename);
        if (file_exists($localPath)) {
            @unlink($localPath);
            $deleted = true;
        }

        return $deleted;
    }

    /**
     * Check if book cover exists on S3 or locally.
     */
    public function fileExists(string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $filename = basename($path);

        if (Cache::has('s3_uploaded_' . $filename)) {
            return true;
        }

        // Local check first
        if (file_exists(public_path('book_covers/' . $filename))) {
            return true;
        }

        // S3 check (cached)
        try {
            return Cache::remember('s3_exists_' . $filename, 86400, function () use ($filename) {
                return Storage::disk('s3')->exists('book_covers/' . $filename);
            });
        } catch (\Exception $e) {
            \Log::warning("S3 existence check failed for {$filename}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the URL for the book cover.
     */
    public function getBookCoverUrl(?string $path = null): string
    {
        if (empty($path)) {
            return config('app.book_covers_base_url', 'https://d3pq1zjqrptggt.cloudfront.net/book_covers/') . 'no-image.png';
        }

        // If it is a full URL already
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $filename = basename($path);

        // 1. Check if it is a new upload (via Cache flag) or exists on S3
        $isS3 = false;
        if (Cache::has('s3_uploaded_' . $filename)) {
            $isS3 = true;
        } else {
            try {
                $isS3 = Cache::remember('s3_exists_' . $filename, 86400, function () use ($filename) {
                    return Storage::disk('s3')->exists('book_covers/' . $filename);
                });
            } catch (\Exception $e) {
                \Log::warning("S3 URL existence check failed for {$filename}: " . $e->getMessage());
            }
        }

        if ($isS3) {
            return rtrim(config('app.book_covers_base_url', 'https://d3pq1zjqrptggt.cloudfront.net/book_covers/'), '/') . '/' . $filename;
        }

        // 2. Else if local image exists: Show local image
        if (file_exists(public_path('book_covers/' . $filename))) {
            return asset('book_covers/' . $filename);
        }

        // 3. Else: Show default placeholder image
        return rtrim(config('app.book_covers_base_url', 'https://d3pq1zjqrptggt.cloudfront.net/book_covers/'), '/') . '/no-image.png';
    }
}
