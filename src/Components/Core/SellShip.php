<?php

namespace EtoA\Components\Core;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\MarketShip;
use EtoA\Entity\Planet;
use EtoA\Form\Type\Core\SellShipType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Market\MarketShipRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(template: 'components/sell_ship.html.twig')]
class SellShip extends AbstractGameController
{
    public Planet $planet;
    public mixed $form = null;

    public function __construct(
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly RequestStack $requestStack,
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly ConfigurationService $configurationService,
        private readonly ShipListRepository $shipListRepository,
        private readonly MarketShipRepository $marketShipRepository,
        private readonly MarketReportRepository $marketReportRepository,
        private readonly LogRepository $logRepository
    )
    {}

    #[PreMount]
    public function preMount(array $data): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        $marketShip = new MarketShip();

        $this->form = $this->createForm(SellShipType::class, $marketShip, [
            'tradeable_ships' => $this->tradeableShips(),
            'market_user_reservation_active' => $this->configurationService->getBoolean('market_user_reservation_active'),
            'has_alliance' => $this->planet->getUser()->getAlliance() !== null,
            'alliance_market_level' => $this->allianceMarketLevel() ?? 0,
            'cd_enabled' => $this->cdEnabled(),
            'nick_length' => $this->configurationService->param2Int('nick_length'),
        ])->handleRequest($this->requestStack->getCurrentRequest());

        if($this->form->isSubmitted() && $this->form->isValid()) {
            $this->processOffer($marketShip);
        }

        $this->form = $this->form->createView();

        return $data;
    }

    public function tradeableShips(): array
    {
        return $this->shipListRepository->getTradeableShipsOnPlanet($this->planet);
    }

    public function availableShips(): array
    {
        return $this->shipListRepository->getEntityShipCounts($this->getUser()->getData(), $this->planet);
    }

    public function possible(): bool
    {
        return (count($this->availableShips()) > 0) && $this->configurationService->getInt('min_market_level_ship') <= $this->marketLevel();
    }

    public function marketLevel(): int
    {
        return $this->buildingListItemRepository->findOneBy(['entity'=>$this->planet,'building'=>BuildingId::MARKET])->getCurrentLevel();
    }

    public function allianceMarketLevel(): ?int
    {
        return $this->allianceBuildListRepository->findOneBy(['alliance'=>$this->planet->getUser()->getAlliance(),'allianceBuilding'=>AllianceBuildingId::MARKET->value])?->getLevel();
    }

    public function cdEnabled():bool
    {
        $alliance_market_level = $this->allianceMarketLevel();

        if ($alliance_market_level > 0) {
            $allianceMarketCooldown = $this->allianceBuildListRepository->findOneBy(['alliance'=>$this->planet->getUser()->getAlliance(),'allianceBuilding'=>AllianceBuildingId::MARKET->value])->getCooldown();
            if ($allianceMarketCooldown > time()) {
                $cd_enabled = true;
            } else {
                $cd_enabled = false;
            }
        } else {
            $cd_enabled = false;
        }

        return $cd_enabled;
    }

    private function processOffer(MarketShip $marketShip)
    {
        $ship_count =$marketShip->getCount();
        $marketShip->setShip($this->form->get('ship')->getData()->getShip());
        $marketShip->setUser($this->getUser()->getData());
        $marketShip->setEntity($this->planet);

        $costs = new BaseResources();

        foreach (ResourceNames::NAMES as $rk => $rn) {
            // Convert formatted number back to integer
            $costs->set($rk, max(0, StringUtils::parseFormattedNumber($marketShip->{'getCosts'.$rk}())));
        }
        // Überprüft, ob die angegebene Anzahl Schiffe noch vorhanden ist (eventuelle Zerstörung durch Kampf?)
        // Schiffe vom Planeten abziehen
        $removed_ships_count = $this->shipListRepository->removeShips($this->form->get('ship')->getData(),$ship_count);

        // Falls alle Schiffe abgezogen werden konnten
        if ($ship_count === $removed_ships_count) {
            $forAlliance = $this->form->get('ship_offer_reservation')->getData() === 2;
            if($forAlliance) {
                $marketShip->setForAlliance($this->getUser()->getData()->getAlliance());
                $allianceMarketLevel = $this->allianceMarketLevel();

                // Calculate cooldown
                if ($this->allianceMarketLevel() < 5) {
                    $factor = 0.2 * $allianceMarketLevel;
                } else {
                    $factor = $allianceMarketLevel - 4;
                }
                $cooldown = ($factor == 0) ? 0 : 3600 / $factor;

                // Set cooldown
                $cd = time() + $cooldown;
                $allianceMarket = $this->allianceBuildListRepository->findOneBy(['alliance'=>$this->planet->getUser()->getAlliance(),'allianceBuilding'=>AllianceBuildingId::MARKET->value]);
                $allianceMarket->setCooldown($cd);
            }

            $marketShip->setDate(time());

            // Angebot speicherns
            $this->marketShipRepository->persist($marketShip);
            $this->marketShipRepository->save();

            $this->marketReportRepository->addShipReport($marketShip->getId(), $this->getUser()->getData(), $marketShip->getEntity()->getEntity(), null, $marketShip->getShip(), $ship_count, "shipadd", $costs, 1.0, $marketShip->getText());

            $this->addFlash('success', 'Angebot erfolgreich abgesendet!');
        } else {
            // if only some ships have been removed, re-add the removed ships
            if ($removed_ships_count > 0) {
                $this->shipListRepository->addShip($marketShip->getShip(), $removed_ships_count, $this->getUser()->getData(), $marketShip->getEntity());
                // log action because this was a bug earlier
                $this->logRepository->add(
                    LogFacility::ILLEGALACTION,
                    LogSeverity::WARNING,
                    'User ' . $this->getUser()->getUserIdentifier() . ' hat versucht, auf dem Planeten' . $marketShip->getEntity()->getName()
                    . ' mehr Schiffe der ID ' . $marketShip->getShip()->getId() . ' zu verkaufen, als vorhanden sind.'
                    . ' Vorhanden: ' . $removed_ships_count . ', Versuchte Verkaufszahl: ' . $ship_count
                );
            }
            $this->addFlash('error', 'Die angegebenen Schiffe sind nicht mehr vorhanden!');
        }
    }
}