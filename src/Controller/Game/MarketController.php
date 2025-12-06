<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Core\CheckboxType;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketService;
use EtoA\Message\MarketReportRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarketController extends AbstractGameController
{
    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly MarketService $marketService,
        private readonly MarketResourceRepository $marketResourceRepository,
        private readonly MarketReportRepository $marketReportRepository
    )
    {
    }

    #[Route('/game/market/home', name: 'game.market.home')]
    public function home(Request $request): Response {
        if ($this->configurationService->getBoolean('market_enabled')) {
            $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
            $market = $this->buildingListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'entity'=>$cp,'building'=>BuildingId::MARKET]);

            //Überprüfung, ob der Marktplatz schon gebaut wurde
            if ($market && $market->getCurrentLevel() > 0) {
                if (!$market->isDeactivated()) {
                    $anzahl = $this->marketService->getOfferCountOnCurrentEntity($cp);
                    $marketLevel = $market->getCurrentLevel();
                    $possible = ($marketLevel - $anzahl) > 0;

                    return $this->render('game/market/market_home.html.twig', [
                        'marketLevel' => $market->getCurrentLevel(),
                        'planetName' => $cp->getName(),
                        'possible' => $possible
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
    public function sell(Request $request): Response {
        $offers = $this->marketResourceRepository->getUserOffers($this->getUser()->getData());
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $market = $this->buildingListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'entity'=>$cp,'building'=>BuildingId::MARKET]);
        $returnFactor =  floor((1 - 1 / ($market ->getCurrentLevel()+ 1)) * 100) / 100;

        $form = $this->createFormBuilder(['offers'=>$offers])
            ->add('offers', CollectionType::class, [
                'entry_type' => CheckboxType::class,
                'label' => false,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Angebot zurückziehen'
            ])

            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('offers')->all() as $marketOffer) {
                if($marketOffer->get('checkbox')->getData()) {
                    $sellResources = $marketOffer->getData()->getSellResources();
                    $returnResources = new PreciseResources();

                    //set exact return factor, depending on planet
                    $currentMarket = $this->buildingListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'entity'=>$marketOffer->getData()->getEntity(),'building'=>BuildingId::MARKET]);
                    $currentReturnFactor = floor((1 - 1 / ($currentMarket ->getCurrentLevel()+ 1)) * 100) / 100;

                    foreach (ResourceNames::NAMES as $rk => $rn) {
                        if ($sellResources->get($rk) > 0) {
                            $returnResources->set($rk, $sellResources->get($rk) * $currentReturnFactor);
                        }
                    }

                    $this->planetRepository->addResources($marketOffer->getData()->getEntity(), $returnResources->metal, $returnResources->crystal, $returnResources->plastic, $returnResources->fuel, $returnResources->food);
                    $this->marketReportRepository->addResourceReport($marketOffer->getData()->getId(), $this->getUser()->getData(), $marketOffer->getData()->getEntity()->getEntity(), null, $sellResources, "rescancel", new BaseResources(), $currentReturnFactor);

                    $this->marketResourceRepository->delete($marketOffer->getData());
                    $this->addFlash('success', "Angebot wurde gelöscht und du hast " . ($currentReturnFactor * 100) . "% der angebotenen Rohstoffe zur&uuml;ck erhalten!");
                }
            }
        }

        return $this->render('game/market/sell.html.twig',[
            'form' => $form,
            'marketLevel' => $market->getCurrentLevel(),
            'planetName' => $cp->getName(),
            'returnFactor' => $returnFactor
        ]);
    }

    #[Route('/game/market/search', name: 'game.market.search')]
    public function search(): Response {

    }
}