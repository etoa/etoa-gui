<?PHP

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

echo "<h2>Energie</h2>";

tableStart("Energieproduktion");
echo "<tr><td colspan=\"6\">
<img src=\"misc/powerproduction.image.php\" alt=\"Graph\" />
</td></tr>";

/** @var BuildingRepository */
$buildingRepository = $app[BuildingRepository::class];
/** @var \EtoA\Building\BuildingDataRepository $buildingDataRepository */
$buildingDataRepository = $app['etoa.building.datarepository'];
$buildings = $buildingDataRepository->getBuildingsByType(BUILDING_POWER_CAT);

echo "<tr>
<th>Produktionsanlage</th>
<th>Prod Lvl 1</th>
<th>Kostenfaktor</th>
<th>Prodfaktor</th>
<th>Felder/Lvl</th>
<th>Total gebaut</th>
</tr>";
foreach ($buildings as $building) {
	    $sum = $buildingRepository->getNumberOfBuildings($building->id);

    echo "<tr>
    <td>".$building->name."</td>
    <td>".$building->prodPower."</td>
    <td>".$building->buildCostsFactor."</td>
    <td>".$building->productionFactor."</td>
    <td>".$building->fields."</td>
		<td>".nf($sum)."</td>
		</tr>";
	}

    /** @var \EtoA\Ship\ShipRepository $shipRepository */
    $shipRepository = $app['etoa.ship.repository'];
    /** @var \EtoA\Ship\ShipDataRepository $shipDataRepository */
    $shipDataRepository = $app['etoa.ship.datarepository'];
    $ships = $shipDataRepository->getShipWithPowerProduction();

	foreach ($ships as $ship) {
	    $sum = $shipRepository->getNumberOfShips($ship->id);

    $tpb1 = Planet::getSolarPowerBonus($config->param1Int('planet_temp'),$config->param1Int('planet_temp')+$config->getInt('planet_temp'));
    $tpb2 = Planet::getSolarPowerBonus($config->param2Int('planet_temp')-$config->getInt('planet_temp'),$config->param2Int('planet_temp'));

    echo "<tr>
    <td>".$ship->name."</td>
    <td>".$ship->powerProduction." (".$tpb1." bis +".$tpb2.")</td>
    <td></td>
    <td></td>
    <td></td>
    <td>".nf($sum)."</td>
    </tr>";
}
tableEnd();
