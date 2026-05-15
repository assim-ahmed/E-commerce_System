<?php
// app/Helpers/ImageHelper.php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Upload single image
     * 
     * @param UploadedFile $image
     * @param string $folder
     * @return string
     */
    public static function uploadImage(UploadedFile $image, string $folder = 'products'): string
    {
        $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
        
        $path = $image->storeAs($folder, $filename, 'public');
        
        return Storage::url($path);
    }
    
    /**
     * Upload multiple images
     * 
     * @param array $images
     * @param string $folder
     * @return array
     */
    public static function uploadMultipleImages(array $images, string $folder = 'products'): array
    {
        $uploadedImages = [];
        
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $uploadedImages[] = self::uploadImage($image, $folder);
            }
        }
        
        return $uploadedImages;
    }
    
    /**
     * Delete single image
     * 
     * @param string $imagePath
     * @return bool
     */
    public static function deleteImage(string $imagePath): bool
    {
        try {
            // Convert URL to relative path
            $relativePath = self::urlToPath($imagePath);
            
            if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                return Storage::disk('public')->delete($relativePath);
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Delete multiple images
     * 
     * @param array $imagePaths
     * @return bool
     */
    public static function deleteImages(array $imagePaths): bool
    {
        $success = true;
        
        foreach ($imagePaths as $imagePath) {
            if (!self::deleteImage($imagePath)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Convert URL to storage path
     * 
     * @param string $url
     * @return string|null
     */
    public static function urlToPath(string $url): ?string
    {
        // Remove base URL
        $baseUrl = config('app.url') . '/storage/';
        if (str_starts_with($url, $baseUrl)) {
            return str_replace($baseUrl, '', $url);
        }
        
        // If it's already a relative path
        if (str_starts_with($url, '/storage/')) {
            return str_replace('/storage/', '', $url);
        }
        
        return null;
    }
    
    /**
     * Get image full URL
     * 
     * @param string $path
     * @return string
     */
    public static function getUrl(string $path): string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        
        return Storage::url($path);
    }
    
    /**
     * Check if image exists
     * 
     * @param string $imagePath
     * @return bool
     */
    public static function imageExists(string $imagePath): bool
    {
        $relativePath = self::urlToPath($imagePath);
        
        if ($relativePath) {
            return Storage::disk('public')->exists($relativePath);
        }
        
        return false;
    }
}