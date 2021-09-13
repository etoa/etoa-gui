<?PHP

use EtoA\Building\BuildingRequirementRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Requirement\RequirementRepositoryProvider;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Technology\TechnologyRequirementRepository;

$xajax->register(XAJAX_FUNCTION, "reqInfo");


function reqInfo($id, $cat = 'b')
{
    global $app;
    $or = new xajaxResponse();
    ob_start();

    /** @var \EtoA\Building\BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[\EtoA\Building\BuildingDataRepository::class];
    $buildingNames = $buildingRepository->getBuildingNames();

    /** @var \EtoA\Technology\TechnologyDataRepository $technologyRepository */
    $technologyRepository = $app[\EtoA\Technology\TechnologyDataRepository::class];
    $technologyNames = $technologyRepository->getTechnologyNames();

    /** @var \EtoA\Ship\ShipDataRepository $shipRepository */
    $shipRepository = $app[\EtoA\Ship\ShipDataRepository::class];
    $shipNames = $shipRepository->getShipNames();

    /** @var \EtoA\Defense\DefenseDataRepository $defenseRepository */
    $defenseRepository = $app[\EtoA\Defense\DefenseDataRepository::class];
    $defenseNames = $defenseRepository->getDefenseNames();

    /** @var \EtoA\Missile\MissileDataRepository $missileRepository */
    $missileRepository = $app[\EtoA\Missile\MissileDataRepository::class];
    $missileNames = $missileRepository->getMissileNames();

    //
    // Required objects
    //

    /** @var RequirementRepositoryProvider $requiredRepositoryProvider */
    $requiredRepositoryProvider = $app[RequirementRepositoryProvider::class];
    $repository = $requiredRepositoryProvider->getRepositoryForCategory($cat);
    $requirements = $repository->getRequirements($id);

    $items = [];
    foreach ($requirements->getBuildingRequirements($id) as $requirement) {
        $items[] = array($requirement->requiredBuildingId, $buildingNames[$requirement->requiredBuildingId], $requirement->requiredLevel, IMAGE_PATH . "/buildings/building" . $requirement->requiredBuildingId . "_middle.png", "xajax_reqInfo(" . $requirement->requiredBuildingId . ",'b')");
    }

    foreach ($requirements->getTechnologyRequirements($id) as $requirement) {
        $items[] = array($requirement->requiredTechnologyId, $technologyNames[$requirement->requiredTechnologyId], $requirement->requiredLevel, IMAGE_PATH . "/technologies/technology" . $requirement->requiredTechnologyId . "_middle.png", "xajax_reqInfo(" . $requirement->requiredTechnologyId . ",'b')");
    }

    if (count($items) > 0) {
        echo "<div class=\"techtreeItemContainer\">";
        foreach ($items as $i) {
            echo "<div class=\"techtreeItem\" style=\"background:url('" . $i[3] . "');\">
            <div class=\"techtreeItemLevel\">Lvl <b>" . $i[2] . "</b></div>
            <a href=\"javascript:;\" onclick=\"" . $i[4] . "\" style=\"height:100%;display:block;\"></a>
            <div class=\"techtreeItemName\">" . $i[1] . "</div>
            </div>";
        }
        echo "<br style=\"clear:both;\"";
        echo "</div>";

        echo "<div style=\"margin:0px auto;\">wird benötigt für</div>";
    }

    //
    // Current object
    //

    if ($cat == 'b') {
        $img = IMAGE_PATH . "/buildings/building" . $id . "_middle.png";
        $name = $buildingNames[$id];
    } elseif ($cat == 't') {
        $img = IMAGE_PATH . "/technologies/technology" . $id . "_middle.png";
        $name = $technologyNames[$id];
    } elseif ($cat == 's') {
        $img = IMAGE_PATH . "/ships/ship" . $id . "_middle.png";
        $name = $shipNames[$id];
    } elseif ($cat == 'd') {
        $img = IMAGE_PATH . "/defense/def" . $id . "_middle.png";
        $name = $defenseNames[$id];
    } elseif ($cat == 'm') {
        $img = IMAGE_PATH . "/missiles/missile" . $id . "_middle.png";
        $name = $missileNames[$id];
    } else {
        throw new \InvalidArgumentException('Unknown category:' . $cat);
    }
    echo "<div class=\"techtreeMainItem\" style=\"background:url('" . $img . "');\">";
    echo "<div class=\"techtreeItemName\">" . $name . "</div>";
    echo "</div>";

    //
    // Allowed objects
    //

    /** @var BuildingRequirementRepository $buildingRequirementRepository */
    $buildingRequirementRepository = $app[BuildingRequirementRepository::class];
    /** @var DefenseRequirementRepository $defenseRequirementRepository */
    $defenseRequirementRepository = $app[DefenseRequirementRepository::class];
    /** @var ShipRequirementRepository $shipRequirementRepository */
    $shipRequirementRepository = $app[ShipRequirementRepository::class];
    /** @var TechnologyRequirementRepository $technologyRequirementRepository */
    $technologyRequirementRepository = $app[TechnologyRequirementRepository::class];
    /** @var MissileRequirementRepository $missileRequirementRepository */
    $missileRequirementRepository = $app[MissileRequirementRepository::class];
    if ($cat == 'b' || $cat == 't') {
        if ($cat == 'b') {
            $buildingRequirements = $buildingRequirementRepository->getRequiredByBuilding($id);
            $defenseRequirements = $defenseRequirementRepository->getRequiredByBuilding($id);
            $shipRequirements = $shipRequirementRepository->getRequiredByBuilding($id);
            $technologyRequirements = $technologyRequirementRepository->getRequiredByBuilding($id);
            $missileRequirements = $missileRequirementRepository->getRequiredByBuilding($id);
        } elseif ($cat == 't') {
            $buildingRequirements = $buildingRequirementRepository->getRequiredByTechnology($id);
            $defenseRequirements = $defenseRequirementRepository->getRequiredByTechnology($id);
            $shipRequirements = $shipRequirementRepository->getRequiredByTechnology($id);
            $technologyRequirements = $technologyRequirementRepository->getRequiredByTechnology($id);
            $missileRequirements = $missileRequirementRepository->getRequiredByTechnology($id);
        } else {
            throw new \InvalidArgumentException('Unknown category:' . $cat);
        }


        $items = array();
        foreach ($buildingRequirements as $requirement) {
            if (isset($buildingNames[$requirement->objectId])) {
                $items[] = array($requirement->objectId, $buildingNames[$requirement->objectId], $requirement->requiredLevel, IMAGE_PATH . "/buildings/building" . $requirement->objectId . "_middle.png", "xajax_reqInfo(" . $requirement->objectId . ",'b')");
            }
        }
        foreach ($technologyRequirements as $requirement) {
            if (isset($technologyNames[$requirement->objectId])) {
                $items[] = array($requirement->objectId, $technologyNames[$requirement->objectId], $requirement->requiredLevel, IMAGE_PATH . "/technologies/technology" . $requirement->objectId . "_middle.png", "xajax_reqInfo(" . $requirement->objectId . ",'t')");
            }
        }
        foreach ($shipRequirements as $requirement) {
            if (isset($shipNames[$requirement->objectId])) {
                $items[] = array($requirement->objectId, $shipNames[$requirement->objectId], $requirement->requiredLevel, IMAGE_PATH . "/ships/ship" . $requirement->objectId . "_middle.png", "xajax_reqInfo(" . $requirement->objectId . ",'s')");
            }
        }
        foreach ($defenseRequirements as $requirement) {
            if (isset($defenseNames[$requirement->objectId])) {
                $items[] = array($requirement->objectId, $defenseNames[$requirement->objectId], $requirement->requiredLevel, IMAGE_PATH . "/defense/def" . $requirement->objectId . "_middle.png", "xajax_reqInfo(" . $requirement->objectId . ",'d')");
            }
        }
        foreach ($missileRequirements as $requirement) {
            if (isset($missileNames[$requirement->objectId])) {
                $items[] = array($requirement->objectId, $missileNames[$requirement->objectId], $requirement->requiredLevel, IMAGE_PATH . "/missiles/missile" . $requirement->objectId . "_middle.png", "xajax_reqInfo(" . $requirement->objectId . ",'m')");
            }
        }

        if (count($items) > 0) {
            echo "<div style=\"margin:10px auto;\">ermöglicht</div>";

            echo "<div class=\"techtreeItemContainer\">";
            $cnt = 0;
            foreach ($items as $i) {
                echo "<div class=\"techtreeItem\" style=\"background:url('" . $i[3] . "');\">
                <div class=\"techtreeItemLevel\">Ab Lvl <b>" . $i[2] . "</b></div>
                <a href=\"javascript:;\" onclick=\"" . $i[4] . "\" style=\"height:100%;display:block;\"></a>
                <div class=\"techtreeItemName\">" . $i[1] . "</div>
                </div>";
                $cnt++;
            }
            echo "<br style=\"clear:both;\"";
            echo "</div>";
        }
    }


    $out = ob_get_clean();
    $or->assign('reqInfo', 'innerHTML', $out);
    return $or;
}
