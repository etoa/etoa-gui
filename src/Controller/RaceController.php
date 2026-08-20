<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Core\TokenContext;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseSearch;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RaceController extends AbstractController
{

    public function __construct(
        private readonly RaceDataRepository    $raceRepository,
        private readonly ShipDataRepository    $shipRepository,
        private readonly DefenseDataRepository $defenseRepository,
    )
    {
    }


    #[Route("/api/races/info", name: "api.race.info", methods: "GET")]

    public function getInfo(TokenContext $context, Request $request): JsonResponse
    {
        $raceId = $request->query->getInt('id');

        if ($raceId <= 0) {
            return new JsonResponse();
        }


        $race = $this->raceRepository->getRace($raceId);
        if ($race === null) {
            return new JsonResponse();
        }

        return new JsonResponse([
            'content' => $this->renderView('race/info.html.twig', [
                'race' => $race,
                'ships' => $this->shipRepository->searchShips(
                    ShipSearch::create()->buildable()->raceId($race->getId())->special(false)
                ),
                'defense' => $this->defenseRepository->searchDefense(
                    DefenseSearch::create()->raceId($race->getId())->buildable()
                ),
            ]),
        ]);
    }
}
