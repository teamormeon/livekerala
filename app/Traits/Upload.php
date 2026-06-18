<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait Upload
{
    public function makeDirectory($path)
    {
        if (file_exists($path)) return true;
        return mkdir($path, 0755, true);
    }

    public function removeFile($path)
    {
        return file_exists($path) && is_file($path) ? @unlink($path) : false;
    }

    public function fileUpload($file, $location, $fileName = null, $size = null, $encodedFormat = null, $encodedQuality = 90, $oldFileName = null, $oldDriver = 'local')
    {
        $activeDisk = config('filesystems.default');
        $path = null;

        if (!is_string($file)) {
            $mimeType = (string) $file->getMimeType();
            $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension());
            $targetFileName = $fileName ?? $file->hashName();

            if (str_starts_with($mimeType, 'image/') && !$this->isSvgFile($mimeType, $extension)) {
                $path = $this->makeImage($activeDisk, $file, $location, $size, $encodedFormat, $encodedQuality, $extension);
            } else {
                $path = Storage::disk($activeDisk)->putFileAs($location, $file, $targetFileName);
            }
        } else {
            if ($this->isImageUrl($file)) {
                $path = $this->makeImage($activeDisk, $file, $location, $size, $encodedFormat, $encodedQuality, pathinfo($file, PATHINFO_EXTENSION));
            } else {
                Storage::disk($activeDisk)->put($location, $file);
                $path = $location;
            }
        }

        if (empty($path)) {
            Log::warning('File upload did not return a stored path.', [
                'disk' => $activeDisk,
                'location' => $location,
                'old_file' => $oldFileName,
                'old_driver' => $oldDriver,
            ]);

            return null;
        }

        if (!empty($oldFileName) && Storage::disk($oldDriver)->exists($oldFileName)) {
            Storage::disk($oldDriver)->delete($oldFileName);
        }

        return [
            'path' => $path,
            'driver' => $activeDisk,
        ];
    }

    protected function makeImage($activeDisk, $file, $location, $size, $encodedFormat, $encodedQuality, $fileExtension)
    {
        $image = Image::make($file);
        if (!empty($size)) {
            $size = explode('x', strtolower($size));
            $image->resize($size[0], $size[1]);
        }

        $extension = $encodedFormat ?: $fileExtension;
        $path = $location . '/' . Str::random(30) . '.' . $extension;
        Storage::disk($activeDisk)->put($path, !empty($encodedFormat) ? $image->encode($encodedFormat, $encodedQuality) : $image->encode());
        return $path;
    }

    protected function isImageUrl($url)
    {
        $imageInfo = @getimagesize($url);
        if ($imageInfo != false && str_starts_with($imageInfo['mime'], 'image/'))
            return true;

        return false;
    }

    protected function isSvgFile(string $mimeType, ?string $extension = null): bool
    {
        return $mimeType === 'image/svg+xml' || strtolower((string) $extension) === 'svg';
    }

    public function fileDelete($driver = 'local', $old)
    {
        if (!empty($old)) {
            if (Storage::disk($driver)->exists($old)) {
                Storage::disk($driver)->delete($old);
            }
        }
        return 0;
    }
}

