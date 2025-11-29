<?php

namespace EtoA\Components\Core;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(template: 'components/market_info.html.twig')]
class MarketInfo extends AbstractGameController
{
    public int $anzahl = 0;
    public int $possible = 0;
    public int $returnFactor = 0;
    public string $tax = '';
    public string $statusText = '';
    public ?string $marketMetalFactor;
    public ?string $marketCrystalFactor;
    public ?string $marketPlasticFactor;
    public ?string $marketFuelFactor;
    public ?string $marketFoodFactor;
    public float $ressPriceFactorMax;
    public float $ressPriceFactorMin;
    public mixed $marketTax;
    public float $auctionPriceFactorMax;
    public float $auctionPriceFactorMin;
    public float $shipPriceFactorMax;
    public float $shipPriceFactorMin;

    public function __construct(
        private readonly MarketResourceRepository $marketResourceRepository,
        private readonly MarketShipRepository $marketShipRepository,
        private readonly MarketAuctionRepository $marketAuctionRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly RequestStack $requestStack,
        private readonly ConfigurationService $configurationService,
        private readonly RuntimeDataStore         $runtimeDataStore,
    )
    {}


    #[PreMount]
    public function preMount(array $data): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->getUser()->getData();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $specialist = $user->getSpecialist();

        $this->anzahl = $this->marketResourceRepository->count(['user'=>$user,'entity'=>$cp])+$this->marketAuctionRepository->count(['user'=>$user,'entity'=>$cp])+$this->marketShipRepository->count(['user'=>$user,'entity'=>$cp]);
        $market = $this->buildingListItemRepository->findOneBy(['user'=>$user,'entity'=>$cp,'building'=>BuildingId::MARKET]);
        $this->possible = $market->getCurrentLevel() - $this->anzahl;
        $this->returnFactor = round(1 - (1/($market->getCurrentLevel()+1)),2)*100;
        $tax = max(1, $this->configurationService->getFloat('market_sell_tax') * ($specialist ? $specialist->getTradeBonus() : 1));
        $this->tax = StringUtils::formatPercentString($tax, true, true);

        // Lädt Stufe des Allianzmarktplatzes
        if ($user->getAlliance())
            $alliance_market_level = $this->allianceBuildListRepository->findOneBy(['alliance'=>$user->getAlliance(),'allianceBuilding'=>AllianceBuildingId::MARKET->value])?->getLevel();
        else
            $alliance_market_level = 0;

        if ($alliance_market_level > 0) {
            $allianceMarketCooldown = $this->allianceBuildListRepository->findOneBy(['alliance'=>$user->getAlliance(),'building'=>AllianceBuildingId::MARKET->value])?->getCooldown();
            if ($allianceMarketCooldown > time()) {
                $this->statusText = "Bereit in <span id=\"cdcd\">" . StringUtils::formatTimespan($allianceMarketCooldown - time()) . "</span>";
            } else {
                $this->statusText = "Bereit";
            }
        } else {
            $this->statusText = "Es wurde noch kein Handelszentrum gebaut!";
        }

        $this->marketMetalFactor = $this->runtimeDataStore->get('market_rate_0', "1");
        $this->marketCrystalFactor = $this->runtimeDataStore->get('market_rate_1', "1");
        $this->marketPlasticFactor = $this->runtimeDataStore->get('market_rate_2', "1");
        $this->marketFuelFactor = $this->runtimeDataStore->get('market_rate_3', "1");
        $this->marketFoodFactor = $this->runtimeDataStore->get('market_rate_4', "1");
        $this->ressPriceFactorMax = $this->configurationService->getFloat('res_price_factor_max');
        $this->ressPriceFactorMin = $this->configurationService->getFloat('res_price_factor_min');
        $this->auctionPriceFactorMin = $this->configurationService->getFloat('auction_price_factor_max');
        $this->auctionPriceFactorMax = $this->configurationService->getFloat('auction_price_factor_min');
        $this->shipPriceFactorMax = $this->configurationService->getFloat('ship_price_factor_max');
        $this->shipPriceFactorMin = $this->configurationService->getFloat('ship_price_factor_min');
        $this->marketTax = max(1, $this->configurationService->getFloat('market_sell_tax') * ($specialist ? $specialist->getTradeBonus() : 1));

        return $data;
    }
}