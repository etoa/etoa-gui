<?PHP

use EtoA\Building\BuildingRepository;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyRepository;

class Usersetup
{
	/**
	* Add an item setlist to a given planet
	*/
	static function addItemSetListToPlanet($planetid,$userid,$setid)
	{
	    global $app;

		$planetid = intval($planetid);
		$userid = intval($userid);
		$setid = intval($setid);

        /** @var DefaultItemRepository $defaultItemRepository */
        $defaultItemRepository = $app[DefaultItemRepository::class];
        $defaultItems = $defaultItemRepository->getItemsGroupedByCategory($setid);

        // Add buildings
        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
		if (isset($defaultItems['b'])) {
		    foreach ($defaultItems['b'] as $defaultItem) {
                $buildingRepository->addBuilding($defaultItem->objectId, $defaultItem->count, $userid, $planetid);
			}
		}

		// Add technologies
        /** @var TechnologyRepository $technologyRepository */
        $technologyRepository = $app[TechnologyRepository::class];
        if (isset($defaultItems['t'])) {
		    foreach ($defaultItems['t'] as $defaultItem) {
		        $technologyRepository->addTechnology($defaultItem->objectId, $defaultItem->count, $userid, $planetid);
			}
		}

		// Add ships
        /** @var ShipRepository $shipRepository */
        $shipRepository = $app[ShipRepository::class];
        if (isset($defaultItems['s'])) {
            foreach ($defaultItems['s'] as $defaultItem) {
                $shipRepository->addShip($defaultItem->objectId, $defaultItem->count, $userid, $planetid);
			}
		}

		// Add defense
        /** @var DefenseRepository $defenseRepository */
        $defenseRepository = $app[DefenseRepository::class];
        if (isset($defaultItems['d'])) {
            foreach ($defaultItems['d'] as $defaultItem) {
                $defenseRepository->addDefense($defaultItem->objectId, $defaultItem->count, $userid, $planetid);
			}
		}
	}
}

