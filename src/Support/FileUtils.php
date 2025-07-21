<?php

declare(strict_types=1);

namespace EtoA\Support;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;
use ZipArchive;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUtils
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private string $projectDir
    )
    {
    }

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

    public function uploadImage(UploadedFile $file, string $targetDir, array $resize, string &$msg = ''): bool|File
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $ext = $file->guessExtension();
        $newFilename = $safeFilename.'-'.uniqid().'.'.$ext;

        try {
            $file = $file->move($targetDir,$newFilename);
        } catch (FileException $e) {
            $msg = "Es ist ein Fehler beim Hochladen des Bildes aufgetreten. Bitte versuche es später erneut.";
            return false;
        }

        $fpath = $targetDir.$newFilename;
        $ims = getimagesize($fpath);

        if ($ims[0] > $resize[0] || $ims[1] > $resize[1]) {
            if (!ImageUtils::resizeImage($fpath, $fpath, $resize[0], $resize[1], $ext)) {
                $msg = "Bildgrösse konnte nicht angepasst werden!";
                @unlink($fpath);
                return false;
            }
        }
        return $file;
    }

    /**
     * Checks wether a given config file exists
     */
    public function configFileExists($file): bool
    {
        return file_exists($this->getConfigFilePath($file));
    }

    public function getConfigFilePath($file): string
    {
        return $this->projectDir.'/config/' . $file;
    }

    public function writeConfigFile($file, $contents)
    {
        file_put_contents($this->projectDir.'/config/' . $file, $contents);
    }

    /**
     * Fetches the contents of a JSON config file and returns it as an associative array
     * @throws Exception
     */
    public function fetchJsonConfig($file):array
    {
        $path = $this->getConfigFilePath($file);
        if (!file_exists($path)) {
            throw new Exception("Config file $file not found!");
        }
        $data = json_decode(file_get_contents($path), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new Exception("Failed to parse config file $file (JSON error " . json_last_error() . ")!");
        }
        return $data;
    }
}
