<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

use Exception;
use SimpleXMLElement;

class ConfigurationDefinitionsRepository
{
    private $xmlData;

    const DEFAULTS_FILE_PATH = __DIR__ . '/../../../htdocs/config/defaults.xml';

    public function getXmlDefinitions(): SimpleXMLElement
    {
        if (is_file(self::DEFAULTS_FILE_PATH)) {
            return simplexml_load_file(self::DEFAULTS_FILE_PATH);
        }
        throw new Exception("Konfigurationsdatei existiert nicht!");
    }

    private function ensureXmlDefinitionsLoaded(): void
    {
        if ($this->xmlData == null) {
            $this->xmlData = $this->getXmlDefinitions();
        }
    }

    public function getItem($key)
    {
        $this->ensureXmlDefinitionsLoaded();
        $arr = $this->xmlData->xpath("/config/items/item[@name='" . $key . "']");
        if ($arr != null && count($arr) > 0) {
            $itemDefinition = $arr[0];
            return new ConfigItem(
                (string) $itemDefinition->v,
                (string) $itemDefinition->p1,
                (string) $itemDefinition->p2
            );
        }
        return false;
    }

    /**
     * @return array<int,string>
     */
    public function categories(): array
    {
        $this->ensureXmlDefinitionsLoaded();
        $c = array();
        foreach ($this->xmlData->categories->category as $i) {
            $c[(int)$i['id']] = (string)$i;
        }
        return $c;
    }

    public function itemInCategory($cat): array
    {
        $this->ensureXmlDefinitionsLoaded();
        return $this->xmlData->xpath("/config/items/item[@cat='" . $cat . "']");
    }

    public function getBaseItems(): array
    {
        $this->ensureXmlDefinitionsLoaded();
        return $this->xmlData->xpath("/config/items/item[@base='yes']");
    }
}
