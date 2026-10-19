<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageConversionService
{
    /**
     * Konversi berkas gambar lokal ke format .webp.
     */
    public function convertToWebp(string $sourcePath, ?string $destinationPath = null, int $quality = 85): string
    {
        if (!file_exists($sourcePath)) {
            throw new \InvalidArgumentException("Berkas gambar sumber tidak ditemukan: {$sourcePath}");
        }

        if (!$destinationPath) {
            $pathInfo = pathinfo($sourcePath);
            $destinationPath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.webp';
        }

        // Jika sudah webp, salin atau kembalikan tujuan
        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if ($extension === 'webp' && $sourcePath === $destinationPath) {
            return $destinationPath;
        }

        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($sourcePath),
            'png' => @imagecreatefrompng($sourcePath),
            'gif' => @imagecreatefromgif($sourcePath),
            'bmp' => @imagecreatefrombmp($sourcePath),
            'webp' => @imagecreatefromwebp($sourcePath),
            default => @imagecreatefromstring(file_get_contents($sourcePath)),
        };

        if (!$image) {
            throw new \RuntimeException("Gagal memproses format gambar sumber: {$sourcePath}");
        }

        // Pertahankan transparansi PNG / GIF
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        // Pastikan direktori tujuan ada
        $dir = dirname($destinationPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $success = imagewebp($image, $destinationPath, $quality);
        imagedestroy($image);

        if (!$success) {
            throw new \RuntimeException("Gagal menyimpan gambar ke format .webp: {$destinationPath}");
        }

        return $destinationPath;
    }

    /**
     * Konversi UploadedFile Laravel dan simpan langsung ke Storage disk sebagai .webp.
     */
    public function storeAsWebp(UploadedFile $file, string $directory = 'uploads', string $disk = 'public', int $quality = 85): string
    {
        $filename = Str::uuid()->toString() . '.webp';
        $tempPath = $file->getRealPath();

        $targetRelativePath = trim($directory, '/') . '/' . $filename;
        $tempConvertedPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        $this->convertToWebp($tempPath, $tempConvertedPath, $quality);

        Storage::disk($disk)->put($targetRelativePath, file_get_contents($tempConvertedPath));
        @unlink($tempConvertedPath);

        return $targetRelativePath;
    }

    /**
     * Konversi file gambar yang sudah berada di Storage ke .webp dan perbarui lokasinya.
     */
    public function convertStorageImageToWebp(?string $path, string $disk = 'public', int $quality = 85): ?string
    {
        if (empty($path)) {
            return $path;
        }

        if (str_ends_with(strtolower($path), '.webp')) {
            return $path;
        }

        if (!Storage::disk($disk)->exists($path)) {
            return $path;
        }

        $fullPath = Storage::disk($disk)->path($path);
        $pathInfo = pathinfo($path);
        $directory = ($pathInfo['dirname'] !== '.' && $pathInfo['dirname'] !== '') ? $pathInfo['dirname'] . '/' : '';
        $newRelativePath = $directory . $pathInfo['filename'] . '.webp';
        $newFullPath = Storage::disk($disk)->path($newRelativePath);

        try {
            $this->convertToWebp($fullPath, $newFullPath, $quality);

            if ($fullPath !== $newFullPath && file_exists($fullPath)) {
                @unlink($fullPath);
            }

            return $newRelativePath;
        } catch (\Throwable $e) {
            return $path;
        }
    }
}
