<?php

use EtoA\Universe\UniverseGenerator;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Asteroids\AsteroidsRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\UniverseResetService;
use EtoA\Universe\Wormhole\WormholeRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var UniverseGenerator */
$universeGenerator = $app[UniverseGenerator::class];

/** @var UniverseResetService */
$universeResetService = $app[UniverseResetService::class];

/** @var CellRepository */
$cellRepo = $app[CellRepository::class];

/** @var StarRepository */
$starRepo = $app[StarRepository::class];

/** @var PlanetRepository */
$planetRepo = $app[PlanetRepository::class];

/** @var AsteroidsRepository */
$asteroidsRepo = $app[AsteroidsRepository::class];

/** @var NebulaRepository */
$nebulaRepo = $app[NebulaRepository::class];

/** @var WormholeRepository */
$wormholeRepo = $app[WormholeRepository::class];

/** @var EmptySpaceRepository */
$emptySpaceRepo = $app[EmptySpaceRepository::class];

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var Request */
$request = Request::createFromGlobals();

echo "<h1>Universum</h1>";

if ($request->request->has('submit_create_universe')) {
    createUniverse($request, $config);
} elseif ($request->request->has('submit_expansion_universe')) {
    extendUniverse($config);
} elseif ($request->request->has('submit_reset')) {
    resetUniverse();
} elseif ($request->request->has('submit_galaxy_reset')) {
    postResetUniverse($universeResetService);
} elseif ($request->request->has('submit_reset2')) {
    postResetRound($universeResetService);
} elseif ($request->request->has('submit_addstars')) {
    addStars($request, $universeGenerator);
} else {
    universeIndex(
        $request,
        $config,
        $universeGenerator,
        $cellRepo,
        $starRepo,
        $planetRepo,
        $asteroidsRepo,
        $nebulaRepo,
        $wormholeRepo,
        $emptySpaceRepo
    );
}

function createUniverse(Request $request, ConfigurationService $config): void
{
    global $page;
    global $sub;

    echo "<h2>Urknall - Schritt 2/3</h2>";
    $config->set("num_of_sectors", "", $request->request->getInt('num_of_sectors_p1'), $request->request->getInt('num_of_sectors_p2'));
    $config->set("num_of_cells", "", $request->request->getInt('num_of_cells_p1'), $request->request->getInt('num_of_cells_p2'));
    $config->set("space_percent_solsys", $request->request->getInt('space_percent_solsys'));
    $config->set("space_percent_asteroids", $request->request->getInt('space_percent_asteroids'));
    $config->set("space_percent_nebulas", $request->request->getInt('space_percent_nebulas'));
    $config->set("space_percent_wormholes", $request->request->getInt('space_percent_wormholes'));
    $config->set("persistent_wormholes_ratio", max(0, min(100, $request->request->getInt('persistent_wormholes_ratio'))));
    $config->set("num_planets", "", $request->request->getInt('num_planets_p1'), $request->request->getInt('num_planets_p2'));
    $config->set("solsys_percent_planet", $request->request->getInt('solsys_percent_planet'));
    $config->set("solsys_percent_asteroids", $request->request->getInt('solsys_percent_asteroids'));
    $config->set("planet_fields", "", $request->request->getInt('planet_fields_p1'), $request->request->getInt('planet_fields_p2'));

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    tableStart("Systfemanordnung", 400);
    $xdim = $config->param1Int('num_of_sectors') * $config->param1Int('num_of_cells');
    $ydim = $config->param2Int('num_of_sectors') * $config->param2Int('num_of_cells');

    echo "<tr>
      <th>Dimension:</th>
      <td>" . $xdim . "x" . $ydim . " Zellen</td>
    </tr>";
    echo "<tr>
      <th>Karte:</th>
      <td>";
    echo "<input type=\"radio\" name=\"map_image\" value=\"\" checked=\"checked\" /> <img style=\"width:" . $xdim . "px;height:" . $ydim . "px;\" src=\"../images/galaxylayout_random.png\" /> Zufällig";
    $dir = "../images/galaxylayouts";
    $d = opendir($dir);
    while ($f = readdir($d)) {
        if (is_file($dir . DIRECTORY_SEPARATOR . $f) && substr($f, strrpos($f, ".png")) == ".png" && $ims = getimagesize($dir . DIRECTORY_SEPARATOR . $f)) {
            if ($ims[0] == $xdim && $ims[1] == $ydim) {
                echo "<div><input type=\"radio\" name=\"map_image\" value=\"$f\" /> <img src=\"" . $dir . "/" . $f . "\" alt=\"" . $dir . "/" . $f . "\" /> " . basename($f, ".png") . "	</div>";
            }
        }
    }
    echo "</td>
    </tr>";
    echo "<tr>
      <th>Genauigkeit:</th>
      <td><input type=\"text\" name=\"map_precision\" value=\"95\" size=\"2\" maxlength=\"3\"/>%</td>
    </tr>";
    tableEnd();

    echo button("Zurück", "?page=$page&amp;sub=$sub") . " &nbsp; <input onclick=\"return confirm('Universum wirklich erstellen?')\" type=\"submit\" name=\"submit_create_universe2\" value=\"Weiter\" >";
    echo "</form>";
}

