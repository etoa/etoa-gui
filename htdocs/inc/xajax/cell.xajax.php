<?PHP

use EtoA\Fleet\FleetRepository;
use EtoA\Support\StringUtils;
use EtoA\User\UserPropertiesRepository;

$xajax->register(XAJAX_FUNCTION, 'launchSypProbe');
$xajax->register(XAJAX_FUNCTION, 'launchAnalyzeProbe');
$xajax->register(XAJAX_FUNCTION, 'launchExplorerProbe');

function launchSypProbe($tid)
{
    $cp = Entity::createFactoryById($_SESSION['cpid']);

    $objResponse = new xajaxResponse();
    ob_start();
    $launched = false;

    // TODO
    global $app;

    /** @var UserPropertiesRepository $userPropertiesRepository */
    $userPropertiesRepository = $app[UserPropertiesRepository::class];
    /** @var FleetRepository $fleetRepository */
    $fleetRepository = $app[FleetRepository::class];
    $properties = $userPropertiesRepository->getOrCreateProperties($cp->owner()->id);

    if ($properties->spyShipId > 0) {
        $fleet = new FleetLaunch($cp, $cp->owner());
        if ($fleet->checkHaven()) {
            if ($probeCount = $fleet->addShip($properties->spyShipId, $properties->spyShipCount)) {
                if ($fleet->fixShips()) {
                    if ($ent = Entity::createFactoryById($tid)) {
                        if ($fleet->setTarget($ent)) {
                            if ($fleet->checkTarget()) {
                                if ($fleet->setAction("spy")) {
                                    if ($fid = $fleet->launch()) {
                                        $flObj = $fleetRepository->find($fid);


                                        $str = "$probeCount Spionagesonden unterwegs. Ankunft in " . StringUtils::formatTimespan($flObj->getRemainingTime());
                                        $launched = true;
                                    } else
                                        $str = $fleet->error();
                                } else
                                    $str = $fleet->error();
                            } else
                                $str = $fleet->error();
                        } else
                            $str = $fleet->error();
                    } else {
                        $str = "Problem beim Finden des Zielobjekts!";
                    }
                } else {
                    $str = $fleet->error();
                }
            } else {
                $str = "Auf deinem Planeten befinden sich keine Spionagesonden des <a href=\"?page=userconfig&mode=game\">gewählten</a> Typs!";
            }
        } else {
            $str = $fleet->error();
        }
    } else {
        $str = "Du hast noch keine Standard-Spionagesonde gewählt, überprüfe bitte deine <a href=\"?page=userconfig&mode=game\">Spieleinstellungen</a>!";
    }
    if ($launched) {
        echo "<div style=\"color:#0f0\">" . $str . "<div>";
    } else {
        echo "<div style=\"color:#f90\">" . $str . "<div>";
    }
    $objResponse->assign("spy_info_box", "style.display", 'block');
    $objResponse->append("spy_info", "innerHTML", ob_get_contents());
    ob_end_clean();
    return $objResponse;
}

