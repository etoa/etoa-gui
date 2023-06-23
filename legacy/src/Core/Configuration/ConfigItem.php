<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

class ConfigItem
{
    /** @var bool|int|float|string */
    public $value;
    /** @var bool|int|float|string */
    public $param1;
    /** @var bool|int|float|string */
    public $param2;

    /**
     * @param bool|int|float|string $value
     * @param bool|int|float|string $param1
     * @param bool|int|float|string $param2
     */
    public function __construct($value, $param1, $param2)
    {
        $this->value = $value;
        $this->param1 = $param1;
        $this->param2 = $param2;
    }
}
