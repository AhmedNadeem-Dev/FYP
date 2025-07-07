<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageService
{
    protected $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    protected $maxFileSize = 2048 * 1024; // 2MB in bytes
    protected $minDimensions = ['width' => 100, 'height' => 100];
    protected $maxDimensions = ['width' => 4000, 'height' => 4000];

    /**
     * Validate and process uploaded image
     */
    public function processImage(UploadedFile $file, string $directory = 'images'): array
    {
        try {
            // Validate file
            $this->validateImage($file);

            // Generate secure filename
            $filename = $this->generateSecureFilename($file);

            // Optimize and save image
            $path = $this->optimizeAndSave($file, $filename, $directory);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];

        } catch (\Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate uploaded image file
     */
    protected function validateImage(UploadedFile $file): void
    {
        // Check if file is valid
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload');
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            throw new \Exception('File size exceeds maximum limit of 2MB');
        }

        // Check mime type
        if (!in_array($file->getMimeType(), $this->allowedMimes)) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, JPG, and GIF are allowed');
        }

        // Additional security check - verify it's actually an image
        $imageInfo = getimagesize($file->getPathname());
        if ($imageInfo === false) {
            throw new \Exception('File is not a valid image');
        }

        // Check dimensions
        [$width, $height] = $imageInfo;
        
        if ($width < $this->minDimensions['width'] || $height < $this->minDimensions['height']) {
            throw new \Exception('Image dimensions too small. Minimum: 100x100 pixels');
        }

        if ($width > $this->maxDimensions['width'] || $height > $this->maxDimensions['height']) {
            throw new \Exception('Image dimensions too large. Maximum: 4000x4000 pixels');
        }
    }

    /**
     * Generate secure filename
     */
    protected function generateSecureFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $hash = hash('sha256', $file->getPathname() . time() . Str::random(10));
        
        return substr($hash, 0, 13) . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;
    }

    /**
     * Optimize and save image
     */
    protected function optimizeAndSave(UploadedFile $file, string $filename, string $directory): string
    {
        $destinationPath = public_path($directory);
        
        // Create directory if it doesn't exist
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fullPath = $destinationPath . '/' . $filename;

        // Move file to destination
        $file->move($destinationPath, $filename);

        // Optimize image if Intervention Image is available
        if (class_exists('\Intervention\Image\Facades\Image')) {
            try {
                $image = Image::make($fullPath);
                
                // Optimize for web
                $image->orientate() // Auto-rotate based on EXIF data
                      ->save($fullPath, 85); // Save with 85% quality
                      
            } catch (\Exception $e) {
                Log::warning('Image optimization failed: ' . $e->getMessage());
            }
        }

        return $directory . '/' . $filename;
    }

    /**
     * Delete image file
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            $fullPath = public_path($imagePath);
            
            if (file_exists($fullPath)) {
                return unlink($fullPath);
            }
            
            return true; // File doesn't exist, consider it deleted
            
        } catch (\Exception $e) {
            Log::error('Failed to delete image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get image info
     */
    public function getImageInfo(string $imagePath): ?array
    {
        try {
            $fullPath = public_path($imagePath);
            
            if (!file_exists($fullPath)) {
                return null;
            }

            $imageInfo = getimagesize($fullPath);
            if ($imageInfo === false) {
                return null;
            }

            return [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'mime_type' => $imageInfo['mime'],
                'size' => filesize($fullPath),
                'url' => url($imagePath)
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get image info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create thumbnail
     */
    public function createThumbnail(string $imagePath, int $width = 150, int $height = 150): ?string
    {
        if (!class_exists('\Intervention\Image\Facades\Image')) {
            return null;
        }

        try {
            $fullPath = public_path($imagePath);
            
            if (!file_exists($fullPath)) {
                return null;
            }

            $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
            $thumbnailFullPath = public_path($thumbnailPath);

            $image = Image::make($fullPath);
            $image->fit($width, $height, function ($constraint) {
                $constraint->upsize();
            })->save($thumbnailFullPath, 80);

            return $thumbnailPath;
            
        } catch (\Exception $e) {
            Log::error('Failed to create thumbnail: ' . $e->getMessage());
            return null;
        }
    }
} 