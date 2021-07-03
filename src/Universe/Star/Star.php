<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

class Star
{
    public int $id;
    public ?string $name;
    public int $typeId;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->name = $data['name'];
        $this->typeId = (int) $data['type_id'];
    }
}
