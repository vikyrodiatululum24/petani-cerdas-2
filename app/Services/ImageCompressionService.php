<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Spatie\Image\Image;

class ImageCompressionService
{
    public function compressAndStore(UploadedFile $uploadedFile, string $directory = 'articles', ?string $oldPath = null): string
    {
        $disk = Storage::disk('public');

        $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::uuid()->toString().'.'.$extension;
        $relativePath = trim($directory, '/').'/'.$filename;

        $fullPath = $disk->path($relativePath);
        $disk->makeDirectory(trim($directory, '/'));

        try {
            $image = $this->makeImageInstance($uploadedFile);

            $image
                ->quality(75)
                ->save($fullPath);
        } catch (Throwable) {
            // Fallback if GD/Imagick is unavailable: store original file.
            $disk->putFileAs(trim($directory, '/'), $uploadedFile, $filename);
        }

        if ($oldPath && $disk->exists($oldPath)) {
            $disk->delete($oldPath);
        }

        return $relativePath;
    }

    private function makeImageInstance(UploadedFile $uploadedFile): Image
    {
        $realPath = $uploadedFile->getRealPath();

        if (extension_loaded('gd')) {
            return Image::useImageDriver('gd')->loadFile($realPath);
        }

        if (class_exists('Imagick')) {
            return Image::useImageDriver('imagick')->loadFile($realPath);
        }

        throw new \RuntimeException('No supported image driver available.');
    }

    public function deleteIfExists(?string $path): void
    {
        if (! $path) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
