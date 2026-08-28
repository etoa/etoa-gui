<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

use EtoA\Entity\Config;
use Exception;
use RuntimeException;

class ConfigurationService
{
    private ?array $items = null;

    public function __construct(
        private readonly ConfigurationRepository            $repository,
        private readonly ConfigurationDefinitionsRepository $definitions,
    )
    {
    }

    private function item(string $name): ?Config
    {
        if ($this->items === null) {
            $this->items = [];
            foreach ($this->repository->findAll() as $item) {
                $this->items[(string) $item->getName()] = $item;
            }
        }

        return $this->items[$name] ?? null;
    }

    public function reload(): void
    {
        $this->items = null;
    }

    /**
     * @param string $name
     * @param float|bool|int|string $value
     * @param float|bool|int|string $param1
     * @param float|bool|int|string $param2
     */
    public function set(string $name, float|bool|int|string $value, float|bool|int|string $param1 = "", float|bool|int|string $param2 = ""): void
    {
        $elem = $this->item($name);
        if($elem) {
            $elem->setValue((string)$value);
            $elem->setParam1((string)$param1);
            $elem->setParam2((string)$param2);
        }
        else {
            $elem = new Config();
            $elem->setName($name);
            $elem->setValue((string)$value);
            $elem->setParam1((string)$param1);
            $elem->setParam2((string)$param2);

            $this->repository->persist($elem);
        }

        $this->repository->save();
        $this->reload();
    }

    public function forget(string $name): void
    {
        $elem = $this->item($name);
        if($elem) {
            $this->repository->remove($elem);
            $this->repository->save();
            $this->reload();
        }
    }

    /**
     * @return array<string,Config>
     */
    public function all(): array
    {
        return $this->repository->findAll();
    }

    public function get(string $key): int|bool|string|float
    {
        $elem = $this->item($key);
        if ($elem) {
            return $elem->getValue();
        }
        $elem = $this->definitions->getItem($key);
        if ($elem !== null) {
            return $elem->value;
        }

        throw new RuntimeException('Invalid configuration key ' . $key);
    }

    public function getInt(string $key): int
    {
        return (int)$this->get($key);
    }

    public function getFloat(string $key): float
    {
        return (float)$this->get($key);
    }

    public function getBoolean(string $key): bool
    {
        return (bool)$this->get($key);
    }

    public function param1(string $key): int|bool|string|float
    {
        $elem = $this->item($key);
        if ($elem) {
            return $elem->getParam1();
        }
        $elem = $this->definitions->getItem($key);
        if ($elem !== null) {
            return $elem->param1;
        }

        throw new Exception('Invalid configuration key ' . $key);
    }

    public function param1Int(string $key): int
    {
        return (int)$this->param1($key);
    }

    public function param1Float(string $key): float
    {
        return (float)$this->param1($key);
    }

    public function param1Boolean(string $key): bool
    {
        return (bool)$this->param1($key);
    }

    /**
     * @return int|bool|string|float
     * @throws Exception
     */
    public function param2(string $key)
    {
        $elem = $this->item($key);
        if ($elem) {
            return $elem->getParam2();
        }
        $elem = $this->definitions->getItem($key);
        if ($elem !== null) {
            return $elem->param2;
        }

        throw new Exception('Invalid configuration key ' . $key);
    }

    public function param2Int(string $key): int
    {
        return (int)$this->param2($key);
    }

    public function param2Boolean(string $key): bool
    {
        return (bool)$this->param2($key);
    }

    public function param2Float(string $key): float
    {
        return (float)$this->param2($key);
    }

    public function has(string $name): bool
    {
        return (bool) $this->item($name);
    }

    public function filled(string $name): bool
    {
        return $this->has($name) && strlen($this->get($name)) > 0;
    }

    public function restoreDefaults(): int
    {
        $xml = $this->definitions->getXmlDefinitions();
        $this->repository->truncate();
        $cnt = 0;
        foreach ($xml->items->item as $itemDefinition) {
            $item = new Config();
            $item->setName($itemDefinition->getName());
            $item->setValue($itemDefinition->v ?? '');
            $item->setParam1($itemDefinition->p1 ?? '');
            $item->setParam2($itemDefinition->p2 ?? '');

            $this->repository->persist($item);

            $cnt++;
        }

        $this->repository->save();
        $this->reload();

        return $cnt;
    }
}
