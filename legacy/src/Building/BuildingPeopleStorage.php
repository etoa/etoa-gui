<?php declare(strict_types=1);

namespace EtoA\Building;

class BuildingPeopleStorage
{
    public float $storeFactor;
    public string $buildingName;
    public int $peoplePlace;
    public int $currentLevel;

    public function __construct(array $data)
    {
        $this->storeFactor = (float) $data['building_store_factor'];
        $this->buildingName = $data['building_name'];
        $this->peoplePlace = (int) $data['building_people_place'];
        $this->currentLevel = (int) $data['buildlist_current_level'];
    }
}
