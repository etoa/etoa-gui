<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService $config */

$sx_num = $config->param1Int('num_of_sectors');
$sy_num = $config->param2Int('num_of_sectors');
$cx_num = $config->param1Int('num_of_cells');
$cy_num = $config->param2Int('num_of_cells');

$sectorMap = new SectorMapRenderer($cx_num, $cy_num);

// Selected cell
if (isset($_GET['cell'])) {
    $cell = new Cell($_GET['cell']);
    if ($cell->isValid()) {
        $sectorMap->setSelectedCell($cell);
    }
}

// View map as user
if (isset($_GET['user'])) {
    $user = new CurrentUser($_GET['user']);
    if ($user->isValid) {
        $sectorMap->setImpersonatedUser($user);
    }
}

// Draw map
$mapsectors = array();
for ($sy = $sy_num; $sy > 0; $sy--) {
    for ($sx = 1; $sx <= $sx_num; $sx++) {
        $mapsectors[$sy][$sx] = $sectorMap->render($sx, $sy);
    }
}
echo $twig->render('admin/galaxy/map.html.twig', [
    'mapSectors' => $mapsectors,
]);
exit();
