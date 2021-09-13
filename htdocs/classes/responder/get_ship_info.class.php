<?PHP

use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Support\BBCodeUtils;

class GetShipInfoJsonResponder extends JsonResponder
{
    function getRequiredParams()
    {
        return array('ship');
    }

    function getResponse($params)
    {
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $this->app[ShipDataRepository::class];

        $data = array();

        $search = ShipSearch::create()->showOrBuildable();
        if (is_numeric($params['ship'])) {
            $search = $search->id((int) $params['ship']);
        } else {
            $search = $search->name($params['ship']);
        }

        $ships = $shipRepository->searchShips($search, null, 1);
        if (count($ships) > 0) {
            $ship = $ships[0];
            if (!in_array($ship->id, $_SESSION['bookmarks']['added'], true)) {
                $data['id'] = $ship->id;
                $data['name'] = $ship->name;
                $data['image'] = $ship->getImagePath();

                $actions = array_filter(explode(",", $ship->actions));
                $accnt = count($actions);
                $acstr = '';
                if ($accnt > 0) {
                    $acstr = "<br/><b>Fägkeiten:</b> ";
                    $x = 0;
                    foreach ($actions as $i) {
                        if ($ac = FleetAction::createFactory($i)) {
                            $acstr .= $ac;
                            if ($x < $accnt - 1)
                                $acstr .= ", ";
                        }
                        $x++;
                    }
                    $acstr .= "";
                }

                $data['tooltip'] = "<img src=\"" . $ship->getImagePath('medium') . "\" style=\"float:left;margin-right:5px;\">" . BBCodeUtils::toHTML($ship->shortComment) . "<br/>" . $acstr . "<br style=\"clear:both;\"/>";

                $data['launchable'] = $ship->launchable;
            }
        } else {
            $data['error'] = "Schiff nicht gefunden!";
        }

        return $data;
    }
}
