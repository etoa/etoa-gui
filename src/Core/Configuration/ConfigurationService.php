<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

class ConfigurationService
{
    private array $_items;

    private $defaultsXml;

    const DEFAULTS_FILE_PATH = __DIR__ . '/../../htdocs/config/defaults.xml';

    private ConfigurationRepository $repository;

    public function __construct(ConfigurationRepository $repository)
    {
        $this->repository = $repository;
        $this->load();
    }

    private function load(): void
    {
        $this->_items = $this->repository->findAll();
    }

    public function reload(): void
    {
        $this->load();
    }

    /**
     * @deprecated
     */
    public function add(string $name, $val, $param1 = "", $param2 = ""): void
    {
        $this->set($name, $val, $param1, $param2);
    }

    public function set(string $name, $val, $param1 = "", $param2 = ""): void
    {
        $this->_items[$name] = new ConfigItem($name, $val, $param1, $param2);
        $this->repository->save($this->_items[$name]);
    }

    public function del(string $name): void
    {
        $this->repository->remove($name);
        unset($this->_items[$name]);
    }

    public function get(string $key)
    {
        return $this->_items[$key]->v;
    }

    /**
     * @deprecated
     */
    public function value(string $key)
    {
        return $this->get($key);
    }

    public function param1(string $key)
    {
        return $this->_items[$key]->p1;
    }

    /**
     * @deprecated
     */
    public function p1(string $key)
    {
        return $this->param1($key);
    }

    public function param2(string $key)
    {
        return $this->_items[$key]->p2;
    }

    /**
     * @deprecated
     */
    public function p2(string $key)
    {
        return $this->param2($key);
    }

    /**
     * Wrapper for saving all values in an array (classic-style)
     * @deprecated
     */
    public function &getArray(): array
    {
        $conf = array();
        foreach ($this->_items as $key => &$i) {
            $conf[$key]['v'] = $i->v;
            $conf[$key]['p1'] = $i->p1;
            $conf[$key]['p2'] = $i->p2;
        }
        unset($i);
        return $conf;
    }

    public function has(string $name): bool
    {
        return isset($this->_items[$name]);
    }

    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    public function __get($name)
    {
        if (isset($this->_items[$name])) {
            return $this->_items[$name];
        }
        if ($elem = $this->loadDefault($name)) {
            return $elem;
        }
        throw new \Exception("Konfigurationsvariable $name existiert nicht!");
    }

    public function restoreDefaults(): int
    {
        if ($xml = simplexml_load_file(self::DEFAULTS_FILE_PATH)) {
            $this->repository->truncate();
            $cnt = 0;
            foreach ($xml->items->item as $i) {
                $item = new ConfigItem(
                    $i['name'],
                    (isset($i->v) ? $i->v : ''),
                    (isset($i->p1) ? $i->p1 : ''),
                    (isset($i->p2) ? $i->p2 : '')
                );
                $this->repository->save($item);
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
            $i = $arr[0];
            return new ConfigItem($key, $i->v, $i->p1, $i->p2);
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

    public function itemInCategory($cat)
    {
        if ($this->defaultsXml == null) {
            $this->defaultsXml = simplexml_load_file(self::DEFAULTS_FILE_PATH);
        }
        return $this->defaultsXml->xpath("/config/items/item[@cat='" . $cat . "']");
    }

    public function getBaseItems()
    {
        if ($this->defaultsXml == null) {
            $this->defaultsXml = simplexml_load_file(self::DEFAULTS_FILE_PATH);
        }
        return $this->defaultsXml->xpath("/config/items/item[@base='yes']");
    }
}
