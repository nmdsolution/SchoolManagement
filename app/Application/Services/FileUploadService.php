<?php

namespace App\Application\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    public function uploadFile(?UploadedFile $file, string $path): string
    {
        return $file ? $file->store($path, 'public') : '';
    }
}