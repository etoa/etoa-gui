<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

use EtoA\Building\BuildingDataRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Entity\Building;
use EtoA\Entity\DefaultItem;
use EtoA\Entity\Defense;
use EtoA\Entity\Missile;
use EtoA\Entity\Ship;
use EtoA\Entity\Technology;
use EtoA\Missile\MissileDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;

/**
 * Resolves the game object a DefaultItem points to.
 *
 * default_items.item_object_id references one of five different tables, selected by
 * item_cat. That is not expressible as a Doctrine association, so the lookup lives here
 * instead of on the entity.
 */
class DefaultItemObjectResolver
{
    public function __construct(
        private readonly BuildingDataRepository   $buildingDataRepository,
        private readonly TechnologyDataRepository $technologyDataRepository,
        private readonly ShipDataRepository       $shipDataRepository,
        private readonly DefenseDataRepository    $defenseDataRepository,
        private readonly MissileDataRepository    $missileDataRepository,
    ) {
    }

    /**
     * Returns null for an unknown category or a dangling object id.
     */
    public function resolve(DefaultItem $item): Building|Technology|Ship|Defense|Missile|null
    {
        $category = DefaultItemType::tryFrom($item->getCat());
        if ($category === null) {
            return null;
        }

        $objectId = $item->getObjectId();

        return match ($category) {
            DefaultItemType::BUILDING => $this->buildingDataRepository->find($objectId),
            DefaultItemType::TECHNOLOGY => $this->technologyDataRepository->find($objectId),
            DefaultItemType::SHIP => $this->shipDataRepository->find($objectId),
            DefaultItemType::DEFENSE => $this->defenseDataRepository->find($objectId),
            DefaultItemType::MISSILE => $this->missileDataRepository->find($objectId),
        };
    }

    public function resolveName(DefaultItem $item): string
    {
        return (string) $this->resolve($item)?->getName();
    }
}
