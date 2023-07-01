<?php

namespace EtoA\Design;

class DesignsService
{
    public function __construct(
        private readonly string $projectDir,
    )
    {
    }

    public function getDesigns(): array
    {
        $rootDir = $this->projectDir . '/assets/' . Design::DIRECTORY;
        $designs = array();

        $rd = 'official';
        $baseDir = $rootDir . '/' . $rd;
        if ($d = opendir($baseDir)) {
            while ($f = readdir($d)) {
                $dir = $baseDir . "/" . $f;
                if (is_dir($dir) && !preg_match('/^\./', $f)) {
                    $file = $dir . "/" . Design::CONFIG_FILE_NAME;
                    $design = $this->parseDesignInfoFile($file);
                    if ($design != null) {
                        $design['dir'] = $dir;
                        $design['custom'] = false;
                        $designs[$f] = $design;
                    }
                }
            }
        }
        return $designs;
    }

    private function parseDesignInfoFile($file): ?array
    {
        if (is_file($file)) {
            $design = [];
            $xml = new \XMLReader();
            $xml->open($file);
            while ($xml->read()) {
                switch ($xml->name) {
                    case "name":
                        $xml->read();
                        $design['name'] = $xml->value;
                        $xml->read();
                        break;
                    case "changed":
                        $xml->read();
                        $design['changed'] = $xml->value;
                        $xml->read();
                        break;
                    case "version":
                        $xml->read();
                        $design['version'] = $xml->value;
                        $xml->read();
                        break;
                    case "author":
                        $xml->read();
                        $design['author'] = $xml->value;
                        $xml->read();
                        break;
                    case "email":
                        $xml->read();
                        $design['email'] = $xml->value;
                        $xml->read();
                        break;
                    case "description":
                        $xml->read();
                        $design['description'] = $xml->value;
                        $xml->read();
                        break;
                    case "restricted":
                        $xml->read();
                        $design['restricted'] = $xml->value == "true";
                        $xml->read();
                        break;
                }
            }
            $xml->close();
            return $design;
        }
        return null;
    }
}