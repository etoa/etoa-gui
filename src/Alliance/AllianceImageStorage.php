<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceImageStorage
{
    public function __construct(
        private string $cacheDir,
    ) {
    }

    public function exists(string $image): bool
    {
        return file_exists($this->cacheDir . '/allianceprofiles/' . $image);
    }

    /**
     * @return string[]
     */
    public function getAllImages(): array
    {
        $files = [];
        if (is_dir($this->cacheDir . '/allianceprofiles/')) {
            $d = opendir($this->cacheDir . '/allianceprofiles/');
            while ($f = readdir($d)) {
                if (is_file($this->cacheDir . '/allianceprofiles/' . $f)) {
                    $files[] = $f;
                }
            }

            closedir($d);
        }

        return $files;
    }

    public function delete(string $image): void
    {
        unlink($this->cacheDir . '/allianceprofiles/' . $image);
    }
}
