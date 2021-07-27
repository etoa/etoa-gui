<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */

$sx_num = $config->param1Int('num_of_sectors');
$sy_num = $config->param2Int('num_of_sectors');
$cx_num = $config->param1Int('num_of_cells');
$cy_num = $config->param2Int('num_of_cells');

/** @var Request */
$request = Request::createFromGlobals();

$sectorMap = new SectorMapRenderer($cx_num, $cy_num);

// Selected cell
if ($request->query->has('cell')) {
    $cell = new Cell($request->query->getInt('cell'));
    if ($cell->isValid()) {
        $sectorMap->setSelectedCell($cell);
    }
}

// View map as user
if ($request->query->has('user')) {
    $sectorMap->setImpersonatedUser($request->query->getInt('user'));
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
