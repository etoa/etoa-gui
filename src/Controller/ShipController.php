<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Core\TokenContext;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Support\BBCodeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShipController extends AbstractController
{
    private ShipDataRepository $shipDataRepository;

    public function __construct(ShipDataRepository $shipDataRepository)
    {
        $this->shipDataRepository = $shipDataRepository;
    }

    /**
     * @Route("/api/ships/search", methods={"GET"}, name="api.ship.search")
     */
    public function searchShips(TokenContext $context, Request $request): JsonResponse
    {
        $data = [];
        $shipNames = $this->shipDataRepository->searchShipNames(ShipSearch::create()->showOrBuildable()->nameLike($request->query->get('q')), null, 20);
        $data['count'] = count($shipNames);
        if ($data['count'] > 0) {
            $data['entries'] = [];
            foreach ($shipNames as $shipId => $shipName) {
                $data['entries'][] = [
                    'id' => $shipId,
                    'name' => $shipName,
                ];
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/ships/search-info", methods={"GET"}, name="api.ship.search-info")
     */
    public function searchShipInfo(TokenContext $context, Request $request): JsonResponse
    {
        $data = [];

        $search = ShipSearch::create()->showOrBuildable();
        if (is_numeric($request->query->get('ship'))) {
            $search = $search->id($request->query->getInt('ship'));
        } else {
            $search = $search->name($request->query->get('ship'));
        }

        $ships = array_values($this->shipDataRepository->searchShips($search, null, 1));
        if (count($ships) > 0) {
            $ship = $ships[0];
            if (!in_array($ship->id, $_SESSION['bookmarks']['added'] ?? [], true)) {
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
                        if ($ac = \FleetAction::createFactory($i)) {
                            $acstr .= $ac;
                            if ($x < $accnt - 1) {
                                $acstr .= ", ";
                            }
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

        return new JsonResponse($data);
    }
}
