<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarketController extends AbstractGameController
{
    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository
    )
    {
    }

    #[Route('/game/market/home', name: 'game.market.home')]
    public function home(Request $request): Response {
        if ($this->configurationService->getBoolean('market_enabled')) {
            $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
            $market = $this->buildingListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'entity'=>$cp,'building'=>BuildingId::MARKET]);

            //Überprüfung ob der Marktplatz schon gebaut wurde
            if ($market && $market->getCurrentLevel() > 0) {
                if (!$market->isDeactivated()) {
                    return $this->render('game/market/market_home.html.twig', [
                        'marketLevel' => $market->getCurrentLevel(),
                        'planetName' => $cp->getName()
                    ]);
                }

                return $this->render('game/error.html.twig',[
                    'msg' => 'Dieses Gebäude ist noch bis'. StringUtils::formatDate($market->getDeactivated()) . 'deaktiviert!',
                    'path' => $this->generateUrl('game.overview'),
                    'headline' => 'Marktplatz'
                ]);
            }

            return $this->render('game/error.html.twig',[
                'msg' => 'Der Marktplatz wurde noch nicht gebaut.',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Marktplatz'
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Der Marktplatz ist momentan im Spiel deaktiviert.',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Marktplatz'
        ]);
    }

    #[Route('/game/market/sell', name: 'game.market.sell')]
    public function sell(): Response {

    }

    #[Route('/game/market/search', name: 'game.market.search')]
    public function search(): Response {

    }
}