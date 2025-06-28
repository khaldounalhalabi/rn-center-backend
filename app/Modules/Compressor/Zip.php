<?php

namespace App\Modules\Compressor;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use ZipArchive;

class Zip
{
    /**
     * @param UploadedFile[]|UploadedFile $files
     * @param string                      $path store path
     * @return string|null
     */
    public static function compress(array|UploadedFile $files, string $path): ?string
    {
        $zip = new ZipArchive();

        self::ensureDirectoryExists($path);
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $zip->addFile($file, basename($file));
                }
            }
            $zip->close();
            return $path;
        }

        return null;
    }

    private static function ensureDirectoryExists(string $directory): void
    {
        $directory = dirname($directory);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0775, true, true);
        }
    }
}
