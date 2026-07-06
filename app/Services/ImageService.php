<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageService
{
    public function upload(UploadedFile $file, string $folder = 'products'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $destination = public_path("images/{$folder}");

        // Crear carpeta si no existe
        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return "images/{$folder}/{$filename}";
    }

    public function delete(?string $path): void
    {
        if ($path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}