<?php

namespace EtoA\Components\Core;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\MarketAuction;
use EtoA\Entity\MarketResource;
use EtoA\Entity\MarketShip;
use EtoA\Entity\User;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipId;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/market_search.html.twig')]
class MarketSearch extends AbstractGameController
{
    use DefaultActionTrait;

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

    public function __construct(
        private readonly MarketResourceRepository $marketResourceRepository,
        private readonly MarketShipRepository $marketShipRepository,
        private readonly MarketAuctionRepository $marketAuctionRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly ConfigurationService $configurationService,
        private readonly RequestStack $requestStack,
        private readonly EntityService $entityService,
        private readonly ShipDataRepository $shipDataRepository
    ) {
    }

    public function getFilteredOffers(): array
    {
        return match ($this->searchCat) {
            'resources' => $this->getFilteredResourceOffers(),
            'ships' => $this->getFilteredShipOffers(),
            'auctions' => $this->getFilteredAuctionOffers(),
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

    private function getCurrentPlanet()
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
}
