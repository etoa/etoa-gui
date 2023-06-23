<?php declare(strict_types=1);

namespace EtoA\Building;

class PeopleWorking
{
    public int $shipyard;
    public int $defense;
    public int $research;
    public int $building;
    public int $people;
    public int $total;

    public function __construct(array $data)
    {
        $this->shipyard = (int) ($data[BuildingId::SHIPYARD] ?? 0);
        $this->defense = (int) ($data[BuildingId::DEFENSE] ?? 0);
        $this->research = (int) ($data[BuildingId::TECHNOLOGY] ?? 0);
        $this->building = (int) ($data[BuildingId::BUILDING] ?? 0);
        $this->people = (int) ($data[BuildingId::BUILDING] ?? 0);
        $this->total = (int) array_sum($data);
    }

    public function getById(int $buildingId): int
    {
        switch ($buildingId) {
            case BuildingId::BUILDING:
                return $this->building;
            case BuildingId::PEOPLE:
                return $this->people;
            case BuildingId::TECHNOLOGY:
                return $this->research;
            case BuildingId::SHIPYARD:
                return $this->shipyard;
            case BuildingId::DEFENSE:
                return $this->defense;
            default:
                throw new \InvalidArgumentException('Unknown building id: ' . $buildingId);
        }
    }
}
