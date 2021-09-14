<?PHP

use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use EtoA\User\UserToXml;

/** @var UserToXml $userToXml */
$userToXml = $app[UserToXml::class];

$twig->addGlobal("title", "XML-Import/Export");

$path = $userToXml->getDataDirectory();

//
// Details
//
if (isset($_GET['file'])) {
    $file = $path . "/" . base64_decode($_GET['file'], true);
    if (is_file($file)) {
        $xml = simplexml_load_file($file);

        $twig->addGlobal("subtitle", "Details " . base64_decode($_GET['file'], true) . "");

        echo "<fieldset><legend>Allgemeines</legend>";
        tableStart();
        if (isset($xml->export)) {
            echo "<tr>
                    <th class=\"tbltitle\" style=\"width:150px;\">Erstellt am:</th>
                    <td class=\"tbldata\">" . $xml->export['date'] . "</td>
                </tr>";
        }
        if (isset($xml->account)) {
            echo "<tr>
                    <th class=\"tbltitle\">ID:</th>
                    <td class=\"tbldata\">" . $xml->account->id . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">Nick:</th>
                    <td class=\"tbldata\">" . $xml->account->nick . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">Name:</th>
                    <td class=\"tbldata\">" . $xml->account->name . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">E-Mail:</th>
                    <td class=\"tbldata\">" . $xml->account->email . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">Punkte:</th>
                    <td class=\"tbldata\">" . $xml->account->points . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">Rank:</th>
                    <td class=\"tbldata\">" . $xml->account->rank . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">Online:</th>
                    <td class=\"tbldata\">" . $xml->account->online . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">IP:</th>
                    <td class=\"tbldata\">" . $xml->account->ip . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">Host:</th>
                    <td class=\"tbldata\">" . $xml->account->host . "</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">Allianz:</th>
                    <td class=\"tbldata\">[" . $xml->account->alliance['tag'] . "] " . $xml->account->alliance . " (Id: " . $xml->account->alliance['id'] . ")</td>
                </tr>";
            echo "<tr>
                    <th class=\"tbltitle\">Rasse:</th>
                    <td class=\"tbldata\">" . $xml->account->race . " (Id: " . $xml->account->race['id'] . ")</td>
                </tr>";
        }
        tableEnd();
        echo "</fieldset>";

        echo "<fieldset><legend>Planeten</legend>";
        tableStart();
        if (isset($xml->planets)) {
            if (isset($xml->planets->planet)) {
                foreach ($xml->planets->planet as $p) {
                    echo "<tr>
                            <th class=\"tbltitle\" rowspan=\"7\" style=\"width:200px;\">" . $p['name'] . "<br/> (Id: " . $p['id'] . "";
                    if ($p['id'] == 1) echo " HAUPTPLANET";
                    echo ")</th>
                            <td class=\"tbldata\">Typ:</td>
                            <td class=\"tbldata\">" . $p->type . " (Id: " . $p->type['id'] . ")</td>
                        </tr>";
                    echo "<tr>
                            <td class=\"tbldata\">" . ResourceNames::METAL . ":</td>
                            <td class=\"tbldata\"> " . StringUtils::formatNumber(intval($p->metal)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td class=\"tbldata\">" . ResourceNames::CRYSTAL . ":</td>
                            <td class=\"tbldata\"> " . StringUtils::formatNumber(intval($p->crystal)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td class=\"tbldata\">" . ResourceNames::PLASTIC . ":</td>
                            <td class=\"tbldata\"> " . StringUtils::formatNumber(intval($p->plastic)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td class=\"tbldata\">" . ResourceNames::FUEL . ":</td>
                            <td class=\"tbldata\"> " . StringUtils::formatNumber(intval($p->fuel)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td class=\"tbldata\">" . ResourceNames::FOOD . ":</td>
                            <td class=\"tbldata\"> " . StringUtils::formatNumber(intval($p->food)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td class=\"tbldata\">Bewohner:</td>
                            <td class=\"tbldata\"> " . StringUtils::formatNumber(intval($p->people)) . "</td>
                        </tr>";
                }
            }
        }
        tableEnd();
        echo "</fieldset>";

        echo "<fieldset><legend>Gebäude</legend>";
        tableStart();
        if (isset($xml->buildings)) {
            if (isset($xml->buildings->building)) {
                foreach ($xml->buildings->building as $p) {
                    echo "<tr>
                            <th class=\"tbltitle\" style=\"width:200px;\">" . $p . "</th>
                            <td class=\"tbldata\">Stufe: " . $p['level'] . "</td>
                            <td class=\"tbldata\">Planet: " . $p['planet'] . "</td>
                            <td class=\"tbldata\">Objekt-Id: " . $p['id'] . "</td>
                        </tr>";
                }
            }
        }
        tableEnd();
        echo "</fieldset>";

        echo "<fieldset><legend>Technologien</legend>";
        tableStart("Technologien");
        if (isset($xml->technologies)) {
            if (isset($xml->technologies->technology)) {
                foreach ($xml->technologies->technology as $p) {
                    echo "<tr>
                            <th class=\"tbltitle\" style=\"width:200px;\">" . $p . "</th>
                            <td class=\"tbldata\">Stufe: " . $p['level'] . "</td>
                            <td class=\"tbldata\">Objekt-Id: " . $p['id'] . "</td>
                        </tr>";
                }
            }
        }
        tableEnd();
        echo "</fieldset>";

        echo "<fieldset><legend>Schiffe</legend>";
        tableStart("Schiffe");
        if (isset($xml->ships)) {
            if (isset($xml->ships->ship)) {
                foreach ($xml->ships->ship as $p) {
                    echo "<tr>
                            <th class=\"tbltitle\" style=\"width:200px;\">" . $p . "</th>
                            <td class=\"tbldata\">Anzahl: " . $p['count'] . "</td>
                            <td class=\"tbldata\">Planet: " . $p['planet'] . "</td>
                            <td class=\"tbldata\">Objekt-Id: " . $p['id'] . "</td>
                        </tr>";
                }
            }
        }
        tableEnd();
        echo "</fieldset>";

        echo "<fieldset><legend>Verteidigung</legend>";
        tableStart();
        if (isset($xml->defenses)) {
            if (isset($xml->defenses->defense)) {
                foreach ($xml->defenses->defense as $p) {
                    echo "<tr>
                            <th class=\"tbltitle\" style=\"width:200px;\">" . $p . "</th>
                            <td class=\"tbldata\">Anzahl: " . $p['count'] . "</td>
                            <td class=\"tbldata\">Planet: " . $p['planet'] . "</td>
                            <td class=\"tbldata\">Objekt-Id: " . $p['id'] . "</td>
                        </tr>";
                }
            }
        }
        tableEnd();
        echo "</fieldset>";
    } else {
        error_msg("Datei $file nicht gefunden!");
    }
    echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" value=\"Übersicht\" />";
}

//
// Overview
//
else {
    $twig->addGlobal("subtitle", "Export");

    if (isset($_POST['exportcache'])) {
        try {
            $xmlfile = $userToXml->toCacheFile((int) $_POST['export_user_id']);
            success_msg("Die Userdaten wurden nach [b]" . $xmlfile . "[/b] exportiert.");
        } catch (Exception $ex) {
            error_msg($ex->getMessage());
        }
    }
    if (isset($_POST['exportdl'])) {
        echo "<script type=\"text/javascript\">window.open('misc/user_xml.php?id=" . $_POST['export_user_id'] . "');</script>";
    }

    echo "<p>Bei jeder Löschung eines Spielers werden automatisch seine Daten
        in ein XML-File geschrieben und dieses in einem Ordner abgelegt. Wenn du manuell von
        einem User ein Backup erstellen willst, kannst du das hier tun:</p>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    echo "<p>Spieler wählen: <select name=\"export_user_id\">";
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $userNicks = $userRepository->searchUserNicknames();
    foreach ($userNicks as $userId => $userNick) {
        echo "<option value=\"" . $userId . "\">" . $userNick . "</option>";
    }
    echo "</select>
        <input type=\"submit\" name=\"exportcache\" value=\"Exportieren\" />
        <input type=\"submit\" name=\"exportdl\" value=\"Herunterladen\" /></p>";
    echo "</form>";

    $d = opendir($path);
    $files = array();
    while ($f = readdir($d)) {
        $file = $path . "/" . $f;
        if (is_file($file) && stristr($f, ".xml")) {
            $files[] = $file;
        }
    }
    closedir($d);
    if (count($files) > 0) {
        echo "<table class=\"tb\">
            <tr><th>Datei (Userid_Datum_Zeit)</th>
            <th>Spieler</th>
            <th>Datum</th>
            <th>Optionen</th></tr>";
        foreach ($files as $file) {
            $xml = simplexml_load_file($file);
            echo "<tr>
                <td>" . basename($file) . "</td>
                <td>" . $xml->account->nick . "</td>
                <td>" . $xml->export['date'] . "</td>
                <td>
                    <a href=\"?page=$page&amp;sub=$sub&amp;file=" . base64_encode(basename($file)) . "\">Details</a> &nbsp;
                    <a href=\"" . createDownloadLink($file) . "\">Download</a></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p><i>Noch keine Dateien vorhanden!</i></p>";
    }
}
