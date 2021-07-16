<?PHP

global $app;

use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use EtoA\User\UserRepository;

echo "<h1>Integritätscheck</h1>";

echo "<h2>Prüfen ob zu allen Planeten mit einer User-Id auch ein User existiert...</h2>";
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
$user = $userRepository->getUserNicknames();

/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];
$planets = $planetRepository->getPlanetsAssignedToUsers();

$cnt = 0;
$rowStr = "";
if (count($planets) > 0) {
    foreach ($planets as $planet) {
        if (!isset($user[$planet->userId])) {
            $cnt++;
            $rowStr += "<tr><td>" . $planet->name . "</td><td>" . $planet->id . "</td><td>" . $planet->userId . "</td>
        <td><a href=\"?page=$page&sub=edit&amp;id=" . $planet->id . "\">Bearbeiten</a></td></tr>";
        }
    }
    if ($cnt == 0) {
        echo MessageBox::ok("", "Keine Fehler gefunden!");
    } else {
        echo "<table class=\"tb\"><tr><th>Name</th><th>Id</th><th>User-Id</th><th>Id</th><th>Aktionen</th></tr>";
        echo $rowStr;
        echo "</table>";
    }
} else {
    echo MessageBox::info("", "Keine bewohnten Planeten gefunden!");
}


echo "<h2>Prüfe auf Hauptplaneten ohne User...</h2>";
$mainPlanetsWithoutOwner = $planetRepository->getMainPlanetsWithoutOwner();
if (count($mainPlanetsWithoutOwner) > 0) {
    echo "<table class=\"tb\"><tr><th>Name</th><th>Id</th><th>Aktionen</th></tr>";
    foreach ($mainPlanetsWithoutOwner as $planet) {
        echo "<tr><td>" . $planet->name . "</td><td>" . $planet->id . "</td><td><a href=\"?page=$page&sub=edit&amp;id=" . $planet->id . "\">Bearbeiten</a></td></tr>";
    }
    echo "</table>";
} else {
    echo MessageBox::ok("", "Keine Fehler gefunden!");
}

echo "<h2>Prüfe auf User ohne Hauptplanet / mit zuviel Hauptplaneten...</h2>";

$userPlanetCounts = [];
foreach ($planets as $planet) {
    $userPlanetCounts[$planet->userId] = isset($userPlanetCounts[$planet->userId]) ? $userPlanetCounts[$planet->userId] + 1 : 1;
}
$usersWithMultiplePlanets = array_filter($userPlanetCounts, fn (int $count) => $count > 1);
if (count($usersWithMultiplePlanets) > 0) {
    echo "<table class=\"tb\"><tr><th>Nick</th><th>Anzahl Hauptplaneten</th></tr>";
    foreach ($usersWithMultiplePlanets as $userId => $count) {
        echo "<tr><td>" . $user[$userId] . " (" . $userId . ")</td>
      <td>" . $count . "</td>
      </tr>";
    }
    echo "</table>";
} else {
    echo MessageBox::ok("", "Keine Fehler gefunden!");
}

/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];
$entityCodes = $entityRepository->getEntityCodes();

/** @var StarRepository $starRepository */
$starRepository = $app[StarRepository::class];
$starIds = $starRepository->getAllIds();

/** @var WormholeRepository $wormholeRepository */
$wormholeRepository = $app[WormholeRepository::class];
$wormholeIds = $wormholeRepository->getAllIds();

/** @var AsteroidRepository $asteroidRepository */
$asteroidRepository = $app[AsteroidRepository::class];
$asteroidIds = $asteroidRepository->getAllIds();

/** @var NebulaRepository $nebulaRepository */
$nebulaRepository = $app[NebulaRepository::class];
$nebulaIds = $nebulaRepository->getAllIds();

/** @var EmptySpaceRepository $emptySpaceRepository */
$emptySpaceRepository = $app[EmptySpaceRepository::class];
$emptySpaceIds = $emptySpaceRepository->getAllIds();

