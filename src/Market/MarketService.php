<?php

namespace EtoA\Market;

use EtoA\Entity\Planet;

class MarketService
{
    public function __construct(
        private readonly MarketResourceRepository $marketResourceRepository,
        private readonly MarketShipRepository $marketShipRepository,
        private readonly MarketAuctionRepository $marketAuctionRepository
    )
    {}

    public function getOfferCountOnCurrentEntity(Planet $planet):int
    {
        $cntRes = $this->marketResourceRepository->count(['entity'=>$planet,'buyerEntity'=>null]);
        $cntShip = $this->marketShipRepository->count(['entity'=>$planet,'buyerEntity'=>null]);
        $cntAuction = $this->marketAuctionRepository->count(['entity'=>$planet]);

        return $cntRes+$cntAuction+$cntShip;
    }
}