<?php declare(strict_types=1);

namespace EtoA\Requirement;

use EtoA\Building\BuildingRequirementRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Technology\TechnologyRequirementRepository;

class RequirementRepositoryProvider
{
    public function __construct(
        private readonly ShipRequirementRepository $shipRequirementRepository,
        private readonly DefenseRequirementRepository $defenseRequirementRepository,
        private readonly BuildingRequirementRepository $buildingRequirementRepository,
        private readonly TechnologyRequirementRepository $technologyRequirementRepository,
        private readonly MissileRequirementRepository $missileRequirementRepository
    )
    {}

    public function getRepositoryForTableName(string $type): AbstractRequirementRepository
    {
        return match ($type) {
            'ship_requirements' => $this->shipRequirementRepository,
            'def_requirements' => $this->defenseRequirementRepository,
            'tech_requirements' => $this->technologyRequirementRepository,
            'building_requirements' => $this->buildingRequirementRepository,
            'missile_requirements' => $this->missileRequirementRepository,
            default => throw new \InvalidArgumentException('No requirement repository available for :' . $type),
        };
    }

    public function getRepositoryForCategory(string $category): AbstractRequirementRepository
    {
        switch ($category) {
            case 's':
            case 'sa':
                return $this->shipRequirementRepository;
            case 'd':
                return $this->defenseRequirementRepository;
            case 't':
                return $this->technologyRequirementRepository;
            case 'b':
                return $this->buildingRequirementRepository;
            case 'm':
                return $this->missileRequirementRepository;
            default:
                throw new \InvalidArgumentException('No requirement repository available for :' . $category);
        }
    }
}