// add the following line to the php of the calling site:
// $_SESSION['currentEntity']=serialize($cp);
function launchAnalyzeProbe($tid)
{
    /** @var Planet $cp */
    $cp = unserialize($_SESSION['currentEntity']);

    $objResponse = new xajaxResponse();
    ob_start();
    $launched = false;

    // TODO
    global $app;

    /** @var UserPropertiesRepository $userPropertiesRepository */
    $userPropertiesRepository = $app[UserPropertiesRepository::class];
    /** @var FleetRepository $fleetRepository */
    $fleetRepository = $app[FleetRepository::class];
    $properties = $userPropertiesRepository->getOrCreateProperties($cp->owner()->id);

    if ($properties->analyzeShipId > 0) {
        $fleet = new FleetLaunch($cp, $cp->owner());
        if ($fleet->checkHaven()) {
            if ($probeCount = $fleet->addShip($properties->analyzeShipId, $properties->analyzeShipCount)) {
                if ($fleet->fixShips()) {
                    if ($ent = Entity::createFactoryById($tid)) {
                        if ($fleet->setTarget($ent)) {
                            if ($fleet->checkTarget()) {
                                if ($fleet->setAction("analyze")) {
                                    if ($fid = $fleet->launch()) {
                                        $flObj = $fleetRepository->find($fid);

                                        $str = "$probeCount Analysatoren unterwegs. Ankunft in " . StringUtils::formatTimespan($flObj->getRemainingTime());
                                        $launched = true;
                                    } else
                                        $str = $fleet->error();
                                } else
                                    $str = $fleet->error();
                            } else
                                $str = $fleet->error();
                        } else
                            $str = $fleet->error();
                    } else {
                        $str = "Problem beim Finden des Zielobjekts!";
                    }
                } else {
                    $str = $fleet->error();
                }
            } else {
                $str = "Auf deinem Planeten befinden sich keine Analysatoren des <a href=\"?page=userconfig&mode=game\">gewählten</a> Typs!";
            }
        } else {
            $str = $fleet->error();
        }
    } else {
        $str = "Du hast noch keinen Standard-Analysator gewählt, überprüfe bitte deine <a href=\"?page=userconfig&mode=game\">Spieleinstellungen</a>!";
    }
    if ($launched) {
        echo "<div style=\"color:#0f0\">" . $str . "<div>";
    } else {
        echo "<div style=\"color:#f90\">" . $str . "<div>";
    }
    $objResponse->assign("spy_info_box", "style.display", 'block');
    $objResponse->append("spy_info", "innerHTML", ob_get_contents());
    ob_end_clean();
    return $objResponse;
}

// add the following line to the php of the calling site:
// $_SESSION['currentEntity']=serialize($cp);
function launchExplorerProbe($tcid)
{
    /** @var Planet $cp */
    $cp = unserialize($_SESSION['currentEntity']);

    $objResponse = new xajaxResponse();
    ob_start();
    $launched = false;

    // TODO
    global $app;

    /** @var UserPropertiesRepository $userPropertiesRepository */
    $userPropertiesRepository = $app[UserPropertiesRepository::class];
    /** @var FleetRepository $fleetRepository */
    $fleetRepository = $app[FleetRepository::class];

    $properties = $userPropertiesRepository->getOrCreateProperties($cp->owner()->id);

    if ($properties->exploreShipId > 0) {
        $fleet = new FleetLaunch($cp, $cp->owner());
        if ($fleet->checkHaven()) {
            if ($probeCount = $fleet->addShip($properties->exploreShipId, $properties->exploreShipCount)) {
                if ($fleet->fixShips()) {
                    $tc = new Cell($tcid);
                    if ($tc->isValid()) {
                        $tce = $tc->getEntities();
                        if (isset($tce[0])) {
                            $ent = $tce[0];
                            if ($fleet->setTarget($ent)) {
                                if ($fleet->checkTarget()) {
                                    if ($fleet->setAction("explore")) {
                                        if ($fid = $fleet->launch()) {
                                            $flObj = $fleetRepository->find($fid);


                                            $str = "$probeCount Explorer unterwegs. Ankunft in " . StringUtils::formatTimespan($flObj->getRemainingTime());
                                            $launched = true;
                                        } else
                                            $str = $fleet->error();
                                    } else
                                        $str = $fleet->error();
                                } else
                                    $str = $fleet->error();
                            } else
                                $str = $fleet->error();
                        } else {
                            $str = "Problem beim Finden des Zielobjekts (Objekt 0)!";
                        }
                    } else {
                        $str = "Problem beim Finden des Zielobjekts (Zelle)!";
                    }
                } else {
                    $str = $fleet->error();
                }
            } else {
                $str = "Auf deinem Planeten befinden sich keine Explorer des <a href=\"?page=userconfig&mode=game\">gewählten</a> Typs!";
            }
        } else {
            $str = $fleet->error();
        }
    } else {
        $str = "Du hast noch keinen Standard-Explorer gewählt, überprüfe bitte deine <a href=\"?page=userconfig&mode=game\">Spieleinstellungen</a>!";
    }
    if ($launched) {
        echo "<div style=\"color:#0f0\">" . $str . "<div>";
    } else {
        echo "<div style=\"color:#f90\">" . $str . "<div>";
    }
    $objResponse->assign("spy_info_box", "style.display", 'block');
    $objResponse->append("spy_info", "innerHTML", ob_get_contents());
    ob_end_clean();
    return $objResponse;
}
