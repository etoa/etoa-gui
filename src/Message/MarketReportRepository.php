<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Universe\Resources\BaseResources;

class MarketReportRepository extends ReportRepository
{
    public function addAuctionReport(int $auctionId, int $userId, int $entityId, int $opponentId, BaseResources $sellResources, string $subType, BaseResources $buyResources, string $content = null, float $factor = 1.0, int $timestamp2 = 0): void
    {
        $reportId = $this->addReport('market', $userId, 0, $content, $entityId, 0, $opponentId);

        $this->createQueryBuilder()
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
            ->execute();
    }

    public function addShipReport(int $marketId, int $userId, int $entityId, int $opponentId, int $shipId, int $shipCount, string $subType, BaseResources $costs, float $factor = 1.0, string $content = null, int $timestamp2 = 0, int $entity2Id = 0, int $fleet1Id = 0, int $fleet2Id = 0): void
    {
        $reportId = $this->addReport('market', $userId, 0, $content, $entityId, $entity2Id, $opponentId);

        $this->createQueryBuilder()
            ->insert('reports_market')
            ->values([
                'id' => ':id',
                'subtype' => ':subtype',
                'record_id' => ':recordId',
                'buy_0' => ':buy0',
                'buy_1' => ':buy1',
                'buy_2' => ':buy2',
                'buy_3' => ':buy3',
                'buy_4' => ':buy4',
                'buy_5' => ':buy5',
                'ship_id' => ':shipId',
                'ship_count' => ':shipCount',
                'factor' => ':factor',
                'timestamp2' => ':timestamp2',
                'fleet1_id' => ':fleet1Id',
                'fleet2_id' => ':fleet2Id',
            ])
            ->setParameters([
                'id' => $reportId,
                'subtype' => $subType,
                'recordId' => $marketId,
                'buy0' => $costs->get(0),
                'buy1' => $costs->get(1),
                'buy2' => $costs->get(2),
                'buy3' => $costs->get(3),
                'buy4' => $costs->get(4),
                'buy5' => 0,
                'factor' => $factor,
                'shipId' => $shipId,
                'shipCount' => $shipCount,
                'timestamp2' => $timestamp2,
                'fleet1Id' => $fleet1Id,
                'fleet2Id' => $fleet2Id,
            ])
            ->execute();
    }

    public function addResourceReport(int $marketId, int $userId, int $entityId, int $opponentId, BaseResources $sellResources, string $subType, BaseResources $costs, float $factor = 1.0, string $content = null, int $timestamp2 = 0, int $entity2Id = 0, int $fleet1Id = 0, int $fleet2Id = 0): void
    {
        $reportId = $this->addReport('market', $userId, 0, $content, $entityId, $entity2Id, $opponentId);

        $this->createQueryBuilder()
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
                'fleet1_id' => ':fleet1Id',
                'fleet2_id' => ':fleet2Id',
            ])
            ->setParameters([
                'id' => $reportId,
                'subtype' => $subType,
                'recordId' => $marketId,
                'sell0' => $sellResources->get(0),
                'sell1' => $sellResources->get(1),
                'sell2' => $sellResources->get(2),
                'sell3' => $sellResources->get(3),
                'sell4' => $sellResources->get(4),
                'sell5' => 0,
                'buy0' => $costs->get(0),
                'buy1' => $costs->get(1),
                'buy2' => $costs->get(2),
                'buy3' => $costs->get(3),
                'buy4' => $costs->get(4),
                'buy5' => 0,
                'factor' => $factor,
                'timestamp2' => $timestamp2,
                'fleet1Id' => $fleet1Id,
                'fleet2Id' => $fleet2Id,
            ])
            ->execute();
    }
}
