<?PHP
$twig->addGlobal('title', 'Bildpakete verwalten');

$imPackDir = IMAGEPACK_DIRECTORY;
$baseType = "png";

$imagepacks = get_imagepacks();

//
// Check
//
$results = [];
$errorMessage = null;
if (isset($_GET['manage'])) {
    $imagePackName = null;
    if (isset($imagepacks[$_GET['manage']])) {
        $imagepack = $imagepacks[$_GET['manage']];
        $imagePackName = $imagepack['name'];

        $cdir = $imagepack['dir'];
        $exts = $imagepack['extensions'];

        $sizes = array(
            "" => $cfg->value('imagesize'),
            "_middle" => $cfg->p1('imagesize'),
            "_small" => $cfg->p2('imagesize')
        );

        $dira = array(
            "abuildings" => array("building", DBManager::getInstance()->getArrayFromTable("alliance_buildings","alliance_building_id")),
            "atechnologies" => array("technology", DBManager::getInstance()->getArrayFromTable("alliance_technologies","alliance_tech_id")),
            "buildings" => array("building", DBManager::getInstance()->getArrayFromTable("buildings","building_id")),
            "defense" => array("def", DBManager::getInstance()->getArrayFromTable("defense","def_id")),
            "missiles" => array("missile", DBManager::getInstance()->getArrayFromTable("missiles","missile_id")),
            "ships" => array("ship", DBManager::getInstance()->getArrayFromTable("ships","ship_id")),
            "stars" => array("star", DBManager::getInstance()->getArrayFromTable("sol_types","sol_type_id")),
            "technologies" => array("technology", DBManager::getInstance()->getArrayFromTable("technologies","tech_id")),
            "nebulas" => array("nebula",range(1,$cfg->value('num_nebula_images'))),
            "asteroids" => array("asteroids",range(1,$cfg->value('num_asteroid_images'))),
            "space" => array("space",range(1,$cfg->value('num_space_images'))),
            "wormholes" => array("wormhole",range(1,$cfg->value('num_wormhole_images'))),
            "races" => array("race", DBManager::getInstance()->getArrayFromTable("races","race_id")),
        );

        foreach ($dira as $sdir => $sd) {
            $sprefix = $sd[0];
            if (is_dir($cdir."/".$sdir)) {
                foreach ($sd[1] as $idx) {
                    $baseFileStr = $sdir."/".$sprefix.$idx.".".$baseType;
                    $baseFile = $cdir."/".$baseFileStr;
                    if (!is_file($baseFile)) {
                        $results[] = "Basisbild fehlt: $baseFile";
                    } else {
                        foreach ($exts as $ext) {
                            foreach ($sizes as $sizep => $sizew) {
                                $filestr = $sdir."/".$sprefix.$idx.$sizep.".".$ext;
                                $file = $cdir."/".$filestr;
                                if (is_file($file)) {
                                    $sa = getimagesize($file);
                                    if ($sa[0] != $sizew) {
                                        $str = "Falsche Grösse: <i>$filestr</i> (".$sa[0]." statt $sizew) ";
                                        if (resizeImage($baseFile, $file, $sizew, $sizew, $ext)) {
                                            $str.= "<span style=\"color:#0f0;\">KORRIGIERT!</span>";
                                        }
                                        $results[] = $str;
                                    }
                                } else {
                                    $str= "Bild fehlt: $filestr ";
                                    if (resizeImage($baseFile, $file, $sizew,$sizew, $ext)) {
                                        $str.= "<span style=\"color:#0f0;\">KORRIGIERT!</span>";
                                    }
                                    $results[] = $str;
                                }
                            }
                        }
                    }
                }
            } else {
                $results[] = "Verzeichnis fehlt: $sdir";
            }
        }
    } else {
        $errorMessage = 'Ungültiges Bildpaket';
    }

    echo $twig->render('admin/misc/imagepacks-check.html.twig', [
        'results' => $results,
        'errorMessage' => $errorMessage,
    ]);
    exit();
}

//
// Imagepack download
//
if (!empty($_GET['download'])) {
    $imagepack = $_GET['download'];
    if (isset($imagepacks[$imagepack])) {
        $zipFile = tempnam('sys_get_temp_dir', 'imagepack-'.$imagepack);
        $dir = $imagepacks[$imagepack]['dir'];

        try {
            createZipFromDirectory($dir, $zipFile);
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename='.$imagepack.'.zip');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
            unlink($zipFile);
            exit();
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }
}

$sampleInfoFile = RELATIVE_ROOT . $cfg->value('default_image_path') . '/' . IMAGEPACK_CONFIG_FILE_NAME;

$required_images = [
    "abuildings" => array("building", DBManager::getInstance()->getArrayFromTable("alliance_buildings",["alliance_building_id", "alliance_building_name"],"alliance_building_id")),
    "asteroids" => array("asteroids",range(1,$cfg->value('num_asteroid_images'))),
    "atechnologies" => array("technology", DBManager::getInstance()->getArrayFromTable("alliance_technologies",["alliance_tech_id","alliance_tech_name"],"alliance_tech_id")),
    "buildings" => array("building", DBManager::getInstance()->getArrayFromTable("buildings",["building_id","building_name"],"building_id")),
    "defense" => array("def", DBManager::getInstance()->getArrayFromTable("defense",["def_id","def_name"],"def_id")),
    "missiles" => array("missile", DBManager::getInstance()->getArrayFromTable("missiles",["missile_id","missile_name"],"missile_id")),
    "nebulas" => array("nebula",range(1,$cfg->value('num_nebula_images'))),
    "races" => array("race", DBManager::getInstance()->getArrayFromTable("races",["race_id","race_name"],"race_id")),
    "ships" => array("ship", DBManager::getInstance()->getArrayFromTable("ships",["ship_id", "ship_name"],"ship_id")),
    "space" => array("space",range(1,$cfg->value('num_space_images'))),
    "stars" => array("star", DBManager::getInstance()->getArrayFromTable("sol_types",["sol_type_id","sol_type_name"],"sol_type_id")),
    "technologies" => array("technology", DBManager::getInstance()->getArrayFromTable("technologies",["tech_id","tech_name"],"tech_id")),
    "wormholes" => array("wormhole",range(1,$cfg->value('num_wormhole_images'))),
];
echo $twig->render('admin/misc/imagepacks.html.twig', [
    'errorMessage' => $errorMessage,
    'imagepacks' => $imagepacks,
    'baseType' => $baseType,
    'sampleInfoFile' => file_get_contents($sampleInfoFile),
    'requiredImages' => $required_images,
    'infoParams' => [
        'name' => 'Name des Designs (sollte identisch mit dem Namen des Verzeichnisses sein',
        'changed' => 'Datum der letzten Änderung',
        'version' => 'Version',
        'author' => 'Autor',
        'email' => 'E-Mail Adresse des Autors',
        'description' => 'Kurzbeschreibung des Designs',
        'extensions' => 'Unterstützte Bildformate (png, gif, jpg)',
    ],
]);
exit();
