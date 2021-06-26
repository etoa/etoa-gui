<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

class ConfigItem
{
    public $value;
    public $param1;
    public $param2;

    public function __construct($value, $param1, $param2)
    {
        $this->value = $value;
        $this->param1 = $param1;
        $this->param2 = $param2;
    }
}
