<?php declare(strict_types=1);

namespace EtoA\Building;

class BuildingWorkplace
{
    public int $buildingId;
    public string $buildingName;
    public int $peoplePlace;
    public int $peopleWorking;

    public function __construct(array $data)
    {
        $this->buildingId = (int) $data['building_id'];
        $this->buildingName = $data['building_name'];
        $this->peoplePlace = (int) $data['building_people_place'];
        $this->peopleWorking = (int) $data['buildlist_people_working'];
    }
}