function extendUniverse(ConfigurationService $config): void
{
    global $page;
    global $sub;

    echo "<h2>Universum erweitern</h2>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    echo "<b>Universum (" . $config->param1Int('num_of_sectors') . "x" . $config->param2Int('num_of_sectors') . ") erweitern</b><br><br>";
    echo "Erweitere das Universum. Es werden dabei die bereits gespeicherten Daten übernommen bezüglich der der Aufteilung von Planeten, Sonnensystemen, Gasplaneten, Wurmlöchern etc. Ändere allenfals die Daten unter dem Link \"Universum\".<br><br>";

    echo "Grösse nach dem Ausbau: ";
    //erstellt 2 auswahllisten für die ausbaugrösse
    echo "<select name=\"expansion_sector_x\">";
    for ($x = ($config->param1Int('num_of_sectors') + 1); 10 >= $x; $x++) {
        echo "<option value=\"$x\">$x</option>";
    }
    echo "</select>";
    echo " x ";
    echo "<select name=\"expansion_sector_y\">";
    for ($x = ($config->param2Int('num_of_sectors') + 1); 10 >= $x; $x++) {
        echo "<option value=\"$x\">$x</option>";
    }
    echo "</select>";
    echo "<br>";

    echo "<input onclick=\"return confirm('Universum wirklich erweitern?')\" type=\"submit\" name=\"submit_expansion_universe2\" value=\"Erweitern\" >";
    echo "</form>";
}

function resetUniverse(): void
{
    global $page;
    global $sub;

    echo "<h2>Runde zurücksetzen</h2>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    echo "Runde wirklich zurücksetzen?<br/><br/>";
    echo "<input onclick=\"return confirm('Reset wirklich durchführen?')\" type=\"submit\" name=\"submit_reset2\" value=\"Ja, die gesamte Runde zurücksetzen\" >";
    echo "</form>";
}

function postResetUniverse(UniverseResetService $universeResetService): void
{
    global $page;
    global $sub;

    $universeResetService->reset(false);
    echo "Das Universum wurde zurückgesetzt!<br/><br/>" . button("Weiter", "?page=$page&amp;sub=$sub");
}

function postResetRound(UniverseResetService $universeResetService): void
{
    global $page;
    global $sub;

    $universeResetService->reset();
    echo "Die Runde wurde zurückgesetzt!<br/><br/>" . button("Weiter", "?page=$page&amp;sub=$sub");
}

function addStars(Request $request, UniverseGenerator $universeGenerator): void
{
    global $page;
    global $sub;

    $n = $request->request->getInt('number_of_stars');
    if ($n < 0) {
        $n = 0;
    }
    echo $universeGenerator->addStarSystems($n);
    echo " Sternensysteme wurden hinzugefügt!<br/><br/>" . button("Weiter", "?page=$page&amp;sub=$sub");
}

