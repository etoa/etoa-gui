<?php

namespace EtoA\Components\Core;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\MarketResource;
use EtoA\Entity\Planet;
use EtoA\Form\Type\Core\SellRessType;
use EtoA\Market\MarketResourceRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(template: 'components/sell_ress.html.twig')]
class SellRess extends AbstractGameController
{
    public Planet $planet;
    public mixed $form = null;

    public function __construct(
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly RequestStack $requestStack,
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly ConfigurationService $configurationService,
        private readonly MarketResourceRepository $marketResourceRepository,
        private readonly MarketReportRepository $marketReportRepository
    )
    {}


    #[PreMount]
    public function preMount(array $data): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        $marketRess = new MarketResource();

        $this->form = $this->createForm(SellRessType::class, $marketRess, [
            'market_user_reservation_active' => $this->configurationService->getBoolean('market_user_reservation_active'),
            'has_alliance' => $this->planet->getUser()->getAlliance() !== null,
            'alliance_market_level' => $this->allianceMarketLevel() ?? 0,
            'cd_enabled' => $this->cdEnabled(),
            'nick_length' => $this->configurationService->param2Int('nick_length'),
            'planet' => $this->planet
        ])->handleRequest($this->requestStack->getCurrentRequest());

        if($this->form->isSubmitted() && $this->form->isValid()) {
            $this->processOffer($marketRess);
        }

        $this->form = $this->form->createView();

        return $data;
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

    private function processOffer(MarketResource $marketResource): void
    {
        $errMsg = '';

        if ($this->form->get('ressource_offer_reservation')->getData() === 1) {
            if (!$marketResource->getForUser()) {
                $this->addFlash('error', 'Reservation nicht möglich, Spieler nicht gefunden!');
            }
        }
        if ($this->form->get('ressource_offer_reservation')->getData() === 2) {
            if ($this->allianceMarketLevel() > 0 && !$this->cdEnabled()) {
                $marketResource->setForAlliance($this->getUser()->getData()->getAlliance());
            } else {
                $errMsg = 'Reservation nicht möglich, Allianzmarkt nicht vorhanden oder nicht bereit!';
            }
        }

        if (!$errMsg) {
            $ok = true;    // Checker for valid resources
            $offerCosts = new PreciseResources(); // Resource to be subtracted from planet

            $sellResources = new BaseResources();
            $costs = new BaseResources();

            $specialist = $this->getUser()->getData()->getSpecialist();
            $marketTax = max(1, $this->configurationService->getFloat('market_sell_tax') * ($specialist !== null ? $specialist->getTradeBonus() : 1));
            $ress = ['Metal','Crystal','Plastic','Fuel','Food'];

            foreach (ResourceNames::NAMES as $rk => $rn) {
                // Prüft, ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
                if ($marketResource->{'getSell'.$rk}() * $marketTax > $this->planet->{'getRes'.$ress[$rk]}()) {
                    $ok = false;
                    break;
                }

                // Save resource to be subtracted from the planet
                $offerCosts->set($rk, $marketResource->{'getSell'.$rk}() * $marketTax);

                // Build query
                $sellResources->set($rk, $marketResource->{'getSell'.$rk}());
                $costs->set($rk, $marketResource->{'getBuy'.$rk}());
            }

            if ($ok) {
                $marketResource->setUser($this->getUser()->getData());
                $marketResource->setEntity($this->planet);

                // Rohstoffe vom Planet abziehen
                if ($this->planetRepository->removeResources($this->planet, $offerCosts)) {
                    $forAlliance = $this->form->get('ressource_offer_reservation')->getData() === 2;
                    if($forAlliance) {
                        $marketResource->setForAlliance($this->getUser()->getData()->getAlliance());
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

                    // Angebot speichern
                    $this->marketResourceRepository->persist($marketResource);
                    $this->marketResourceRepository->save();
                    $this->marketReportRepository->addResourceReport($marketResource->getId(), $this->getUser()->getData(), $this->planet->getEntity(), null, $sellResources, "resadd", $costs, $marketTax, $marketResource->getText());

                    $this->addFlash('success', "Angebot erfolgreich aufgegeben");
                } else {
                    $this->addFlash('error', "Es gab ein Problem beim Reservieren der Rohstoffe!");
                }
            } else {
                $this->addFlash('error', "Es sind nicht mehr genügend Rohstoffe vorhanden!");
            }
        }
        else {
            $this->addFlash('error', $errMsg);
        }
    }
}