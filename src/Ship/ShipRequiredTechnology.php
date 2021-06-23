<?php declare(strict_types=1);

namespace EtoA\Ship;

class ShipRequiredTechnology
{
    public int $id;
    public string $name;
    public int $requiredLevel;

    public function __construct(array $data)
    {
        $this->id = (int) $data['tech_id'];
        $this->name = $data['tech_name'];
        $this->requiredLevel = (int) $data['req_level'];
    }
}
