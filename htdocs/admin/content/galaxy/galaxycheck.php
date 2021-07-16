<?PHP

global $app;

use EtoA\Universe\Planet\PlanetRepository;
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


$res = dbquery("SELECT id,code FROM entities;");
if (mysql_num_rows($res) > 0) {
    $errcnt = 0;
    echo "<h2>Entitäten werden auf Integrität geprüft...</h2>";
    while ($arr = mysql_fetch_assoc($res)) {
        switch ($arr['code']) {
            case 's':
                $eres = dbquery("
          SELECT
            id
          FROM
            stars
          WHERE id=" . $arr['id'] . ";");
                if (mysql_num_rows($eres) == 0) {
                    echo "Fehlender Detaildatensatz bei Entität " . $arr['id'] . " (Stern)<br/>";
                    $errcnt++;
                }
                break;
            case 'p':
                $eres = dbquery("
          SELECT
            id
          FROM
            planets
          WHERE id=" . $arr['id'] . ";");
                if (mysql_num_rows($eres) == 0) {
                    echo "Fehlender Detaildatensatz bei Entität " . $arr['id'] . " (Planet)<br/>";
                    $errcnt++;
                }
                break;
            case 'a':
                $eres = dbquery("
          SELECT
            id
          FROM
            asteroids
          WHERE id=" . $arr['id'] . ";");
                if (mysql_num_rows($eres) == 0) {
                    echo "Fehlender Detaildatensatz bei Entität " . $arr['id'] . " (Asteroidenfeld)<br/>";
                    $errcnt++;
                }
                break;
            case 'n':
                $eres = dbquery("
          SELECT
            id
          FROM
            nebulas
          WHERE id=" . $arr['id'] . ";");
                if (mysql_num_rows($eres) == 0) {
                    echo "Fehlender Detaildatensatz bei Entität " . $arr['id'] . " (Nebel)<br/>";
                    $errcnt++;
                }
                break;
            case 'w':
                $eres = dbquery("
          SELECT
            id
          FROM
            wormholes
          WHERE id=" . $arr['id'] . ";");
                if (mysql_num_rows($eres) == 0) {
                    echo "Fehlender Detaildatensatz bei Entität " . $arr['id'] . " (Wurmloch)<br/>";
                    $errcnt++;
                }
                break;
            case 'e':
                $eres = dbquery("
          SELECT
            id
          FROM
            space
          WHERE id=" . $arr['id'] . ";");
                if (mysql_num_rows($eres) == 0) {
                    echo "Fehlender Detaildatensatz bei Entität " . $arr['id'] . " (Leerer Raum)<br/>";
                    $errcnt++;
                }
                break;
            default:
                echo "Achtung! Entität <a href=\"?page=galaxy&sub=edit&id=" . $arr['id'] . "\">" . $arr['id'] . "</a> hat einen unbekannten Code (" . $arr['code'] . ")<br/>";
                $errcnt++;
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", mysql_num_rows($res) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", mysql_num_rows($res) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
} else {
    echo MessageBox::info("", "Keine Entitäten vorhanden!");
}


$res = dbquery("
  SELECT
    id
  FROM
    stars;");
if (mysql_num_rows($res) > 0) {
    $errcnt = 0;
    echo "<h2>Sterne werden auf Integrität geprüft...</h2>";
    while ($arr = mysql_fetch_assoc($res)) {
        $eres = dbquery("
      SELECT
        code
      FROM
        entities
      WHERE
        id=" . $arr['id'] . ";");
        if (mysql_num_rows($eres) == 0) {
            echo "Fehlender Entitätsdatemsatz bei Stern " . $arr['id'] . "<br/>";
            $errcnt++;
        } else {
            $earr = mysql_fetch_array($eres);
            if ($earr['code'] != 's') {
                echo "Falscher Code (" . $earr['code'] . ") bei Stern <a href=\"?page=galaxy&sub=edit&id=" . $arr['id'] . "\">" . $arr['id'] . "</a><br/>";
                $errcnt++;
            }
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", mysql_num_rows($res) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", mysql_num_rows($res) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
} else {
    echo MessageBox::info("", "Keine Sterne vorhanden!");
}

$res = dbquery("
  SELECT
    id
  FROM
    wormholes;");
if (mysql_num_rows($res) > 0) {
    $errcnt = 0;
    echo "<h2>Wurmlöcher werden auf Integrität geprüft...</h2>";
    while ($arr = mysql_fetch_assoc($res)) {
        $eres = dbquery("
      SELECT
        code
      FROM
        entities
      WHERE
        id=" . $arr['id'] . ";");
        if (mysql_num_rows($eres) == 0) {
            echo "Fehlender Entitätsdatemsatz bei Wurmloch " . $arr['id'] . "<br/>";
            $errcnt++;
        } else {
            $earr = mysql_fetch_array($eres);
            if ($earr['code'] != 'w') {
                echo "Falscher Code (" . $earr['code'] . ") bei Wurmloch <a href=\"?page=galaxy&sub=edit&id=" . $arr['id'] . "\">" . $arr['id'] . "</a><br/>";
                $errcnt++;
            }
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", mysql_num_rows($res) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", mysql_num_rows($res) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
} else {
    echo MessageBox::info("", "Keine Wurmlöcher vorhanden!");
}

$res = dbquery("
  SELECT
    id
  FROM
    space;");
if (mysql_num_rows($res) > 0) {
    $errcnt = 0;
    echo "<h2>Leere Räume werden auf Integrität geprüft...</h2>";
    while ($arr = mysql_fetch_assoc($res)) {
        $eres = dbquery("
      SELECT
        code
      FROM
        entities
      WHERE
        id=" . $arr['id'] . ";");
        if (mysql_num_rows($eres) == 0) {
            echo "Fehlender Entitätsdatemsatz bei leerem Raum " . $arr['id'] . "<br/>";
            $errcnt++;
        } else {
            $earr = mysql_fetch_array($eres);
            if ($earr['code'] != 'e') {
                echo "Falscher Code (" . $earr['code'] . ") bei leerem Raum <a href=\"?page=galaxy&sub=edit&id=" . $arr['id'] . "\">" . $arr['id'] . "</a>.<br/>";
                $errcnt++;
            }
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", mysql_num_rows($res) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", mysql_num_rows($res) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
} else {
    echo MessageBox::info("", "Keine leeren Räume vorhanden!");
}

$res = dbquery("SELECT id FROM cells;");
if (mysql_num_rows($res) > 0) {
    $errcnt = 0;
    echo "<h2>Zellen werden auf Integrität geprüft...</h2>";
    while ($arr = mysql_fetch_assoc($res)) {
        $eres = dbquery("
        SELECT
          id
        FROM
          entities
        WHERE cell_id=" . $arr['id'] . ";");
        if (mysql_num_rows($eres) == 0) {
            $earr = mysql_fetch_assoc($eres);
            echo "Fehlende Entität " . $earr['id'] . " bei Zelle <a href=\"?page=galaxy&sub=edit&id=" . $arr['id'] . "\">" . $arr['id'] . "</a><br/>";
            $errcnt++;
        }
    }
    if ($errcnt > 0) {
        echo MessageBox::warning("", mysql_num_rows($res) . " Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!");
    } else {
        echo MessageBox::ok("", mysql_num_rows($res) . " Datensätze geprüft. Keine Fehler gefunden!");
    }
}
