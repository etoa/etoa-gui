<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Entity\Entity;
use EtoA\Entity\Fleet;
use EtoA\Entity\MarketReportData;
use EtoA\Entity\Ship;
use EtoA\Entity\User;
use EtoA\Universe\Resources\BaseResources;

class MarketReportRepository extends ReportRepository
{
    public function addAuctionReport(int $auctionId, User $user, Entity $entity, ?User $opponent, BaseResources $sellResources, string $subType, BaseResources $buyResources, string $content = null, float $factor = 1.0, int $timestamp2 = 0): void
    {
        $report = $this->addReport(ReportTypes::MARKET->value, $user, null, $content, $entity, null, $opponent);

        $marketReport = new MarketReportData();
        $marketReport->setRecordId($auctionId);
        $marketReport->setReport($report);
        $marketReport->setSubtype($subType);
        $marketReport->setSell($sellResources);
        $marketReport->setBuy($buyResources);
        $marketReport->setFactor($factor);
        $marketReport->setTimestamp2($timestamp2);

        $this->persist($marketReport);
        $this->save();
    }

    public function addShipReport(int $marketId, User $user, Entity $entity, ?User $opponent, Ship $ship, int $shipCount, string $subType, BaseResources $costs, float $factor = 1.0, string $content = null, int $timestamp2 = 0, Entity $entity2 = null, Fleet $fleet1 = null, Fleet $fleet2 = null): void
    {
        $report = $this->addReport(ReportTypes::MARKET->value, $user, null, $content, $entity, $entity2, $opponent);

        $marketReport = new MarketReportData();
        $marketReport->setReport($report);
        $marketReport->setSubtype($subType);
        $marketReport->setRecordId($marketId);
        $marketReport->setBuyMetal($costs->metal);
        $marketReport->setBuyCrystal($costs->crystal);
        $marketReport->setBuyPlastic($costs->plastic);
        $marketReport->setBuyFuel($costs->fuel);
        $marketReport->setBuyFood($costs->food);
        $marketReport->setBuyPeople(0);
        $marketReport->setShipCount($shipCount);
        $marketReport->setFactor($factor);
        $marketReport->setShip($ship);
        $marketReport->setTimestamp2($timestamp2);
        $marketReport->setFleet1($fleet1);
        $marketReport->setFleet2($fleet2);

        $this->persist($marketReport);
        $this->save();
    }

    public function addResourceReport(int $marketId, User $user, Entity $entity, ?User $opponent, BaseResources $sellResources, string $subType, BaseResources $costs, float $factor = 1.0, ?string $content = null, int $timestamp2 = 0, ?Entity $entity2 = null, ?Fleet $fleet1 = null, ?Fleet $fleet2 = null): void
    {
        $report = $this->addReport(ReportTypes::MARKET->value, $user, null, $content, $entity, $entity2, $opponent);

        $marketReport = new MarketReportData();
        $marketReport->setRecordId($marketId);
        $marketReport->setReport($report);
        $marketReport->setSubtype($subType);
        $marketReport->setSell($sellResources);
        $marketReport->setBuy($costs);
        $marketReport->setFactor($factor);
        $marketReport->setTimestamp2($timestamp2);
        $marketReport->setFleet1($fleet1);
        $marketReport->setFleet2($fleet2);

        $this->persist($marketReport);
        $this->save();
    }
}
