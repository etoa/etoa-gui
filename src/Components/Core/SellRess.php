<?php

namespace EtoA\Components\Core;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Entity\Planet;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(template: 'components/sell_ress.html.twig')]
class SellRess extends AbstractGameController
{
    public Planet $planet;

    public function __construct(
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly RequestStack $requestStack,
        private readonly AllianceBuildListRepository $allianceBuildListRepository
    )
    {}


    #[PreMount]
    public function preMount(array $data): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->planet = $this->planetRepository->find($request->getSession()->get('cpid'));

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
}