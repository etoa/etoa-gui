<?php

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\AccessLogRepository;
use EtoA\Support\StringUtils;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var AccessLogRepository $accessLogRepository */
$accessLogRepository = $app[AccessLogRepository::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

echo "<h1>Tools</h1>";

if ($sub == "accesslog") {
    accessLog($config, $accessLogRepository);
} elseif ($sub == "filesharing") {
    fileSharing();
} elseif ($sub == "ipresolver") {
    ipResolver($networkNameService);
} else {
    toolsIndex();
}

function accessLog(ConfigurationService $config, AccessLogRepository $accessLogRepository)
{
    global $page;
    global $sub;

    echo "<h2>Seitenzugriffe</h2>";

    $frm = new Form("accesslog", "?page=$page&amp;sub=$sub");
    if (isset($_POST['submit_toggle'])) {
        $config->set("accesslog", !$config->getBoolean('accesslog'));
        success_msg("Einstellungen gespeichert");
    }
    if (isset($_POST['submit_truncate'])) {
        $accessLogRepository->deleteAll();
        success_msg("Aufzeichnungen gelöscht");
    }

    echo $frm->begin();
    if ($config->getBoolean('accesslog')) {
        echo "<p>Seitenzugriffe werden aufgezeichnet.
        <input type=\"submit\" value=\"Deaktivieren\" name=\"submit_toggle\"  />";
    } else {
        echo "<p>Seitenzugriffe werden momentan NICHT aufgezeichnet.
        <input type=\"submit\" value=\"Aktivieren\" name=\"submit_toggle\"  />";
    }
    echo " <input type=\"submit\" value=\"Aufzeichnungen löschen\" name=\"submit_truncate\"  /></p>";
    echo $frm->end();

    $domains = array('ingame', 'public', 'admin');

    foreach ($domains as $d) {
        echo "<h3>" . ucfirst($d) . "</h3>";
        echo "<table class=\"tb\" style=\"width:500px\"><tr>
        <th>Ziel</th>
        <th style=\"width:90px\">Zugriffe
        <th style=\"width:200px\">Unterbereiche</th></tr>";
        $counts = $accessLogRepository->getCountsForDomain($d);
        foreach ($counts as $target => $targetCount) {
            echo "<tr><td>" . $target . "</td>
            <td>" . $targetCount . "</td>
            <td style=\"padding:1px\"><table style=\"margin:0;width:100%;border:none;\">";
            $subCounts = $accessLogRepository->getCountsForTarget($d, $target);
            foreach ($subCounts as $subLabel => $count) {
                echo "<tr><td>" . $subLabel . "</td>
                <td style=\"width:60px\">" . $count . "</td></tr>";
            }
            echo "</table></td>
            </tr>";
        }
        echo "</table>";
    }
}

function fileSharing()
{
    global $page;
    global $sub;

    $root = ADMIN_FILESHARING_DIR;

    echo "<h2>Filesharing</h2>";

    if (isset($_GET['action']) && $_GET['action'] == "rename") {
        $f = base64_decode($_GET['file'], true);
        if (md5($f) == $_GET['h']) {
            echo "<h2>Umbenennen</h2>
            <form action=\"?page=$page&sub=$sub\" method=\"post\">";
            echo "Dateiname:
            <input type=\"text\" name=\"rename\" value=\"" . $f . "\" />
            <input type=\"hidden\" name=\"rename_old\" value=\"" . $f . "\" />
            &nbsp; <input type=\"submit\" name=\"rename_submit\" value=\"Umbenennen\" /> &nbsp;
            </form>";
        } else {
            echo "Fehler im Dateinamen!";
        }
    } else {
        if (isset($_FILES["datei"])) {
            if (move_uploaded_file($_FILES["datei"]['tmp_name'], $root . "/" . $_FILES["datei"]['name'])) {
                echo "Die Datei <b>" . $_FILES["datei"]['name'] . "</b> wurde heraufgeladen!<br/><br/>";
            } else {
                echo "Fehler beim Upload!<br/><br/>";
            }
        }

        if (isset($_POST['rename_submit']) && $_POST['rename'] != "") {
            rename($root . "/" . $_POST['rename_old'], $root . "/" . $_POST['rename']);
            echo "Datei wurde umbenannt!<br/><br/>";
        }

        if (isset($_GET['action']) && $_GET['action'] == "delete") {
            $f = base64_decode($_GET['file'], true);
            if (md5($f) == $_GET['h']) {
                @unlink($root . "/" . $f);
                echo "Datei wurde gelöscht!<br/><br/>";
            } else {
                echo "Fehler im Dateinamen!";
            }
        }

        if ($d = opendir($root)) {
            $cnt = 0;
            echo "<table class=\"tb\">
            <tr>
                <th>Datei</th>
                <th>Grösse</th>
                <th>Datum</th>
                <th style=\"width:150px;\">Optionen</th>
            </tr>";
            while ($f = readdir($d)) {
                $file = $root . "/" . $f;
                if (is_file($file) && substr($f, 0, 1) != ".") {
                    $link = "file=" . base64_encode($f) . "&h=" . md5($f);
                    echo "<tr>
                        <td><a href=\"" . createDownloadLink($file) . "\">$f</a></td>
                        <td>" . byte_format(filesize($file)) . "</td>
                        <td>" . StringUtils::formatDate(filemtime($file)) . "</td>
                        <td>
                            <a href=\"?page=$page&amp;sub=$sub&amp;action=rename&" . $link . "\">Umbenennen</a>
                            <a href=\"?page=$page&amp;sub=$sub&amp;action=delete&" . $link . "\" onclick=\"return confirm('Soll diese Datei wirklich gelöscht werden?')\">Löschen</a>
                        </td>
                    </tr>";
                    $cnt++;
                }
            }
            if ($cnt == 0) {
                echo "<tr><td colspan=\"4\"><i>Keine Dateien vorhanden!</i></td></tr>";
            }
            echo "</table>";
            closedir($d);

            echo "<h2>Upload</h2>
            <form method=\"post\" action=\"?page=$page&sub=$sub\" enctype=\"multipart/form-data\">
            <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"10000000\" />
            <input type=\"file\" name=\"datei\" size=\"40\" maxlength=\"10000000\" />
            <input type=\"submit\" name=\"submit\" value=\"Datei heraufladen\" />
            </form>
            ";
        } else {
            echo "Verzeichnis $root kann nicht gefunden werden!";
        }
    }
}

function ipResolver(NetworkNameService $networkNameService)
{
    global $page;
    global $sub;

    $ip = "";
    $host = "";

    if (isset($_POST['resolve'])) {
        if ($_POST['address'] != "") {
            $ip = $_POST['address'];
            $host = $networkNameService->getHost($_POST['address']);
            echo "Die IP <b>" . $ip . "</b> hat den Hostnamen <b>" . $host . "</b><br/>";
        } elseif ($_POST['hostname'] != "") {
            $ip = gethostbyname($_POST['hostname']);
            $host = $_POST['hostname'];
            echo "Die Host <b>" . $host . "</b> hat die IP <b>" . $ip . "</b><br/>";
        }
    }
    if (isset($_POST['whois'])) {
        echo "<div style=\"border:1px solid #fff;background:#000;padding:3px;\">";
        $cmd = "whois " . $_POST['hostname'];
        $out = array();
        exec($cmd, $out);
        foreach ($out as $o) {
            echo "$o <br/>";
        }
        echo "</div>";
    }
    echo "<h2>IP-Resolver</h2>";
    echo '<form action="?page=' . $page . '&amp;sub=' . $sub . '" method="post">';
    echo "IP-Adresse: <input type=\"text\" name=\"address\" value=\"$ip\" /><br/>";
    echo "oder Hostname: <input type=\"text\" name=\"hostname\" value=\"$host\" /><br/><br/>";
    echo "<input type=\"submit\" name=\"resolve\" value=\"Auflösen\" /> &nbsp; ";
    echo "<input type=\"submit\" name=\"whois\" value=\"WHOIS\" /><br/>";
    echo "</form>";
}

function toolsIndex()
{
    echo "Wähle ein Tool aus dem Menü!";
}
