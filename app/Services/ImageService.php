<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * Upload and resize profile picture
     */
    public function uploadAndResizeProfilePicture(UploadedFile $file, ?string $oldPath = null): string
    {
        // Delete old avatar if exists
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        // Process the image
        $image = Image::make($file);

        // Resize to 300x300 pixels maintaining aspect ratio
        $image->fit(300, 300, function ($constraint): void {
            $constraint->upsize();
        });

        // Generate unique filename
        $filename = uniqid().'.jpg';
        $path = 'avatars/'.$filename;

        // Save the processed image
        Storage::disk('public')->put($path, (string) $image->encode('jpg', 80));

        return $path;
    }

    /**
     * Create multiple sizes of profile picture
     */
    public function createProfilePictureSizes(UploadedFile $file, ?string $oldPath = null): array
    {
        // Delete old avatar if exists
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $sizes = [
            'sm' => 100,
            'md' => 300,
            'lg' => 600,
        ];

        $paths = [];
        $baseFilename = uniqid();

        foreach ($sizes as $size => $pixels) {
            $image = Image::make($file);
            $image->fit($pixels, $pixels, function ($constraint): void {
                $constraint->upsize();
            });

            $filename = $baseFilename.'_'.$size.'.jpg';
            $path = 'avatars/'.$filename;

            Storage::disk('public')->put($path, (string) $image->encode('jpg', 80));
            $paths[$size] = $path;
        }

        return $paths;
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture(?string $path): bool
    {
        if ($path) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrl(?string $path): string
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return asset('storage/'.$path);
        }

        return asset('images/default-avatar.png');
    }
}
