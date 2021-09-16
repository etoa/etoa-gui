<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Core\TokenContext;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
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
}
