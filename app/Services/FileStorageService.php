<?php

namespace App\Services;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    /**
     * Summary of storeFile
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path The folder where the file will be stored
     * @param string $disk The place where the file will be stored
     * @return string Path to the file
     * 
     */
    public function storeFile(UploadedFile $file, string $path, string $disk = "local"): string
    {
        return $file->store($path, $disk);
    }

    /**
     * Summary of getUrlFile
     * @param string $path 
     * @param string $disck not used for now
     * @return string
     */
    public function getUrlFile(string $path, string $disck = 'local'): string
    {
        return config('app.url') . Storage::url($path);
    }


    public function deleteFile(string $path, string $disk = "local"): bool
    {
        return Storage::disk($disk)->delete($path);
    }
}