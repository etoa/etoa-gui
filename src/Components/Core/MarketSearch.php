<?php

namespace EtoA\Components\Core;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Entity;
use EtoA\Entity\MarketAuction;
use EtoA\Entity\MarketResource;
use EtoA\Entity\MarketShip;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetShipRepository;
use EtoA\Fleet\FleetStatus;
use EtoA\Form\Type\Core\CheckboxType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Market\MarketHandler;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Market\TradePoints;
use EtoA\Message\MarketReportRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipId;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserRatingService;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/market_search.html.twig')]
class MarketSearch extends AbstractGameController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true)]
    public string $searchCat = 'resources';

    #[LiveProp(writable: true)]
    public User $user;

    #[LiveProp(writable: true)]
    public bool $filterPayable = false;

    // Resource filters - supply (Angebot)
    #[LiveProp(writable: true)]
    public bool $supplyMetal = true;

    #[LiveProp(writable: true)]
    public bool $supplyCrystal = true;

    #[LiveProp(writable: true)]
    public bool $supplyPlastic = true;

    #[LiveProp(writable: true)]
    public bool $supplyFuel = true;

    #[LiveProp(writable: true)]
    public bool $supplyFood = true;

    // Resource filters - demand (Preis)
    #[LiveProp(writable: true)]
    public bool $demandMetal = true;

    #[LiveProp(writable: true)]
    public bool $demandCrystal = true;

    #[LiveProp(writable: true)]
    public bool $demandPlastic = true;

    #[LiveProp(writable: true)]
    public bool $demandFuel = true;

    #[LiveProp(writable: true)]
    public bool $demandFood = true;

    // Ship filters - demand only
    #[LiveProp(writable: true)]
    public bool $shipDemandMetal = true;

    #[LiveProp(writable: true)]
    public bool $shipDemandCrystal = true;

    #[LiveProp(writable: true)]
    public bool $shipDemandPlastic = true;

    #[LiveProp(writable: true)]
    public bool $shipDemandFuel = true;

    #[LiveProp(writable: true)]
    public bool $shipDemandFood = true;

    #[LiveProp(writable: true)]
    public bool $shipFilterPayable = false;

    #[LiveProp]
    public ?MarketAuction $marketAuction = null;

    private array $supplyTotal = [];
    private array $demandTotal = [];

    #[LiveProp]
    public int $cnt = 0;

    #[LiveProp]
    public int $cntError = 0;

    #[LiveProp]
    public string $errorMessage = '';

    #[LiveProp]
    public string $successMessage = '';

    public function __construct(
        private readonly MarketResourceRepository $marketResourceRepository,
        private readonly MarketShipRepository $marketShipRepository,
        private readonly MarketAuctionRepository $marketAuctionRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly ConfigurationService $configurationService,
        private readonly RequestStack $requestStack,
        private readonly EntityService $entityService,
        private readonly ShipDataRepository $shipDataRepository,
        private readonly FleetRepository $fleetRepository,
        private readonly FleetShipRepository $fleetShipRepository,
        private readonly MarketReportRepository $marketReportRepository,
        private readonly UserRatingService $userRatingService,
        private readonly UserMultiRepository $userMultiRepository,
        private readonly LogRepository $logRepository,
        private readonly MarketHandler $marketHandler,
        private readonly RuntimeDataStore $runtimeDataStore
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        if($this->marketAuction) {
            $this->searchCat = 'detail';
            return $this->createFormBuilder(
                $this->marketAuction,
                ['attr'=>['data-action'=> 'live#action:prevent', 'data-live-action-param'=> 'buyOffer']]
            )
                ->add('buy0_offer', TextType::class, [
                    'empty_data' => 0,
                    'attr' => [
                        'onkeyup'=>"calcMarketAuctionPrice(0)",
                        "size"=>"9",
                        "maxlength"=>"15"
                    ],
                    'mapped' => false
                ])
                ->add('buy1_offer', TextType::class, [
                    'empty_data' => 0,
                    'attr' => [
                        'onkeyup'=>"calcMarketAuctionPrice(0)",
                        "size"=>"9",
                        "maxlength"=>"15"
                    ],
                    'mapped' => false
                ])
                ->add('buy2_offer', TextType::class, [
                    'empty_data' => 0,
                    'attr' => [
                        'onkeyup'=>"calcMarketAuctionPrice(0)",
                        "size"=>"9",
                        "maxlength"=>"15"
                    ],
                    'mapped' => false
                ])
                ->add('buy3_offer', TextType::class, [
                    'empty_data' => 0,
                    'attr' => [
                        'onkeyup'=>"calcMarketAuctionPrice(0)",
                        "size"=>"9",
                        "maxlength"=>"15"
                    ],
                    'mapped' => false
                ])
                ->add('buy4_offer', TextType::class, [
                    'empty_data' => 0,
                    'attr' => [
                        'onkeyup'=>"calcMarketAuctionPrice(0)",
                        "size"=>"9",
                        "maxlength"=>"15"
                    ],
                    'mapped' => false
                ])
                ->add('auction_show_last_update', HiddenType::class, [
                    'data' => '0',
                    'mapped' => false
                ])
                ->add('auction_rest_time', HiddenType::class, [
                    'attr'=>['value'=> $this->marketAuction->getDateEnd() - time()],
                    'mapped' => false
                ])
                ->add('ress_metal', HiddenType::class, [
                    'label' => false,
                    'attr'=>['value'=>$this->getCurrentPlanet()->getResMetal()],
                    'mapped' => false
                ])
                ->add('ress_crystal', HiddenType::class, [
                    'label' => false,
                    'attr'=>['value'=>$this->getCurrentPlanet()->getResCrystal()],
                    'mapped' => false
                ])
                ->add('ress_plastic', HiddenType::class, [
                    'label' => false,
                    'attr'=>['value'=>$this->getCurrentPlanet()->getResPlastic()],
                    'mapped' => false
                ])
                ->add('ress_fuel', HiddenType::class, [
                    'label' => false,
                    'attr'=>['value'=>$this->getCurrentPlanet()->getResFuel()],
                    'mapped' => false
                ])
                ->add('ress_food', HiddenType::class, [
                    'label' => false,
                    'attr'=>['value'=>$this->getCurrentPlanet()->getResFood()],
                    'mapped' => false
                ])

                ->add('sell0', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getSell0()]
                ])
                ->add('sell1', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getSell1()]
                ])
                ->add('sell2', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getSell2()]
                ])
                ->add('sell3', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getSell3()]
                ])
                ->add('sell4', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getSell4()]
                ])

                ->add('buy0', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getBuy0()]
                ])
                ->add('buy1', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getBuy1()]
                ])
                ->add('buy2', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getBuy2()]
                ])
                ->add('buy3', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getBuy3()]
                ])
                ->add('buy4', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getBuy4()]
                ])

                ->add('currency0', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getCurrency0()?1:0]
                ])
                ->add('currency1', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getCurrency1()?1:0]
                ])
                ->add('currency2', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getCurrency2()?1:0]
                ])
                ->add('currency3', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getCurrency3()?1:0]
                ])
                ->add('currency4', HiddenType::class, [
                    'label' => false,
                    'mapped' => false,
                    'attr'=>['value'=>$this->marketAuction->getCurrency4()?1:0]
                ])
                ->getForm();
        } else {
            $offers = $this->getFilteredOffers();

            return $this->createFormBuilder(
                ['offers' => $offers],
                ['attr'=>['data-action'=> 'live#action:prevent', 'data-live-action-param'=> 'buyOffer']]
            )
                ->add('offers', CollectionType::class, [
                    'entry_type' => CheckboxType::class,
                    'label' => false,
                    'required' => false,
                ])
                ->getForm();
        }
    }

    #[LiveAction]
    public function buyOffer(): void
    {
        $this->submitForm();

        $this->supplyTotal = array_fill(0, count(ResourceNames::NAMES), 0);
        $this->demandTotal = array_fill(0, count(ResourceNames::NAMES), 0);
        $this->cnt = 0;
        $this->cntError = 0;

        if($this->marketAuction) {
            $this->processAuctionOffer($this->marketAuction);
        }

        if($this->form->has('offers')) {
            foreach ($this->form->get('offers')->all() as $marketOffer) {
                if($marketOffer->get('checkbox')->getData()) {
                    match ($this->searchCat) {
                        'resources' => $this->processRessOffer($marketOffer->getData()),
                        'ships' => $this->processShipOffer($marketOffer->getData()),
                        default => [],
                    };
                }
            }
            $this->marketHandler->addResToRate($this->supplyTotal, $this->demandTotal);
        }
    }

    public function getFilteredOffers(): array
    {
        return match ($this->searchCat) {
            'resources' => $this->getFilteredResourceOffers(),
            'ships' => $this->getFilteredShipOffers(),
            'auctions' => $this->getFilteredAuctionOffers(),
            'detail' => [$this->marketAuction],
            default => [],
        };
    }

    private function getFilteredResourceOffers(): array
    {
        $offers = $this->marketResourceRepository->getBuyableOffers($this->user);

        // Apply filters
        $filtered = [];
        foreach ($offers as $offer) {
            // Check supply filters
            if (!$this->supplyMetal && $offer->getSellResources()->metal > 0) continue;
            if (!$this->supplyCrystal && $offer->getSellResources()->crystal > 0) continue;
            if (!$this->supplyPlastic && $offer->getSellResources()->plastic > 0) continue;
            if (!$this->supplyFuel && $offer->getSellResources()->fuel > 0) continue;
            if (!$this->supplyFood && $offer->getSellResources()->food > 0) continue;

            // Check demand filters
            if (!$this->demandMetal && $offer->getBuyResources()->metal > 0) continue;
            if (!$this->demandCrystal && $offer->getBuyResources()->crystal > 0) continue;
            if (!$this->demandPlastic && $offer->getBuyResources()->plastic > 0) continue;
            if (!$this->demandFuel && $offer->getBuyResources()->fuel > 0) continue;
            if (!$this->demandFood && $offer->getBuyResources()->food > 0) continue;

            // Check if payable
            if ($this->filterPayable) {
                $planet = $this->getCurrentPlanet();
                $canAfford = true;
                
                if ($offer->getBuyResources()->metal > $planet->getResMetal()) $canAfford = false;
                if ($offer->getBuyResources()->crystal > $planet->getResCrystal()) $canAfford = false;
                if ($offer->getBuyResources()->plastic > $planet->getResPlastic()) $canAfford = false;
                if ($offer->getBuyResources()->fuel > $planet->getResFuel()) $canAfford = false;
                if ($offer->getBuyResources()->food > $planet->getResFood()) $canAfford = false;
                
                if (!$canAfford) continue;
            }

            $filtered[] = $offer;
        }

        return $filtered;
    }

    private function getFilteredShipOffers(): array
    {
        $offers = $this->marketShipRepository->getBuyableOffers($this->user);

        // Apply filters
        $filtered = [];
        foreach ($offers as $offer) {
            // Check demand filters
            if (!$this->shipDemandMetal && $offer->getCosts()->metal > 0) continue;
            if (!$this->shipDemandCrystal && $offer->getCosts()->crystal > 0) continue;
            if (!$this->shipDemandPlastic && $offer->getCosts()->plastic > 0) continue;
            if (!$this->shipDemandFuel && $offer->getCosts()->fuel > 0) continue;
            if (!$this->shipDemandFood && $offer->getCosts()->food > 0) continue;

            // Check if payable
            if ($this->shipFilterPayable) {
                $planet = $this->getCurrentPlanet();
                $canAfford = true;
                
                if ($offer->getCosts()->metal > $planet->getResMetal()) $canAfford = false;
                if ($offer->getCosts()->crystal > $planet->getResCrystal()) $canAfford = false;
                if ($offer->getCosts()->plastic > $planet->getResPlastic()) $canAfford = false;
                if ($offer->getCosts()->fuel > $planet->getResFuel()) $canAfford = false;
                if ($offer->getCosts()->food > $planet->getResFood()) $canAfford = false;
                
                if (!$canAfford) continue;
            }

            $filtered[] = $offer;
        }

        return $filtered;
    }

    private function getFilteredAuctionOffers(): array
    {
        return $this->marketAuctionRepository->getBuyableAuctions($this->user);
    }

    private function getCurrentPlanet():Planet
    {
        $request = $this->requestStack->getCurrentRequest();
        return $this->planetRepository->find($request->getSession()->get('cpid'));
    }

    public function getMarketLevel(): int
    {
        $planet = $this->getCurrentPlanet();
        $market = $this->buildingListItemRepository->findOneBy([
            'user' => $this->user,
            'entity' => $planet,
            'building' => BuildingId::MARKET
        ]);

        return $market?->getCurrentLevel() ?? 0;
    }

    public function canShowResources(): bool
    {
        return $this->configurationService->getInt('min_market_level_ress') <= $this->getMarketLevel();
    }

    public function canShowShips(): bool
    {
        return $this->configurationService->getInt('min_market_level_ship') <= $this->getMarketLevel();
    }

    public function canShowAuctions(): bool
    {
        return $this->configurationService->getInt('min_market_level_auction') <= $this->getMarketLevel();
    }

    public function getResourceNames(): array
    {
        return ResourceNames::NAMES;
    }

    public function getRessourceCount(BaseResources $resources): int
    {
        $count = 0;
        foreach ($this->getResourceNames() as $rk => $rn) {
            if ($resources->get($rk) > 0)
                $count++;
        }

        return $count;
    }

    public function getDuration(MarketResource|MarketShip|MarketAuction $offer): float
    {
        $sellerEntity = $offer->getEntity()->getEntity();
        $specialist = $this->user->getSpecialist();
        $specialistTradeTime = $specialist ? $specialist->getTradeTime() : 1;
        $dist = $this->entityService->distance($sellerEntity,  $this->getCurrentPlanet()->getEntity());
        $tradeShip = $this->shipDataRepository->getShip(ShipId::MARKET, false);

        return ceil($dist / ($tradeShip->getSpeed() * $specialistTradeTime) * 3600 + $tradeShip->getTimeToStart() + $tradeShip->getTimeToLand());
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

    private function processRessOffer(MarketResource $offer): void
    {
        // Prüft, ob Angebot noch vorhanden ist
        $offer = $this->marketResourceRepository->getBuyableOffer($offer->getId(), $this->user, $this->user->getAlliance());

        if ($offer !== null) {
            $ok = true;
            $buyarr = array();
            $sellarr = array();
            $buyResource = $offer->getBuyResources();
            $sellResources = $offer->getSellResources();
            $ress = ['Metal','Crystal','Plastic','Fuel','Food'];

            foreach (ResourceNames::NAMES as $rk => $rn) {
                $buyarr[$rk] = $buyResource->get($rk);
                $sellarr[$rk] = $sellResources->get($rk);

                if ($offer->{'getBuy'.$rk}() > $this->getCurrentPlanet()->{'getRes'.$ress[$rk]}()) {
                    $ok = false;
                    break;
                }
            }

            if ($ok) {
                $this->planetRepository->removeResources($this->getCurrentPlanet(), $buyResource);

                $sellerEntity = $offer->getEntity()->getEntity();
                $ownEntity = $this->getCurrentPlanet()->getEntity();

                $tradeShip = $this->shipDataRepository->getShip(ShipId::MARKET, false);
                $numSellerShip = ($tradeShip->getCapacity() > 0) ? ceil(array_sum($sellarr) / $tradeShip->getCapacity()) : 1;

                $sellerSpecialist = $offer->getUser()->getSpecialist();
                $specialist = $this->user->getSpecialist();

                $dist = $this->entityService->distance($sellerEntity, $ownEntity);
                $sellerFlighttime = ceil($dist / (($sellerSpecialist !== null ? $sellerSpecialist->getTradeTime() : 1) * $tradeShip->getSpeed() / 3600) + $tradeShip->getTimeToStart() + $tradeShip->getTimeToLand());
                $buyerFlighttime = ceil($dist / (($specialist !== null ? $specialist->getTradeTime() : 1) * $tradeShip->getSpeed() / 3600) + $tradeShip->getTimeToStart() + $tradeShip->getTimeToLand());

                $launchtime = time();
                $sellerLandtime = $launchtime + $sellerFlighttime;
                $buyerLandtime = $launchtime + $buyerFlighttime;


                // Fleet Seller -> Buyer
                $sellerFid = $this->fleetRepository->add($this->user, $launchtime, (int)$buyerLandtime, $sellerEntity, $ownEntity, FleetAction::MARKET, FleetStatus::DEPARTURE->value, $sellResources);
                $this->fleetShipRepository->addShipsToFleet($sellerFid, $this->shipDataRepository->find(ShipId::MARKET), $numSellerShip);

                $numBuyerShip = ($tradeShip->getCapacity() > 0) ? ceil(array_sum($buyarr) / $tradeShip->getCapacity()) : 1;

                // Fleet Buyer->Seller
                $buyerFid = $this->fleetRepository->add($offer->getUser(), $launchtime, (int)$sellerLandtime, $ownEntity, $sellerEntity, FleetAction::MARKET, FleetStatus::DEPARTURE->value, $buyResource);
                $this->fleetShipRepository->addShipsToFleet($buyerFid, $this->shipDataRepository->find(ShipId::MARKET), $numBuyerShip);

                // Add values for market rate calculation and
                foreach (ResourceNames::NAMES as $rk => $rn) {
                    $this->supplyTotal[$rk] += $sellarr[$rk];
                    $this->demandTotal[$rk] += $buyarr[$rk];
                }

                // Send report to seller
                $this->marketReportRepository->addResourceReport($offer->getId(), $offer->getUser(), $offer->getEntity()->getEntity(), $this->user, $sellResources, "ressold", $buyResource, 1.0, null, 0, $this->getCurrentPlanet()->getEntity(), $sellerFid, $buyerFid);

                // Send report to buyer (the current user)
                $this->marketReportRepository->addResourceReport($offer->getId(), $this->user, $this->getCurrentPlanet()->getEntity(), $offer->getUser(), $sellResources, "resbought", $buyResource, 1.0, null, 0, $offer->getEntity()->getEntity(), $buyerFid, $sellerFid);

                // Add market ratings
                $this->userRatingService->addTradeRating(
                    $this->user,
                    TradePoints::POINTS_PER_TRADE,
                    false,
                    'Handel #' . $offer->getId() . ' mit ' . $offer->getUser()->getId()
                );
                if (strlen($offer->getText()) > TradePoints::POINTS_TRADE_TEXT_MIN_LENGTH) {
                    $this->userRatingService->addTradeRating(
                        $offer->getUser(),
                        TradePoints::POINTS_PER_TRADE + TradePoints::POINTS_PER_TRADE_TEXT,
                        true,
                        'Handel #' . $offer->getId() . ' mit ' . $this->user->getId()
                    );
                } else {
                    $this->userRatingService->addTradeRating(
                        $offer->getUser(),
                        TradePoints::POINTS_PER_TRADE,
                        true,
                        'Handel #' . $offer->getId() . ' mit ' . $this->user->getId()
                    );
                }

                // Log schreiben, falls dieser Handel regelwidrig ist
                $isMultiWith = $this->userMultiRepository->existsEntryWith($this->user, $offer->getUser());
                if ($isMultiWith) {
                    $seller = $offer->getUser();
                    $this->logRepository->add(LogFacility::MULTITRADE, LogSeverity::INFO, "[B]" . $this->user->getNick() . "[/B] hat von [B]" . $seller . "[/B] Rohstoffe gekauft:\n\n" . ResourceNames::METAL . ": " . StringUtils::formatNumber($offer->getSell0()) . "\n" . ResourceNames::CRYSTAL . ": " . StringUtils::formatNumber($offer->getSell1()) . "\n" . ResourceNames::PLASTIC . ": " . StringUtils::formatNumber($offer->getSell2()) . "\n" . ResourceNames::FUEL . ": " . StringUtils::formatNumber($offer->getSell3()) . "\n" . ResourceNames::FOOD . ": " . StringUtils::formatNumber($offer->getSell4()) . "\n\nDies hat ihn folgende Rohstoffe gekostet:\n" . ResourceNames::METAL . ": " . StringUtils::formatNumber($offer->getBuy0()) . "\n" . ResourceNames::CRYSTAL . ": " . StringUtils::formatNumber($offer->getBuy1()) . "\n" . ResourceNames::PLASTIC . ": " . StringUtils::formatNumber($offer->getBuy2()) . "\n" . ResourceNames::FUEL . ": " . StringUtils::formatNumber($offer->getBuy3()) . "\n" . ResourceNames::FOOD . ": " . StringUtils::formatNumber($offer->getBuy4()));
                }

                // Angebot löschen
                $this->marketResourceRepository->delete($offer);

                // Zählt die erfolgreich abgewickelten Angebote
                $this->cnt++;
            }
            else {
                $this->cntError++;
            }
        } else {
            $this->cntError++;
        }
    }

    private function processShipOffer(MarketShip $offer):void
    {
        $offer = $this->marketShipRepository->getBuyableOffer($offer->getId(), $this->user, $this->user->getAlliance());
        // Prüft, ob Angebot noch vorhanden ist
        if ($offer !== null) {
            $ok = true;
            $buyarr = array();
            $costs = $offer->getCosts();
            $ress = ['Metal','Crystal','Plastic','Fuel','Food'];

            foreach (ResourceNames::NAMES as $rk => $rn) {
                $buyarr[$rk] = $costs->get($rk);

                if ($offer->{'getCosts'.$rk}() > $this->getCurrentPlanet()->{'getRes'.$ress[$rk]}()) {
                    $ok = false;
                    break;
                }
            }

            // Prüft, ob genug Rohstoffe vorhanden sind
            if ($ok) {
                // Rohstoffe vom Käuferplanet abziehen
                $this->planetRepository->removeResources($this->getCurrentPlanet(), $costs);

                $sellerEntity = $offer->getEntity()->getEntity();
                $ownEntity = $this->getCurrentPlanet()->getEntity();

                $tradeShip = $this->shipDataRepository->getShip(ShipId::MARKET, false);

                $sellerSpecialist = $offer->getUser()->getSpecialist();
                $specialist = $this->user->getSpecialist();

                $dist = $this->entityService->distance($sellerEntity, $ownEntity);
                $sellerFlighttime = ceil($dist / (($sellerSpecialist !== null ? $sellerSpecialist->getTradeTime() : 1) * $tradeShip->getSpeed() / 3600) + $tradeShip->getTimeToStart() + $tradeShip->getTimeToLand());
                $buyerFlighttime = ceil($dist / (($specialist !== null ? $specialist->getTradeTime() : 1) * $tradeShip->getSpeed() / 3600) + $tradeShip->getTimeToStart() + $tradeShip->getTimeToLand());

                $launchtime = time();
                $sellerLandtime = $launchtime + $sellerFlighttime;
                $buyerLandtime = $launchtime + $buyerFlighttime;

                // Fleet Seller -> Buyer
                $sellerFid = $this->fleetRepository->add($this->user, $launchtime, (int)$buyerLandtime, $sellerEntity, $ownEntity, FleetAction::MARKET, FleetStatus::DEPARTURE->value, new BaseResources());
                $this->fleetShipRepository->addShipsToFleet($sellerFid, $offer->getShip(), $offer->getCount());

                $numBuyerShip = ($tradeShip->getCapacity() > 0) ? ceil(array_sum($buyarr) / $tradeShip->getCapacity()) : 1;

                // Fleet Buyer->Seller
                $buyerFid = $this->fleetRepository->add($offer->getUser(), $launchtime, (int)$sellerLandtime, $ownEntity, $sellerEntity, FleetAction::MARKET, FleetStatus::DEPARTURE->value, $costs);
                $this->fleetShipRepository->addShipsToFleet($buyerFid,  $this->shipDataRepository->find(ShipId::MARKET), $numBuyerShip);

                $this->cnt++;

                // Send report to seller
                $this->marketReportRepository->addShipReport($offer->getId(), $offer->getUser(), $offer->getEntity()->getEntity(), $this->user, $offer->getShip(), $offer->getCount(), "shipsold", $costs, 1.0, null, 0, $this->getCurrentPlanet()->getEntity(), $sellerFid, $buyerFid);

                // Send report to buyer (the current user)
                $this->marketReportRepository->addShipReport($offer->getId(), $this->user, $this->getCurrentPlanet()->getEntity(), $offer->getUser(), $offer->getShip(), $offer->getCount(), "shipbought", $costs, 1.0, null, 0, $offer->getEntity()->getEntity(), $buyerFid, $sellerFid);

                // Add market ratings
                $this->userRatingService->addTradeRating(
                    $this->user,
                    TradePoints::POINTS_PER_TRADE,
                    false,
                    'Handel #' . $offer->getId() . ' mit ' . $offer->getUser()->getId()
                );
                if (strlen($offer->getText()) > TradePoints::POINTS_TRADE_TEXT_MIN_LENGTH) {
                    $this->userRatingService->addTradeRating(
                        $offer->getUser(),
                        TradePoints::POINTS_PER_TRADE + TradePoints::POINTS_PER_TRADE_TEXT,
                        true,
                        'Handel #' . $offer->getId() . ' mit ' . $this->user->getId()
                    );
                } else {
                    $this->userRatingService->addTradeRating(
                        $offer->getUser(),
                        TradePoints::POINTS_PER_TRADE,
                        true,
                        'Handel #' . $offer->getId() . ' mit ' . $this->user->getId()
                    );
                }

                //Log schreiben, falls dieser Handel regelwidrig ist
                /** @var UserMultiRepository $userMultiRepository */
                $isMultiWith = $this->userMultiRepository->existsEntryWith($this->user, $offer->getUser());
                if ($isMultiWith) {
                    /** @var ShipDataRepository $shipRepository */
                    $seller = $offer->getUser();
                    $this->logRepository->add(LogFacility::MULTITRADE, LogSeverity::INFO, "[B]" . $this->user->getNick() . "[/B] hat von [B]" . $seller . "[/B] Schiffe gekauft:\n\n" . $offer->getCount() . " " . $offer->getShip()->getName() . "\n\nund das zu folgendem Preis:\n\n" . ResourceNames::METAL . ": " . StringUtils::formatNumber($offer->getCosts0()) . "\n" . ResourceNames::CRYSTAL . ": " . StringUtils::formatNumber($offer->getCosts1()) . "\n" . ResourceNames::PLASTIC . ": " . StringUtils::formatNumber($offer->getCosts2()) . "\n" . ResourceNames::FUEL . ": " . StringUtils::formatNumber($offer->getCosts3()) . "\n" . ResourceNames::FOOD . ": " . StringUtils::formatNumber($offer->getCosts4()));
                }

                $this->marketShipRepository->delete($offer);
            } else {
                // Zählt die gescheiterten Angebote
                $this->cntError++;
            }
        } else {
            // Zählt die gescheiterten Angebote
            $this->cntError++;
        }
    }

    private function processAuctionOffer(MarketAuction $offer):void
    {
        $offer = $this->marketAuctionRepository->getNonUserAuction($offer->getId(), $this->user);
        $this->errorMessage = '';

        // Prüft, ob Angebot noch vorhanden ist
        if ($offer !== null) {
            $ok = true;

            $buyRes = array();
            $newBuyResource = new BaseResources();

            $sell_price = 0;
            $current_price = 0;
            $new_price = 0;

            $sellResources = $offer->getSellResources();
            $currentBuyResources = $offer->getBuyResources();
            $ress = ['Metal','Crystal','Plastic','Fuel','Food'];

            foreach (ResourceNames::NAMES as $rk => $rn) {
                if ($this->form->get('buy'.$rk.'_offer')->getData() > $this->getCurrentPlanet()->{'getRes'.$ress[$rk]}()) {
                    $ok = false;
                    break;
                }

                if ($this->form->get('buy'.$rk.'_offer')?->getData()) {
                    $newBuyResource->set($rk, StringUtils::parseFormattedNumber($this->form->get('buy'.$rk.'_offer')->getData()));
                    $buyRes[$rk] = StringUtils::parseFormattedNumber($this->form->get('buy'.$rk.'_offer')->getData());
                } else
                    $buyRes[$rk] = 0;

                $rate = $this->runtimeDataStore->get('market_rate_' . $rk, 1);

                // Errechnet Rohstoffwert vom Angebot
                $sell_price += $sellResources->get($rk) * $rate;
                // Errechnet Rohstoffwert vom Höchstbietenden
                $current_price += $currentBuyResources->get($rk) * $rate;
                // Errechnet Rohstoffwert vom abgegebenen Gebot
                $new_price += $buyRes[$rk] * $rate;
            }

            // Prüft, ob genug Rohstoffe vorhanden sind
            if ($ok) {
                // Prüft, ob Gebot höher ist als das vom Höchstbietenden
                if ($current_price * (1 + $this->configurationService->getFloat('auction_overbid')) < $new_price) {
                    // wenn der bietende das höchst mögliche (oder mehr) bietet...
                    if ($this->configurationService->getFloat('auction_price_factor_max') <= (ceil($new_price) / floor($sell_price))) {
                        if ($offer->getCurrentBuyer()) {
                            // Rohstoffe dem überbotenen User wieder zurückgeben
                            $this->planetRepository->addResources($offer->getCurrentBuyerEntity(), $currentBuyResources->metal, $currentBuyResources->crystal, $currentBuyResources->plastic, $currentBuyResources->fuel, $currentBuyResources->food);

                            // Nachricht dem überbotenen User schicken
                            $this->marketReportRepository->addAuctionReport($offer->getId(), $offer->getCurrentBuyer(), $this->getCurrentPlanet()->getEntity(), $this->user, $sellResources, "auctionoverbid", $newBuyResource);
                        }

                        $bid = new BaseResources();
                        foreach (ResourceNames::NAMES as $rk => $rn) {
                            $bid->set($rk, $buyRes[$rk]);
                        }

                        // Rohstoffe dem Gewinner abziehen
                        $this->planetRepository->removeResources($this->getCurrentPlanet(), $bid);

                        // Nachricht an Verkäufer
                        $this->marketReportRepository->addAuctionReport($offer->getId(), $offer->getUser(), $this->getCurrentPlanet()->getEntity(), $this->user, $sellResources, "auctionfinished", $newBuyResource);
                        $this->marketReportRepository->addAuctionReport($offer->getId(), $offer->getUser(), $this->getCurrentPlanet()->getEntity(), $offer->getUser(), $sellResources, "auctionwon", $newBuyResource);

                        // Add market ratings
                        $this->userRatingService->addTradeRating(
                            $this->user,
                            TradePoints::POINTS_PER_TRADE,
                            false,
                            'Handel #' . $offer->getId() . ' mit ' . $offer->getUser()->getId()
                        );
                        if (strlen($offer->getText()) > TradePoints::POINTS_TRADE_TEXT_MIN_LENGTH) {
                            $this->userRatingService->addTradeRating(
                                $offer->getUser(),
                                TradePoints::POINTS_PER_TRADE + TradePoints::POINTS_PER_TRADE_TEXT,
                                true,
                                'Handel #' . $offer->getId() . ' mit ' . $this->user->getId()
                            );
                        } else {
                            $this->userRatingService->addTradeRating(
                                $offer->getUser(),
                                TradePoints::POINTS_PER_TRADE,
                                true,
                                'Handel #' . $offer->getId() . ' mit ' . $offer->getId()
                            );
                        }

                        // Auktion Speichern und "Stoppen" so dass nicht mehr geboten werden kann
                        $delete_date = time() + ($this->configurationService->getInt('market_auction_delay_time') * 3600);
                        $this->marketAuctionRepository->addBid($offer, $this->user, $this->getCurrentPlanet(), $bid, true, $delete_date);

                        //Log schreiben, falls dieser Handel regelwidrig ist
                        $isMultiWith = $this->userMultiRepository->existsEntryWith($this->user, $offer->getUser());
                        if ($isMultiWith) {
                            $seller = $offer->getUser();
                            $this->logRepository->add(LogFacility::MULTITRADE, LogSeverity::INFO, "[B]" . $this->user->getNick() . "[/B] hat an einer Auktion von [B]" . $seller->getNick() . "[/B] gewonnen:\n\nRohstoffe:\n" . ResourceNames::METAL . ": " . StringUtils::formatNumber($offer->getSell0()) . "\n" . ResourceNames::CRYSTAL . ": " . StringUtils::formatNumber($offer->getSell1()) . "\n" . ResourceNames::PLASTIC . ": " . StringUtils::formatNumber($offer->getSell2()) . "\n" . ResourceNames::FUEL . ": " . StringUtils::formatNumber($offer->getSell3()) . "\n" . ResourceNames::FOOD . ": " . StringUtils::formatNumber($offer->getSell4()) . "\n\nDies hat ihn folgende Rohstoffe gekostet:\n" . ResourceNames::METAL . ": " . $newBuyResource->metal . "\n" . ResourceNames::CRYSTAL . ": " . $newBuyResource->crystal . "\n" . ResourceNames::PLASTIC . ": " . $newBuyResource->plastic . "\n" . ResourceNames::FUEL . ": " . $newBuyResource->fuel . "\n" . ResourceNames::FOOD . ": " . $newBuyResource->food);
                        }

                        $this->successMessage = "Gratulation, du hast die Auktion gewonnen, da du den maximal Betrag geboten hast!";
                        $this->searchCat = 'auctions';
                    }
                    else {
                        if ($offer->getCurrentBuyer()) {
                            // Rohstoffe dem überbotenen User wieder zurückgeben
                            $this->planetRepository->addResources($offer->getCurrentBuyerEntity(), $currentBuyResources->metal, $currentBuyResources->crystal, $currentBuyResources->plastic, $currentBuyResources->fuel, $currentBuyResources->food);

                            // Nachricht dem überbotenen user schicken
                            $this->marketReportRepository->addAuctionReport($offer->getId(), $offer->getCurrentBuyer(), $this->getCurrentPlanet()->getEntity(), $this->user, $sellResources, "auctionoverbid", $newBuyResource, null, $offer->getDateEnd());
                        }

                        $bid = new BaseResources();
                        foreach (ResourceNames::NAMES as $rk => $rn) {
                            $bid->set($rk, $buyRes[$rk]);
                        }

                        // Rohstoffe vom neuen Bieter abziehen
                        $this->planetRepository->removeResources($this->getCurrentPlanet(), $bid);

                        //Das neue Angebot Speichern
                        $this->marketAuctionRepository->addBid($offer, $this->user, $this->getCurrentPlanet(), $bid);
                        $this->successMessage = "Gebot erfolgeich abgegeben!";
                        $this->searchCat = 'auctions';
                    }
                }
                else {
                    $this->errorMessage = "Das Gebot muss mindestens " . $this->configurationService->getFloat('auction_overbid') . "% höher sein als das Gebot des Höchstbietenden!";
                }
            }
            else {
                $this->errorMessage = "Die gebotenen Rohstoffe sind nicht mehr verfügbar!";
            }
        }
        else {
            $this->errorMessage = "Die Auktion ist nicht mehr vorhanden oder bereits abgelaufen!";
        }
    }

    #[LiveAction]
    public function showAuctionDetail(#[LiveArg] int $id): void
    {
        $this->marketAuction = $this->marketAuctionRepository->getNonUserAuction($id,$this->user);
    }

    #[LiveAction]
    public function back(): void
    {
        $this->marketAuction = null;
        $this->searchCat = 'auction';
    }

    public function getDistance(Entity $entity): float
    {
        return $this->entityService->distance($entity, $this->getCurrentPlanet()->getEntity());
    }

    public function getRuntimeValue(string $key, string $default = null): string
    {
        return (string)$this->runtimeDataStore->get($key, $default);
    }
}
