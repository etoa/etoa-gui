<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceRight
{
    public int $id;
    public string $key;
    public string $description;

    public function __construct(array $data)
    {
        $this->id = (int) $data['right_id'];
        $this->key = $data['right_key'];
        $this->description = $data['right_desc'];
    }
}
