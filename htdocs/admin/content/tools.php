<?php

use EtoA\Support\StringUtils;

echo "<h1>Tools</h1>";

if ($sub == "filesharing") {
    fileSharing();
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
                        <td>" . StringUtils::formatBytes(filesize($file)) . "</td>
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
