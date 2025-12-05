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
    public function addAuctionReport(int $auctionId, int $userId, int $entityId, int $opponentId, BaseResources $sellResources, string $subType, BaseResources $buyResources, string $content = null, float $factor = 1.0, int $timestamp2 = 0): void
    {
        $reportId = $this->addReport(ReportTypes::TYPE_MARKET, $userId, 0, $content, $entityId, 0, $opponentId);

        $this->createQueryBuilder('q')
            ->insert('reports_market')
            ->values([
                'id' => ':id',
                'subtype' => ':subtype',
                'record_id' => ':recordId',
                'sell_0' => ':sell0',
                'sell_1' => ':sell1',
                'sell_2' => ':sell2',
                'sell_3' => ':sell3',
                'sell_4' => ':sell4',
                'sell_5' => ':sell5',
                'buy_0' => ':buy0',
                'buy_1' => ':buy1',
                'buy_2' => ':buy2',
                'buy_3' => ':buy3',
                'buy_4' => ':buy4',
                'buy_5' => ':buy5',
                'factor' => ':factor',
                'timestamp2' => ':timestamp2',
            ])
            ->setParameters([
                'id' => $reportId,
                'subtype' => $subType,
                'recordId' => $auctionId,
                'sell0' => $sellResources->get(0),
                'sell1' => $sellResources->get(1),
                'sell2' => $sellResources->get(2),
                'sell3' => $sellResources->get(3),
                'sell4' => $sellResources->get(4),
                'sell5' => 0,
                'buy0' => $buyResources->get(0),
                'buy1' => $buyResources->get(1),
                'buy2' => $buyResources->get(2),
                'buy3' => $buyResources->get(3),
                'buy4' => $buyResources->get(4),
                'buy5' => 0,
                'factor' => $factor,
                'timestamp2' => $timestamp2,
            ])
            ->executeQuery();
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

    public function addResourceReport(int $marketId, User $user, Entity $entity, ?User $opponent, BaseResources $sellResources, string $subType, BaseResources $costs, float $factor = 1.0, string $content = null, int $timestamp2 = 0, ?Entity $entity2 = null, ?Fleet $fleet1 = null, ?Fleet $fleet2 = null): void
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
