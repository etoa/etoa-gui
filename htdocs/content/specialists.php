<?PHP

use EtoA\Backend\BackendMessageService;
use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;

/** @var SpecialistDataRepository $speciaistRepository */
$speciaistRepository = $app[SpecialistDataRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var BackendMessageService $backendMessageService */
$backendMessageService = $app[BackendMessageService::class];

$t = time();

$planet = $planetRepo->find($cp->id);

echo '<h1>Spezialisten</h1>';
echo $resourceBoxDrawer->getHTML($planet);

$uCnt = $userRepository->count();
$totAvail = ceil($uCnt * $config->getFloat('specialistconfig'));

//
// Engage specialist
//
if (isset($_POST['submit_engage']) && isset($_POST['engage'])) {
    echo "<br/>";
    if ($cu->specialistTime < $t) {
        $specalist = $speciaistRepository->getSpecialist((int) $_POST['engage']);
        if ($specalist !== null) {
            $specialistsInUse = $userRepository->countUsersWithSpecialists();
            $used = min(($specialistsInUse[$specalist->id ?? 0]), $totAvail);
            $avail = $totAvail - $used;
            if ($totAvail != 0)
                $factor = 1 + ($config->param1Float('specialistconfig') / $totAvail * $used);
            else
                $factor = 1;

            if ($cu->points >= $specalist->pointsRequirement) {
                if (
                    $planet->resMetal >= $specalist->costsMetal * $factor &&
                    $planet->resCrystal >= $specalist->costsCrystal * $factor &&
                    $planet->resPlastic >= $specalist->costsPlastic * $factor &&
                    $planet->resFuel >= $specalist->costsFuel * $factor &&
                    $planet->resFood >= $specalist->costsFood * $factor
                ) {
                    $st = $t + (86400 * $specalist->days);
                    $userRepository->setSpecialist($cu->getId(), $specalist->id, $st);
                    $cu->specialistId = $specalist->id;
                    $cu->specialistTime = $st;

                    $planetRepo->addResources(
                        $planet->id,
                        -$specalist->costsMetal * $factor,
                        -$specalist->costsCrystal * $factor,
                        -$specalist->costsPlastic * $factor,
                        -$specalist->costsFuel * $factor,
                        -$specalist->costsFood * $factor
                    );

                    //Update every planet
                    foreach ($planets as $pid) {
                        $backendMessageService->updatePlanet($pid);
                    }
                    success_msg('Der gewählte Spezialist wurde eingestellt!');
                    $app['dispatcher']->dispatch(new \EtoA\Specialist\Event\SpecialistHire($cu->specialistId), \EtoA\Specialist\Event\SpecialistHire::HIRE_SUCCESS);
                } else {
                    error_msg('Zuwenig Rohstoffe vorhanden!');
                }
            } else {
                error_msg('Zuwenig Punkte!');
            }
        } else {
            error_msg('Spezialist nicht gefunden!');
        }
    } else {
        error_msg('Es ist bereits ein Spezialist eingestellt.
        Seine Anstellung dauert noch bis ' . df($cu->specialistTime) . '.
        Du musst warten bis seine Anstellung beendet ist!');
    }
}

//
// Discharge specialist
//
if (isset($_POST['discharge'])) {
    echo '<br/>';
    if ($cu->specialistId > 0 && $cu->specialistTime > $t) {
        $inUse = false;
        $specialist = $speciaistRepository->getSpecialist((int) $cu->specialistId);
        if ($specialist !== null) {
            $inittime = $cu->specialistTime - (86400 * $specialist->days);

            // check if a research is in progress if using the professor
            if ($specialist->timeTechnologies !== 1.0) {
                /** @var TechnologyRepository $technologyRepository */
                $technologyRepository = $app[TechnologyRepository::class];
                $technologyEntries = $technologyRepository->findForUser($cu->getId(), $t);
                foreach ($technologyEntries as $entry) {
                    if ($entry->startTime > $inittime) {
                        $inUse = true;
                        break;
                    }
                }
            }

            //Ingenieur
            if ($specialist->timeDefense !== 1.0) {
                /** @var DefenseQueueRepository $defQueueRepository */
                $defQueueRepository = $app[DefenseQueueRepository::class];
                $entries = $defQueueRepository->searchQueueItems(DefenseQueueSearch::create()->userId($cu->getId()));
                foreach ($entries as $entry) {
                    if ($entry->endTime > $t && $entry->userClickTime > $inittime) {
                        $inUse = true;
                        break;
                    }
                }
            }

            //Architekt
            if ($specialist->timeBuildings !== 1.0) {
                /** @var BuildingRepository $buildingRepository */
                $buildingRepository = $app[BuildingRepository::class];
                $buildingEntries = $buildingRepository->findForUser($cu->getId(), null, $t);
                foreach ($buildingEntries as $entry) {
                    if ($entry->startTime > $inittime) {
                        $inUse = true;
                        break;
                    }
                }
            }

            //Admiral
            if ($specialist->fleetSpeed !== 1.0) {
                /** @var FleetRepository $fleetRepository */
                $fleetRepository = $app[FleetRepository::class];
                $search = FleetSearchParameters::create();
                $search->userId = $cu->getId();
                $fleets = $fleetRepository->findByParameters($search);
                foreach ($fleets as $fleet) {
                    if ($fleet->launchTime > $inittime) {
                        if ($fleet->status == \EtoA\Fleet\FleetStatus::DEPARTURE) {
                            $inUse = true;
                            break;
                        } else {
                            $duration = $fleet->landTime - $fleet->launchTime;
                            $org_launchtime = $fleet->launchTime - $duration;

                            if ($org_launchtime >= $inittime) {
                                $inUse = true;
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            error_msg("Du hast einen Spezialist eingestellt, der gar nicht existiert. Cheater!");
        }

        if ($inUse) {
            error_msg('Der Spezialist wird gerade verwendet!');
        } else {
            $userRepository->setSpecialist($cu->getId(), 0, 0);
            $specialistId = $cu->specialistId;
            $cu->specialistId = 0;
            $cu->specialistTime = 0;

            success_msg('Der Spezialist wurde entlassen!');
            $app['dispatcher']->dispatch(new \EtoA\Specialist\Event\SpecialistDischarge($specialistId), \EtoA\Specialist\Event\SpecialistDischarge::DISCHARGE_SUCCESS);
        }
    } else {
        error_msg('Du kannst niemanden entlassen, da kein Spezialist angestellt ist!');
    }
}

//
// Show current engaged specialist
//
$s_active = false;
if ($cu->specialistId > 0 && $cu->specialistTime > $t) {
    $s_active = true;

    $specialist = $speciaistRepository->getSpecialist((int) $cu->specialistId);
    echo "<form action=\"?page=" . $page . "\" method=\"post\">";
    tableStart("Momentan eingestellter Spezialist");
    echo '<tr>
    <th>Funktion</th>
    <th>Angestellt bis</th>
    <th>Verbleibende Zeit</th>
    <th>Aktionen</th>
    </tr>';
    echo '<tr>
    <td>' . $specialist->name . '</td>
    <td>' . df($cu->specialistTime) . '</td>
    <td id="countDownElem">';
    if ($cu->specialistTime - $t > 0)
        echo StringUtils::formatTimespan($cu->specialistTime - $t);
    else
        echo 'Anstellung abgelaufen!';
    echo '</td>
    <td id="dischargeElem">';
    if ($cu->specialistTime - $t > 0)
        echo '<input type="submit" value="Entlassen" name="discharge"
    onclick="return confirm(\'Willst du den Spezialisten wirklich entlassen? Es werden keine Ressourcen zurückerstattet, da der Spezialist diese als Abgangsentschädigung behält!\')" />';
    echo '</td>
    </tr>';
    tableEnd();
    echo "</form>";
    if ($cu->specialistTime - $t > 0)
        countDown("countDownElem", $cu->specialistTime, "dischargeElem");
}


//
// Show all specialists
//
if (!$s_active) {
    echo "<form action=\"?page=" . $page . "\" method=\"post\">";
}
tableStart("Galaktisches Arbeitsamt " . helpLink('specialists') . "");
echo "<tr>
<th>Name</th>
<th>Benötigte Punkte</th>
<th>Anstellbar für</th>
<th>Verfügbar</th>
<th>Kosten</th>";
if (!$s_active) {
    echo "<th>Auswahl</th>";
}
echo "</tr>";

$specialists = $speciaistRepository->getActiveSpecialists();
$specialistsInUse = $userRepository->countUsersWithSpecialists();
foreach ($specialists as $specialist) {
    $used = min(($specialistsInUse[$specialist->id] ?? 0), $totAvail);
    $avail = $totAvail - $used;
    if ($totAvail != 0)
        $factor = 1 + ($config->param1Float('specialistconfig') / $totAvail * $used);
    else
        $factor = 1;

    echo '<tr>';
    echo '<th style="width:140px;">' . $specialist->name . '</th>';
    echo '<td>';
    echo StringUtils::formatNumber($specialist->pointsRequirement);
    echo '</td>';
    echo '<td>';
    echo $specialist->days . ' Tage';
    echo '</td>';
    echo '<td style="color:' . ($avail > 0 ? '#0f0' : '#f90') . '">';
    echo $avail . " / " . $totAvail;
    echo '</td>';
    echo '<td style="width:150px;">';
    echo RES_ICON_METAL . StringUtils::formatNumber($specialist->costsMetal * $factor) . ' ' . RES_METAL . '<br style="clear:both;"/>';
    echo RES_ICON_CRYSTAL . StringUtils::formatNumber($specialist->costsCrystal * $factor) . ' ' . RES_CRYSTAL . '<br style="clear:both;"/>';
    echo RES_ICON_PLASTIC . StringUtils::formatNumber($specialist->costsPlastic * $factor) . ' ' . RES_PLASTIC . '<br style="clear:both;"/>';
    echo RES_ICON_FUEL . StringUtils::formatNumber($specialist->costsFuel * $factor) . ' ' . RES_FUEL . '<br style="clear:both;"/>';
    echo RES_ICON_FOOD . StringUtils::formatNumber($specialist->costsFood * $factor) . ' ' . RES_FOOD . '<br style="clear:both;"/>';
    echo '</td>';
    if (!$s_active) {
        echo '<td>';
        if ($avail > 0) {
            if (
                $planet->resMetal >= $specialist->costsMetal * $factor &&
                $planet->resCrystal >= $specialist->costsCrystal * $factor &&
                $planet->resPlastic >= $specialist->costsPlastic * $factor &&
                $planet->resFuel >= $specialist->costsFuel * $factor &&
                $planet->resFood >= $specialist->costsFood * $factor &&
                $cu->points >= $specialist->pointsRequirement
            ) {
                echo '<input type="radio" name="engage" value="' . $specialist->id . '" />';
            } else {
                echo 'Zuwenig Rohstoffe/Punkte';
            }
        } else {
            echo "Zurzeit nicht verfügbar!";
        }
        echo '</td>';
    }
    echo '</tr>';
}
tableEnd();


if (!$s_active) {
    echo '<input type="submit" name="submit_engage" value="Gewählten Spezialisten einstellen" /></form>';
}

echo '<div><br/><input type="button" onclick="document.location=\'?page=economy\'" value="Wirtschaft des aktuellen Planeten anzeigen" /></div>';
