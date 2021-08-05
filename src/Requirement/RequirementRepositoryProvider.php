<?php declare(strict_types=1);

namespace EtoA\Requirement;

use EtoA\Building\BuildingRequirementRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Technology\TechnologyRequirementRepository;

class RequirementRepositoryProvider
{
    private ShipRequirementRepository $shipRequirementRepository;
    private DefenseRequirementRepository $defenseRequirementRepository;
    private BuildingRequirementRepository $buildingRequirementRepository;
    private TechnologyRequirementRepository $technologyRequirementRepository;
    private MissileRequirementRepository $missileRequirementRepository;

    public function __construct(ShipRequirementRepository $shipRequirementRepository, DefenseRequirementRepository $defenseRequirementRepository, BuildingRequirementRepository $buildingRequirementRepository, TechnologyRequirementRepository $technologyRequirementRepository, MissileRequirementRepository $missileRequirementRepository)
    {
        $this->shipRequirementRepository = $shipRequirementRepository;
        $this->defenseRequirementRepository = $defenseRequirementRepository;
        $this->buildingRequirementRepository = $buildingRequirementRepository;
        $this->technologyRequirementRepository = $technologyRequirementRepository;
        $this->missileRequirementRepository = $missileRequirementRepository;
    }

    public function getRepositoryForTableName(string $type): AbstractRequirementRepository
    {
        switch ($type) {
            case 'ship_requirements':
                return $this->shipRequirementRepository;
            case 'def_requirements':
                return $this->defenseRequirementRepository;
            case 'tech_requirements':
                return $this->technologyRequirementRepository;
            case 'building_requirements':
                return $this->buildingRequirementRepository;
            case 'missile_requirements':
                return $this->buildingRequirementRepository;
            default:
                throw new \InvalidArgumentException('No requirement repository available for :' . $type);
        }
    }

    public function getRepositoryForCategory(string $category): AbstractRequirementRepository
    {
        switch ($category) {
            case 's':
                return $this->shipRequirementRepository;
            case 'd':
                return $this->defenseRequirementRepository;
            case 't':
                return $this->technologyRequirementRepository;
            case 'b':
                return $this->buildingRequirementRepository;
            case 'm':
                return $this->buildingRequirementRepository;
            default:
                throw new \InvalidArgumentException('No requirement repository available for :' . $category);
        }
    }
}
