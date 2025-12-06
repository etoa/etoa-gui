<?php

namespace EtoA\Components\Core;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\MarketAuction;
use EtoA\Entity\Planet;
use EtoA\Form\Type\Core\AuctionType;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(template: 'components/auction.html.twig')]
class Auction extends AbstractGameController
{
    public Planet $planet;
    public mixed $form = null;

    public function __construct(
        private readonly PlanetRepository            $planetRepository,
        private readonly BuildingListItemRepository  $buildingListItemRepository,
        private readonly RequestStack                $requestStack,
        private readonly ConfigurationService        $configurationService,
        private readonly MarketAuctionRepository     $marketAuctionRepository,
        private readonly MarketReportRepository      $marketReportRepository
    )
    {
    }


    #[PreMount]
    public function preMount(array $data): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        $marketAuction = new MarketAuction();

        $this->form = $this->createForm(AuctionType::class, $marketAuction, [
            'planet' => $this->planet,
            'auction_min_duration' => $this->minDuration()
        ])->handleRequest($this->requestStack->getCurrentRequest());

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->processOffer($marketAuction);
        }

        $this->form = $this->form->createView();

        return $data;
    }

    public function marketLevel(): int
    {
        return $this->buildingListItemRepository->findOneBy(['entity' => $this->planet, 'building' => BuildingId::MARKET])->getCurrentLevel();
    }

    public function possible(): bool
    {
        return $this->configurationService->getInt('min_market_level_auction') <= $this->marketLevel();
    }

    public function minDuration():int
    {
        return $this->configurationService->getInt('auction_min_duration');
    }

    private function processOffer(MarketAuction $marketAuction): void
    {
        $ok = true;    // Checker for valid resources
        $offerCosts = new PreciseResources(); // Resource to be subtracted from planet

        $sellResources = new BaseResources();
        $costs = new BaseResources();

        $specialist = $this->getUser()->getData()->getSpecialist();
        $marketTax = max(1, $this->configurationService->getFloat('market_sell_tax') * ($specialist !== null ? $specialist->getTradeBonus() : 1));
        $ress = ['Metal', 'Crystal', 'Plastic', 'Fuel', 'Food'];

        foreach (ResourceNames::NAMES as $rk => $rn) {
            // Prüft, ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
            if ($marketAuction->{'getSell' . $rk}() * $marketTax > $this->planet->{'getRes' . $ress[$rk]}()) {
                $ok = false;
                break;
            }

            // Save resource to be subtracted from the planet
            $offerCosts->set($rk, $marketAuction->{'getSell' . $rk}() * $marketTax);

            // Build query
            $sellResources->set($rk, $marketAuction->{'getSell' . $rk}());
            $costs->set($rk, $marketAuction->{'getCurrency' . $rk}());
        }

        if ($ok) {
            $marketAuction->setUser($this->getUser()->getData());
            $marketAuction->setEntity($this->planet);

            // Rohstoffe vom Planet abziehen
            if ($this->planetRepository->removeResources($this->planet, $offerCosts)) {

                // Angebot speichern
                $this->marketAuctionRepository->persist($marketAuction);
                $this->marketAuctionRepository->save();

                $auctionMinTime = $this->minDuration() * 24 * 3600;
                $auctionEndTime = time() + $auctionMinTime + $this->form->get('auction_time_days')->getData() * 24 * 3600 + $this->form->get('auction_time_hours')->getData() * 3600;
                $this->marketReportRepository->addAuctionReport($marketAuction->getId(), $this->getUser()->getData(), $this->planet->getEntity(), null, $sellResources, "auction", $costs,$marketAuction->getText(), $marketTax, $auctionEndTime);

                $this->addFlash('success', "Angebot erfolgreich aufgegeben");
            } else {
                $this->addFlash('error', "Es gab ein Problem beim Reservieren der Rohstoffe!");
            }
        } else {
            $this->addFlash('error', "Es sind nicht mehr genügend Rohstoffe vorhanden!");
        }
    }
}