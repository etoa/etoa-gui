<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

use Exception;

class ConfigurationService
{
    /** @var array<string,ConfigItem> */
    private ?array $_items = null;

    private $defaultsXml;

    const DEFAULTS_FILE_PATH = __DIR__ . '/../../../htdocs/config/defaults.xml';

    private ConfigurationRepository $repository;

    public function __construct(ConfigurationRepository $repository)
    {
        $this->repository = $repository;
    }

    private function ensureLoaded(bool $reload = false): void
    {
        if ($this->_items === null || $reload) {
            $this->_items = $this->repository->findAll();
        }
    }

    public function reload(): void
    {
        $this->ensureLoaded(true);
    }

    public function set(string $name, $value, $param1 = "", $param2 = ""): void
    {
        $this->ensureLoaded();
        $this->_items[$name] = new ConfigItem($value, $param1, $param2);
        $this->repository->save($name, $this->_items[$name]);
    }

    public function forget(string $name): void
    {
        $this->ensureLoaded();
        $this->repository->remove($name);
        unset($this->_items[$name]);
    }

    /**
     * @return array<string,ConfigItem>
     */
    public function all(): array
    {
        $this->ensureLoaded();
        return $this->_items;
    }

    public function get(string $key)
    {
        $this->ensureLoaded();
        if (isset($this->_items[$key])) {
            return $this->_items[$key]->value;
        }
        if ($elem = $this->loadDefault($key)) {
            return $elem->value;
        }
        throw new Exception('Invalid configuration key ' . $key);
    }

    public function getInt(string $key): int
    {
        return (int) $this->get($key);
    }

    public function getFloat(string $key): float
    {
        return (float) $this->get($key);
    }

    public function getBoolean(string $key): bool
    {
        return (bool) $this->get($key);
    }

    public function param1(string $key)
    {
        $this->ensureLoaded();
        if (isset($this->_items[$key])) {
            return $this->_items[$key]->param1;
        }
        if ($elem = $this->loadDefault($key)) {
            return $elem->param1;
        }
        throw new Exception('Invalid configuration key ' . $key);
    }

    public function param1Int(string $key): int
    {
        return (int) $this->param1($key);
    }

    public function param1Float(string $key): float
    {
        return (float) $this->param1($key);
    }

    public function param1Boolean(string $key): bool
    {
        return (bool) $this->param1($key);
    }

    public function param2(string $key)
    {
        $this->ensureLoaded();
        if (isset($this->_items[$key])) {
            return $this->_items[$key]->param2;
        }
        if ($elem = $this->loadDefault($key)) {
            return $elem->param2;
        }
        throw new Exception('Invalid configuration key ' . $key);
    }

    public function param2Int(string $key): int
    {
        return (int) $this->param2($key);
    }

    public function param2Boolean(string $key): bool
    {
        return (bool) $this->param2($key);
    }

    public function param2Float(string $key): float
    {
        return (float) $this->param2($key);
    }

    public function has(string $name): bool
    {
        $this->ensureLoaded();
        return isset($this->_items[$name]);
    }

    public function filled(string $name): bool
    {
        return $this->has($name) && strlen($this->get($name)) > 0;
    }

    public function restoreDefaults(): int
    {
        if ($xml = simplexml_load_file(self::DEFAULTS_FILE_PATH)) {
            $this->repository->truncate();
            $cnt = 0;
            foreach ($xml->items->item as $itemDefinition) {
                $item = new ConfigItem(
                    (string) $itemDefinition->v ?? '',
                    (string) $itemDefinition->p1 ?? '',
                    (string) $itemDefinition->p2 ?? ''
                );
                $this->repository->save((string) $itemDefinition['name'], $item);
                $cnt++;
            }
            return $cnt;
        }
        throw new \Exception("Konfigurationsdatei existiert nicht!");
    }

    public function loadDefault($key)
    {
        if ($this->defaultsXml == null) {
            $this->defaultsXml = simplexml_load_file(self::DEFAULTS_FILE_PATH);
        }
        $arr = $this->defaultsXml->xpath("/config/items/item[@name='" . $key . "']");
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
        if ($this->defaultsXml == null) {
            $this->defaultsXml = simplexml_load_file(self::DEFAULTS_FILE_PATH);
        }
        $c = array();
        foreach ($this->defaultsXml->categories->category as $i) {
            $c[(int)$i['id']] = (string)$i;
        }
        return $c;
    }

    public function itemInCategory($cat): array
    {
        if ($this->defaultsXml == null) {
            $this->defaultsXml = simplexml_load_file(self::DEFAULTS_FILE_PATH);
        }
        return $this->defaultsXml->xpath("/config/items/item[@cat='" . $cat . "']");
    }

    public function getBaseItems(): array
    {
        if ($this->defaultsXml == null) {
            $this->defaultsXml = simplexml_load_file(self::DEFAULTS_FILE_PATH);
        }
        return $this->defaultsXml->xpath("/config/items/item[@base='yes']");
    }
}
