<?PHP

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingSort;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseSort;
use EtoA\Missile\MissileDataRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSort;
use EtoA\Support\FileUtils;
use EtoA\Support\ImageUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologySort;
use EtoA\Universe\Star\SolarTypeRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];

/** @var AllianceTechnologyRepository $allianceTechnologyRepository */
$allianceTechnologyRepository = $app[AllianceTechnologyRepository::class];

/** @var BuildingDataRepository $buildingDataRepository */
$buildingDataRepository = $app[BuildingDataRepository::class];

/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];

/** @var MissileDataRepository $missileDataRepository */
$missileDataRepository = $app[MissileDataRepository::class];

/** @var RaceDataRepository $raceDataRepository */
$raceDataRepository = $app[RaceDataRepository::class];

/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];

/** @var SolarTypeRepository $solarTypeRepository */
$solarTypeRepository = $app[SolarTypeRepository::class];

/** @var TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[TechnologyDataRepository::class];

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

        $sizes = [
            "" => $config->getInt('imagesize'),
            "_middle" => $config->param1Int('imagesize'),
            "_small" => $config->param2Int('imagesize'),
        ];

        $dira = [
            "abuildings" => ["building", array_keys($allianceBuildingRepository->getNames(true))],
            "asteroids" => ["asteroids", range(1, $config->getInt('num_asteroid_images'))],
            "atechnologies" => ["technology", array_keys($allianceTechnologyRepository->getNames(true))],
            "buildings" => ["building", array_keys($buildingDataRepository->getBuildingNames(true, BuildingSort::id()))],
            "defense" => ["def", array_keys($defenseDataRepository->getDefenseNames(true, DefenseSort::id()))],
            "missiles" => ["missile", array_keys($missileDataRepository->getMissileNames(true, true))],
            "races" => ["race", array_keys($raceDataRepository->getRaceNames(true, true))],
            "ships" => ["ship", array_keys($shipDataRepository->getShipNames(true, ShipSort::id()))],
            "stars" => ["star", array_keys($solarTypeRepository->getSolarTypeNames(true, true))],
            "technologies" => ["technology", array_keys($technologyDataRepository->getTechnologyNames(true, TechnologySort::id()))],
            "nebulas" => ["nebula", range(1, $config->getInt('num_nebula_images'))],
            "space" => ["space", range(1, $config->getInt('num_space_images'))],
            "wormholes" => ["wormhole", range(1, $config->getInt('num_wormhole_images'))],
        ];

        foreach ($dira as $sdir => $sd) {
            $sprefix = $sd[0];
            if (is_dir($cdir . "/" . $sdir)) {
                foreach ($sd[1] as $idx) {
                    $baseFileStr = $sdir . "/" . $sprefix . $idx . "." . $baseType;
                    $baseFile = $cdir . "/" . $baseFileStr;
                    if (!is_file($baseFile)) {
                        $results[] = "Basisbild fehlt: $baseFile";
                    } else {
                        foreach ($exts as $ext) {
                            foreach ($sizes as $sizep => $sizew) {
                                $filestr = $sdir . "/" . $sprefix . $idx . $sizep . "." . $ext;
                                $file = $cdir . "/" . $filestr;
                                if (is_file($file)) {
                                    $sa = getimagesize($file);
                                    if ($sa[0] != $sizew) {
                                        $str = "Falsche Grösse: <i>$filestr</i> (" . $sa[0] . " statt $sizew) ";
                                        if (ImageUtils::resizeImage($baseFile, $file, $sizew, $sizew, $ext)) {
                                            $str .= "<span style=\"color:#0f0;\">KORRIGIERT!</span>";
                                        }
                                        $results[] = $str;
                                    }
                                } else {
                                    $str = "Bild fehlt: $filestr ";
                                    if (ImageUtils::resizeImage($baseFile, $file, $sizew, $sizew, $ext)) {
                                        $str .= "<span style=\"color:#0f0;\">KORRIGIERT!</span>";
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
        'imagePackName' => $imagePackName,
        'results' => $results,
        'errorMessage' => $errorMessage,
    ]);
    exit();
}

//
// Imagepack download
//
if (isset($_GET['download'])) {
    $imagepack = $_GET['download'];
    if (isset($imagepacks[$imagepack])) {
        $zipFile = tempnam('sys_get_temp_dir', 'imagepack-' . $imagepack);
        $dir = $imagepacks[$imagepack]['dir'];

        try {
            FileUtils::createZipFromDirectory($dir, $zipFile);
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $imagepack . '.zip');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
            unlink($zipFile);
            exit();
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }
}

$sampleInfoFile = RELATIVE_ROOT . $config->get('default_image_path') . '/' . IMAGEPACK_CONFIG_FILE_NAME;

$required_images = [
    "abuildings" => ["building", $allianceBuildingRepository->getNames(true)],
    "asteroids" => ["asteroids", range(1, $config->getInt('num_asteroid_images'))],
    "atechnologies" => ["technology", $allianceTechnologyRepository->getNames(true)],
    "buildings" => ["building", $buildingDataRepository->getBuildingNames(true, BuildingSort::id())],
    "defense" => ["def", $defenseDataRepository->getDefenseNames(true, DefenseSort::id())],
    "missiles" => ["missile", $missileDataRepository->getMissileNames(true, true)],
    "nebulas" => ["nebula", range(1, $config->getInt('num_nebula_images'))],
    "races" => ["race", $raceDataRepository->getRaceNames(true, true)],
    "ships" => ["ship", $shipDataRepository->getShipNames(true, ShipSort::id())],
    "space" => ["space", range(1, $config->getInt('num_space_images'))],
    "stars" => ["star", $solarTypeRepository->getSolarTypeNames(true, true)],
    "technologies" => ["technology", $technologyDataRepository->getTechnologyNames(true, TechnologySort::id())],
    "wormholes" => ["wormhole", range(1, $config->getInt('num_wormhole_images'))],
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
