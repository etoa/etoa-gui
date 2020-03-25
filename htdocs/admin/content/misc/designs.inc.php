<?PHP

$designs = get_designs();

$customDesignDir = RELATIVE_ROOT.DESIGN_DIRECTORY.'/custom';
$successMessage = null;
$errorMessage = null;
// Design upload
if (isset($_POST['submit'])) {
    if (isset($_FILES["design"])) {
        // Check MIME type
        if (in_array($_FILES["design"]['type'], array('application/zip', 'application/x-zip-compressed', 'application/x-zip'))) {
            // Test if ZIP file can be read
            $zip = new ZipArchive();
            if ($zip->open($_FILES["design"]['tmp_name']) === true) {
                // Iterate over files and detect design info file
                $uploadedDesignDir = null;
                $hasMainTemplateFile = false;
                $hasMainStylesheetFile = false;
                $hasMainScriptFile = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    if (basename($stat['name']) == DESIGN_CONFIG_FILE_NAME) {
                        $uploadedDesignDir = dirname($stat['name']);
                    } else if (basename($stat['name']) == DESIGN_TEMPLATE_FILE_NAME) {
                        $hasMainTemplateFile = true;
                    } else if (basename($stat['name']) == DESIGN_STYLESHEET_FILE_NAME) {
                        $hasMainStylesheetFile = true;
                    } else if (basename($stat['name']) == DESIGN_SCRIPT_FILE_NAME) {
                        $hasMainScriptFile = true;
                    }
                }
                $zip->close();

                // Check if design directory exits
                if ($uploadedDesignDir != null) {
                    // Test naming pattern of design directory
                    if (preg_match('/^[a-z0-9_-]+$/i', $uploadedDesignDir)) {
                        // Test for main template file
                        if ($hasMainTemplateFile) {
                            // Test for main stylesheet file
                            if ($hasMainStylesheetFile) {
                                // Test for main script file
                                if ($hasMainScriptFile) {
                                    // Move uploaded file
                                    $target = $customDesignDir . '/' . $_FILES["design"]['name'];
                                    if (move_uploaded_file($_FILES["design"]['tmp_name'], $target)) {
                                        $zip = new ZipArchive();
                                        if ($zip->open($target) === true) {
                                            // Remove existing design, if it exists
                                            $existingDesign = $customDesignDir . '/' . $uploadedDesignDir;
                                            if (is_dir($existingDesign)) {
                                                rrmdir($existingDesign);
                                            }

                                            // Extract design
                                            $zip->extractTo($customDesignDir);
                                            $zip->close();

                                            // Reload list of designs
                                            $designs = get_designs();
                                        }

                                        // Remove uploaded design archive
                                        unlink($target);
                                        $successMessage = 'Design hochgeladen';
                                    } else {
                                        $errorMessage = 'Fehler beim Upload des Designs!';
                                    }
                                } else {
                                    $errorMessage = 'Ungültiges Design, Script-Datei ' . DESIGN_SCRIPT_FILE_NAME . ' nicht vorhanden!';
                                }
                            } else {
                                $errorMessage = 'Ungültiges Design, Stylesheet-Datei ' . DESIGN_STYLESHEET_FILE_NAME . ' nicht vorhanden!';
                            }
                        } else {
                            $errorMessage = 'Ungültiges Design, Template-Datei ' . DESIGN_TEMPLATE_FILE_NAME . ' nicht vorhanden!';
                        }
                    } else {
                        $errorMessage = 'Ungültiges Design, Verzeichnis-Name enthält ungültige Zeichen (nur a-z, 0-9 sowie _ und - sind erlaubt)!';
                    }
                } else {
                    $errorMessage = 'Ungültiges Design, Info-Datei ' . DESIGN_CONFIG_FILE_NAME . ' nicht vorhanden!';
                }
            } else {
                $errorMessage = 'Kann ZIP-Datei nicht öffnen!';
            }
        } else {
            $errorMessage = 'Keine ZIP-Datei (' . $_FILES["design"]['type'] . ')!';
        }
    }
}
// Design download
else if (!empty($_GET['download'])) {
    $design = $_GET['download'];
    if (isset($designs[$design])) {
        $zipFile = tempnam('sys_get_temp_dir', $design);
        $dir = $designs[$design]['dir'];

        try {
            createZipFromDirectory($dir, $zipFile);
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename='.$design.'.zip');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
            unlink($zipFile);
            exit();
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }
}
// Removal of custom design
else if (!empty($_GET['remove'])) {
    $design = $_GET['remove'];
    if (isset($designs[$design]) && $designs[$design]['custom']) {
        $dir = $designs[$design]['dir'];
        rrmdir($dir);
        $successMessage = 'Design gelöscht';
        $designs = get_designs();
    }
}

// Show all designs
foreach ($designs as $k => $v) {
    $res = dbQuerySave("
    SELECT 
        COUNT(id) as cnt 
    FROM 
        user_properties
    WHERE 
        css_style=?;",
    array(
        $k
    ));
    $arr = mysql_fetch_row($res);
    $designs[$k]['users'] = $arr[0];
    $designs[$k]['default'] = ($k == $cfg->value('default_css_style'));
    // If it is the default design, add all users who have not explicitly selected a design
    if ($k == $cfg->value('default_css_style')) {
        $res = dbQuerySave("
        SELECT
            COUNT(id) as cnt
        FROM
            user_properties
        WHERE
            css_style='';");
        $arr = mysql_fetch_row($res);
        $designs[$k]['users'] += $arr[0];
    }
}

$sampleInfoFile = RELATIVE_ROOT.DESIGN_DIRECTORY."/official/".$cfg->value('default_css_style').'/'.DESIGN_CONFIG_FILE_NAME;

echo $twig->render('admin/misc/designs.html.twig', [
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'designs' => $designs,
    'sampleInfoFile' => file_get_contents($sampleInfoFile),
    'designInfoParams' => [
        'name' => 'Name des Designs (sollte identisch mit dem Namen des Verzeichnisses sein',
        'changed' => 'Datum der letzten Änderung',
        'version' => 'Version',
        'author' => 'Autor',
        'email' => 'E-Mail Adresse des Autors',
        'description' => 'Kurzbeschreibung des Designs',
        'restricted' => 'Wenn auf \'true\' gesetzt, können nur als Admin oder Entwickler markierte Spieler dieses Design auswählen',
    ],
    'knownTemplateVars' => [
        'currentPlanetImage' => 'Pfad zum aktuellen Planetenbild',
        'prevPlanetId' => 'ID des vorherigen Planeten',
        'nextPlanetId' => 'ID des nächsten Planeten',
        'page' => 'Name der aktuellen Seite',
        'selectField' => 'Auswahlfeld (&lt;select&gt;) aller Planeten',
        'planetList' => 'Liste aller Planeten [label, url, current, image]',
        'messages' => 'Anzahl neuer Nachrichten',
        'newreports' => 'Anzahl neuer Berichte',
        'notes' => 'Anzahl vorhandener Notizen',
        'fleetAttack' => 'Anzahl fremder angreifender Flotten',
        'ownFleetCount' => 'Anzahl eigener Flotten',
        'buddys' => 'Anzahl Freunde welche online sind',
        'bugreportUrl' => 'URL zur Seite wo man Fehler melden kann',
        'helpcenterOnclick' => 'JavaScript-Code zum Öffnen des Help-Centers als Popup',
        'urlForum' => 'URL zum Forum',
        'chatOnclick' => 'JavaScript-Code zum Öffnen des Chats',
        'teamspeakOnclick' => 'JavaScript-Code zum Öffnen der TeamSpeak Seite als Popup',
        'rulesOnclick' => 'JavaScript-Code zum Öffnen der Regeln-Seite als Popup',
        'serverTime' => 'Die aktuelle Zeit, wird automatisch aktualisiert',
        'serverTimeUnix' => 'Die aktuelle Zeit als Unix-Zeitstempel',
        'content_for_layout' => 'Die eigentliche Inhalt der Seite',
        'templateDir' => 'Pfad zum Template-Verzeichnis (z.B. zum Laden von Bildern)',
        'topNav' => 'Array mit allen Elementen der Header-Navigation',
        'mainNav' => 'Array mit allen Elementen der Haupt-Spielnavigation',
        'usersOnline' => 'Anzahl User welche online sind',
        'usersTotal' => 'Anzahl registrierter User',
        'renderTime' => 'Zeit welche gebraucht wurde, um den Inhalt der Seite zusammenzustellen',
        'userNick' => 'Name des Spielers',
        'userPoints' => 'Punkte des Spielers',
        'isAdmin' => 'Ist wahr falls der Spieler ein Admin ist',
        'buddyreq' => 'Ist wahr falls Freundschaftsanfragen vorhanden sind',
        'infoText' => 'InGame Infotext, falls definiert',
        'enableKeybinds' => 'Ist wahr wenn die Tastaturnavigation aktiviert ist',
    ],
    'additionalCommonCssFiles' => [
        [
            'name' => 'reset.css',
            'description' => 'Resets all element dimensions',
            'linkUrl' => '../web/css/reset.css',
            'url' => '../../../web/css/reset.css',
        ],
        [
            'name' => 'game.css',
            'description' => 'Common definitions for some ingame elements, colors, icons, ...',
            'linkUrl' => '../web/css/game.css',
            'url' => '../../../web/css/game.css',
        ],
    ],
]);
exit();
