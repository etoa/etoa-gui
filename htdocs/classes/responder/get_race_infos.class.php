<?PHP

use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseSearch;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;

class GetRaceInfosJsonResponder extends JsonResponder
{
    function getRequiredParams()
    {
        return array('id');
    }

    function getResponse($params)
    {

        $data = array();

        defineImagePaths();

        $val = $params['id'];

        if ($val > 0) {
            /** @var RaceDataRepository $raceRepository */
            $raceRepository = $this->app[RaceDataRepository::class];
            /** @var ShipDataRepository $shipRepository */
            $shipRepository = $this->app[ShipDataRepository::class];
            /** @var DefenseDataRepository $defenseRepository */
            $defenseRepository = $this->app[DefenseDataRepository::class];
            $race = $raceRepository->getRace((int) $val);

            if ($race !== null) {
                ob_start();

                echo BBCodeUtils::toHTML($race->comment) . "<br/><br/>";
                tableStart('', 300);
                echo "<tr><th colspan=\"2\">St&auml;rken / Schw&auml;chen:</th></tr>";
                if ($race->metal !== 1.00) {
                    echo "<tr><th>" . RES_ICON_METAL . "Produktion von " . RES_METAL . ":</td><td>" . StringUtils::formatPercentString($race->metal, true) . "</td></tr>";
                }
                if ($race->crystal !== 1.0) {
                    echo "<tr><th>" . RES_ICON_CRYSTAL . "Produktion von " . RES_CRYSTAL . ":</td><td>" . StringUtils::formatPercentString($race->crystal, true) . "</td></tr>";
                }
                if ($race->plastic !== 1.0) {
                    echo "<tr><th>" . RES_ICON_PLASTIC . "Produktion von " . RES_PLASTIC . ":</td><td>" . StringUtils::formatPercentString($race->plastic, true) . "</td></tr>";
                }
                if ($race->fuel !== 1.0) {
                    echo "<tr><th>" . RES_ICON_FUEL . "Produktion von " . RES_FUEL . ":</td><td>" . StringUtils::formatPercentString($race->fuel, true) . "</td></tr>";
                }
                if ($race->food !== 1.0) {
                    echo "<tr><th>" . RES_ICON_FOOD . "Produktion von " . RES_FOOD . ":</td><td>" . StringUtils::formatPercentString($race->food, true) . "</td></tr>";
                }
                if ($race->power !== 1.0) {
                    echo "<tr><th>" . RES_ICON_POWER . "Produktion von Energie:</td><td>" . StringUtils::formatPercentString($race->power, true) . "</td></tr>";
                }
                if ($race->population !== 1.0) {
                    echo "<tr><th>" . RES_ICON_PEOPLE . "Bevölkerungswachstum:</td><td>" . StringUtils::formatPercentString($race->population, true) . "</td></tr>";
                }
                if ($race->researchTime !== 1.0) {
                    echo "<tr><th>" . RES_ICON_TIME . "Forschungszeit:</td><td>" . StringUtils::formatPercentString($race->researchTime, true, true) . "</td></tr>";
                }
                if ($race->buildTime !== 1.0) {
                    echo "<tr><th>" . RES_ICON_TIME . "Bauzeit:</td><td>" . StringUtils::formatPercentString($race->buildTime, true, true) . "</td></tr>";
                }
                if ($race->fleetTime !== 1.0) {
                    echo "<tr><th>" . RES_ICON_TIME . "Fluggeschwindigkeit:</td><td>" . StringUtils::formatPercentString($race->fleetTime, true) . "</td></tr>";
                }
                tableEnd();
                tableStart('', 500);

                echo  "<tr><th colspan=\"3\">Spezielle Schiffe:</th></tr>";
                $ships = $shipRepository->searchShips(ShipSearch::create()->buildable()->raceId($race->id)->special(false));
                if (count($ships) > 0) {
                    foreach ($ships as $ship) {
                        echo "<tr><td style=\"background:black;\"><img src=\"" . $ship->getImagePath() . "\" style=\"width:40px;height:40px;border:none;\" alt=\"ship" . $ship->id . "\" /></td>
					<th style=\"width:180px;\">" . BBCodeUtils::toHTML($ship->name) . "</th>
					<td>" . BBCodeUtils::toHTML($ship->shortComment) . "</td></tr>";
                    }
                } else
                    echo "<tr><td colspan=\"3\">Keine Rassenschiffe vorhanden</td></tr>";

                tableEnd();
                tableStart('', 500);
                echo  "<tr><th colspan=\"3\">Spezielle Verteidigung:</th></tr>";
                $defense = $defenseRepository->searchDefense(DefenseSearch::create()->raceId($race->id)->buildable());
                if (count($defense) > 0) {
                    foreach ($defense as $def) {
                        echo "<tr><td style=\"background:black;\"><img src=\"" . $def->getImagePath() . "\" style=\"width:40px;height:40px;border:none;\" alt=\"def" . $def->id . "\" /></td>
					<th style=\"width:180px;\">" . BBCodeUtils::toHTML($def->name) . "</th>
					<td>" . BBCodeUtils::toHTML($def->shortComment) . "</td></tr>";
                    }
                } else
                    echo "<tr><td colspan=\"3\">Keine Rassenverteidigung vorhanden</td></tr>";


                tableEnd();

                $data['content'] = ob_get_clean();
            }
        }

        return $data;
    }
}