$planetIds = $planetRepository->getAllIds();
if (count($entityCodes) > 0) {
    $errcnt = 0;
    echo "<h2>Entitäten werden auf Integrität geprüft...</h2>";
    foreach ($entityCodes as $entityId => $entityCode) {
        switch ($entityCode) {
            case EntityType::STAR:
                if (!in_array($entityId, $starIds, true)) {
                    echo "Fehlender Detaildatensatz bei Entität " . $entityId . " (Stern)<br/>";
                    $errcnt++;
                }
                break;
            case EntityType::PLANET:
                if (!in_array($entityId, $planetIds, true)) {
                    echo "Fehlender Detaildatensatz bei Entität " . $entityId . " (Planet)<br/>";
                    $errcnt++;
                }
                break;
            case EntityType::ASTEROID:
                if (!in_array($entityId, $asteroidIds, true)) {
                    echo "Fehlender Detaildatensatz bei Entität " . $entityId . " (Asteroidenfeld)<br/>";
                    $errcnt++;
                }
                break;
            case EntityType::NEBULA:
                if (!in_array($entityId, $nebulaIds, true)) {
                    echo "Fehlender Detaildatensatz bei Entität " . $entityId . " (Nebel)<br/>";
                    $errcnt++;
                }
                break;
            case EntityType::WORMHOLE:
                if (!in_array($entityId, $wormholeIds, true)) {
                    echo "Fehlender Detaildatensatz bei Entität " . $entityId . " (Wurmloch)<br/>";
                    $errcnt++;
                }
                break;
            case EntityType::EMPTY_SPACE:
                if (!in_array($entityId, $emptySpaceIds, true)) {
                    echo "Fehlender Detaildatensatz bei Entität " . $entityId . " (Leerer Raum)<br/>";
                    $errcnt++;
                }
                break;
            case EntityType::ALLIANCE_MARKET:
            case EntityType::MARKET:
                // No need to check anything here
                break;
            default:
                echo "Achtung! Entität <a href=\"?page=galaxy&sub=edit&id=" . $entityId . "\">" . $entityId . "</a> hat einen unbekannten Code (" . $entityCode . ")<br/>";
                $errcnt++;
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", count($entityCodes) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", count($entityCodes) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
} else {
    echo MessageBox::info("", "Keine Entitäten vorhanden!");
}

if (count($starIds) > 0) {
    $errcnt = 0;
    echo "<h2>Sterne werden auf Integrität geprüft...</h2>";
    foreach ($starIds as $starId) {
        if (!isset($entityCodes[$starId])) {
            echo "Fehlender Entitätsdatemsatz bei Stern " . $starId . "<br/>";
            $errcnt++;
        } elseif ($entityCodes[$starId] !== EntityType::STAR) {
            echo "Falscher Code (" . $entityCodes[$starId] . ") bei Stern <a href=\"?page=galaxy&sub=edit&id=" . $starId . "\">" . $starId . "</a><br/>";
            $errcnt++;
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", count($starIds) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", count($starIds) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
} else {
    echo MessageBox::info("", "Keine Sterne vorhanden!");
}

if (count($wormholeIds) > 0) {
    $errcnt = 0;
    echo "<h2>Wurmlöcher werden auf Integrität geprüft...</h2>";
    foreach ($wormholeIds as $wormholeId) {
        if (!isset($entityCodes[$wormholeId])) {
            echo "Fehlender Entitätsdatemsatz bei Wurmloch " . $wormholeId . "<br/>";
            $errcnt++;
        } elseif ($entityCodes[$wormholeId] !== EntityType::WORMHOLE) {
            echo "Falscher Code (" . $entityCodes[$wormholeId] . ") bei Wurmloch <a href=\"?page=galaxy&sub=edit&id=" . $wormholeId . "\">" . $wormholeId . "</a><br/>";
            $errcnt++;
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", count($wormholeIds) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", count($wormholeIds) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
} else {
    echo MessageBox::info("", "Keine Wurmlöcher vorhanden!");
}

if (count($emptySpaceIds) > 0) {
    $errcnt = 0;
    echo "<h2>Leere Räume werden auf Integrität geprüft...</h2>";
    foreach ($emptySpaceIds as $emptySpaceId) {
        if (!isset($entityCodes[$emptySpaceId])) {
            echo "Fehlender Entitätsdatemsatz bei leerem Raum " . $emptySpaceId . "<br/>";
            $errcnt++;
        } elseif ($entityCodes[$emptySpaceId] !== EntityType::EMPTY_SPACE) {
            echo "Falscher Code (" . $entityCodes[$emptySpaceId] . ") bei leerem Raum <a href=\"?page=galaxy&sub=edit&id=" . $emptySpaceId . "\">" . $emptySpaceId . "</a>.<br/>";
            $errcnt++;
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", count($emptySpaceIds) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", count($emptySpaceIds) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
} else {
    echo MessageBox::info("", "Keine leeren Räume vorhanden!");
}

/** @var CellRepository $cellRepository */
$cellRepository = $app[CellRepository::class];
$cellIds = $cellRepository->getAllIds();
if (count($cellIds) > 0) {
    $errcnt = 0;
    echo "<h2>Zellen werden auf Integrität geprüft...</h2>";
    foreach ($cellIds as $cellId) {
        if (!isset($entityCodes[$cellId])) {
            echo "Fehlende Entität " . $cellId . " bei Zelle <a href=\"?page=galaxy&sub=edit&id=" . $cellId . "\">" . $cellId . "</a><br/>";
            $errcnt++;
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", count($cellIds) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", count($cellIds) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
}
