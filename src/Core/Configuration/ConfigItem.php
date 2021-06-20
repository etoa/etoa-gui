<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

class ConfigItem
{
    public string $name;
    public $v;
    public $p1;
    public $p2;

    function __construct(string $name, $v, $p1, $p2)
    {
        $this->name = $name;
        $this->v = $v;
        $this->p1 = $p1;
        $this->p2 = $p2;
    }
}
