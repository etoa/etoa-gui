<?php

declare(strict_types=1);

namespace EtoA\Support;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Webmozart\Assert\Assert;
use ZipArchive;

class FileUtils
{
    /**
     * Recursively remove a directory and its contents
     */
    public static function removeDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        self::removeDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function createZipFromDirectory(string $dir, string $zipFile): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
            throw new Exception("Cannot open ZIP file " . $zipFile);
        }

        // create recursive directory iterator
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);

        // let's iterate
        foreach ($files as $name => $file) {
            Assert::isInstanceOf($file, \SplFileInfo::class);
            $new_filename = substr((string) $name, strlen(dirname($dir)) + 1);
            $filePath = $file->getRealPath();
            if (is_file($filePath)) {
                $zip->addFile($filePath, $new_filename);
            }
        }

        // close the zip file
        if (!$zip->close()) {
            throw new Exception("There was a problem writing the ZIP archive " . $zipFile);
        }
    }
}