function universeIndex(
    Request $request,
    ConfigurationService $config,
    UniverseGenerator $universeGenerator,
    CellRepository $cellRepo,
    StarRepository $starRepo,
    PlanetRepository $planetRepo,
    AsteroidsRepository $asteroidsRepo,
    NebulaRepository $nebulaRepo,
    WormholeRepository $wormholeRepo,
    EmptySpaceRepository $emptySpaceRepo
): void {
    global $page;
    global $sub;

    if ($request->request->has('submit_create_universe2')) {
        echo "<h2>Urknall - Schritt 3/3</h2>";
        $output = $universeGenerator->create(
            $request->request->get('map_image'),
            $request->request->getInt('map_precision')
        );
        echo implode('<br>', $output);
        echo "<br/><br/>
      <img src=\"../misc/map.image.php?req_admin\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/><br/><br/>
      <input type=\"button\" value=\"Weiter\" onclick=\"document.location='?page=$page&sub=uni'\" />";
    } else {
        if ($cellRepo->count() == 0) {
            echo "<h2>Urknall - Schritt 1/3</h2>";
            echo "Das Universum existiert noch nicht, bitte prüfe die Einstellungen und klicke auf 'Weiter':<br/><br/>";

?>
            <script type="text/javascript">
                function alignSystemPercentage() {
                    sum = parseInt(document.getElementById('space_percent_solsys').value) +
                        parseInt(document.getElementById('space_percent_asteroids').value) +
                        parseInt(document.getElementById('space_percent_nebulas').value) +
                        parseInt(document.getElementById('space_percent_wormholes').value);
                    res = 100 - sum
                    document.getElementById('space_percent_empty').value = res.toString();
                    if (res < 0 || res > 100)
                        document.getElementById('space_percent_empty').style.color = "red";
                    else
                        document.getElementById('space_percent_empty').style.color = "";
                }

                function alignObjectsInSystemPercentage() {
                    sum = parseInt(document.getElementById('solsys_percent_planet').value) +
                        parseInt(document.getElementById('solsys_percent_asteroids').value);
                    res = 100 - sum
                    document.getElementById('solsys_percent_empty').value = res.toString();
                    if (res < 0 || res > 100)
                        document.getElementById('solsys_percent_empty').style.color = "red";
                    else
                        document.getElementById('solsys_percent_empty').style.color = "";
                }
            </script>
<?PHP

            echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
            echo "Alle Einstellungen werden von der <a href=\"?page=config&sub=editor&category=3\">Konfiguration</a> übernommen!<br/><br/>";

            tableStart("Galaxie", 420);
            echo "<tr>
          <th>Sektoren:</th>
          <td>
            <input type=\"text\" name=\"num_of_sectors_p1\" value=\"" . $config->param1Int('num_of_sectors') . "\" size=\"2\" maxlength=\"2\" />x
            <input type=\"text\" name=\"num_of_sectors_p2\" value=\"" . $config->param2Int('num_of_sectors') . "\" size=\"2\" maxlength=\"2\" />
          </td></tr>";
            echo "<tr>
          <th>Anzahl Zellen pro Sektor:</th>
          <td>
            <input type=\"text\" name=\"num_of_cells_p1\" value=\"" . $config->param1Int('num_of_cells') . "\" size=\"2\" maxlength=\"2\" />x
            <input type=\"text\" name=\"num_of_cells_p2\" value=\"" . $config->param2Int('num_of_cells') . "\" size=\"2\" maxlength=\"2\" />
          </td></tr>";
            echo "</table>";

            tableStart("Verteilung der Systeme", 420);
            echo "<tr>
          <th>Sternensysteme:</th>
          <td><input type=\"text\" name=\"space_percent_solsys\" id=\"space_percent_solsys\" value=\"" . $config->getInt('space_percent_solsys') . "\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
        </tr>";
            echo "<tr>
          <th>Asteroidenfelder:</th>
          <td><input type=\"text\" name=\"space_percent_asteroids\" id=\"space_percent_asteroids\" value=\"" . $config->getInt('space_percent_asteroids') . "\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
        </tr>";
            echo "<tr>
          <th>Nebelwolken:</th>
          <td><input type=\"text\" name=\"space_percent_nebulas\" id=\"space_percent_nebulas\" value=\"" . $config->getInt('space_percent_nebulas') . "\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
        </tr>";
            echo "<tr>
          <th>Wurmlöcher:</th>
          <td><input type=\"text\" name=\"space_percent_wormholes\" id=\"space_percent_wormholes\" value=\"" . $config->getInt('space_percent_wormholes') . "\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%
		  davon <input type=\"text\" name=\"persistent_wormholes_ratio\" id=\"persistent_wormholes_ratio\" value=\"" . $config->getInt('persistent_wormholes_ratio') . "\" size=\"2\" maxlength=\"2\" />% persistent
		  </td>
        </tr>";
            echo "<tr>
          <th>Leerer Raum:</th>
          <td><input type=\"text\" id=\"space_percent_empty\" value=\"\" size=\"2\" maxlength=\"2\" readonly=\"readonly\"/>%</td>
        </tr>";
            echo "</table>";

            tableStart("Sternensystem", 420);
            echo "<tr>
          <th>Objekte pro Sternensystem:</th>
          <td><input type=\"text\" name=\"num_planets_p1\" value=\"" . $config->param1Int('num_planets') . "\" size=\"2\" maxlength=\"2\" /> min,
              <input type=\"text\" name=\"num_planets_p2\" value=\"" . $config->param2Int('num_planets') . "\" size=\"2\" maxlength=\"2\" /> max
          </td></tr>";
            echo "<tr>
          <th>Planeten:</th>
          <td><input type=\"text\" name=\"solsys_percent_planet\" id=\"solsys_percent_planet\" value=\"" . $config->getInt('solsys_percent_planet') . "\" size=\"2\" maxlength=\"2\" onkeyup=\"alignObjectsInSystemPercentage()\" />%</td>
        </tr>";
            echo "<tr>
          <th>Asteroidenfelder:</th>
          <td><input type=\"text\" name=\"solsys_percent_asteroids\" id=\"solsys_percent_asteroids\" value=\"" . $config->getInt('solsys_percent_asteroids') . "\" size=\"2\" maxlength=\"2\" onkeyup=\"alignObjectsInSystemPercentage()\" />%</td>
        </tr>";
            echo "<tr>
          <th>Leerer Raum:</th>
          <td><input type=\"text\" id=\"solsys_percent_empty\" value=\"\" size=\"2\" maxlength=\"2\" readonly=\"readonly\" />%</td>
        </tr>";

            echo "</table>";

            tableStart("Planeten", 420);
            echo "<tr>
          <th>Felder pro Planet:</th>
          <td>
            <input type=\"text\" name=\"planet_fields_p1\" value=\"" . $config->param1Int('planet_fields') . "\" size=\"2\" maxlength=\"2\" /> min,
            <input type=\"text\" name=\"planet_fields_p2\" value=\"" . $config->param2Int('planet_fields') . "\" size=\"2\" maxlength=\"2\" /> max
          </td>
        </tr>";
            echo "</table>";

            echo "<script type=\"text/javascript\">
          alignSystemPercentage();
          alignObjectsInSystemPercentage();
        </script>";

            echo "<br/><input type=\"submit\" name=\"submit_create_universe\" value=\"Weiter\" >";
            echo "</form><br/>";
        } else {
            echo "<h2>Übersicht</h2>";

            $sectorDimensions = $cellRepo->getSectorDimensions();
            $cellDimensions = $cellRepo->getCellDimensions();

            tableStart("Informationen", GALAXY_MAP_WIDTH);
            echo "<tr><th>Sektoren</th><td>" . $sectorDimensions['x'] . " x " . $sectorDimensions['y'] . "</td></tr>";
            echo "<tr><th>Zellen pro Sektor</th><td>" . $cellDimensions['x'] . " x " . $cellDimensions['y'] . "</td></tr>";
            echo "<tr><th>Sterne</th><td>" . nf($starRepo->count()) . "</td></tr>";
            echo "<tr><th>Planeten</th><td>" . nf($planetRepo->count()) . "</td></tr>";
            echo "<tr><th>Asteroidenfelder</th><td>" . nf($asteroidsRepo->count()) . "</td></tr>";
            echo "<tr><th>Nebel</th><td>" . nf($nebulaRepo->count()) . "</td></tr>";
            echo "<tr><th>Wurmlöcher</th><td>" . nf($wormholeRepo->count()) . "</td></tr>";
            echo "<tr><th>Leerer Raum</th><td>" . nf($emptySpaceRepo->count()) . "</td></tr>";
            tableEnd();

            echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";

            echo "<h3>Sternensysteme hinzufügen</h3>";
            echo "Hiermit können <input style=\"width:3em\" type=\"number\" name=\"number_of_stars\" value=\"0\" > Sternensysteme hinzugfügt werden.<br/><br/>";
            echo "<input type=\"submit\" name=\"submit_addstars\" value=\"Ja, Sternensysteme hinzufügen\" ><br><br>";

            echo "<h3>Universum löschen</h3>";
            if ($planetRepo->countWithUser() == 0) {
                echo "Es sind noch keine Planeten im Besitz von Spielern. Das Universum kann ohne Probleme gelöscht werden.<br/><br/>
                    <input type=\"submit\" name=\"submit_galaxy_reset\" value=\"Universum zurücksetzen\" ><br/>";
            } else {
                echo "Es sind bereits Planeten im Besitz von Spielern. Du kannst das Universum zurücksetzen, jedoch werden
                    sämtliche Gebäude, Schiffe, Forschungen etc von den Spielern gelöscht.<br/><br/>
                    <input type=\"submit\" name=\"submit_galaxy_reset\" value=\"Universum zurücksetzen\" onclick=\"return confirm('Universum wirklich zurücksetzen? ALLE Einheiten der Spieler werden gelöscht, jedoch keine Spieleraccounts!')\"><br/>";
            }

            // Reset
            echo "<h3>Runde komplett zurücksetzen</h3>";
            echo "Hiermit kann die gesamte Runde zurückgesetzt werden (User, Allianzen, Planeten).<br/><br/>";
            echo "<input type=\"submit\" name=\"submit_reset\" value=\"Ja, die gesamte Runde zurücksetzen\" ><br><br>";

            echo "</form>";
        }
    }
}
