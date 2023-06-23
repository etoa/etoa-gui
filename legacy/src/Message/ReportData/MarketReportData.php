<?php declare(strict_types=1);

namespace EtoA\Message\ReportData;

use EtoA\Universe\Resources\BaseResources;

class MarketReportData
{
    public int $id;
    public string $subtype;
    public int $recordId;
    public int $fleetId1;
    public int $fleetId2;
    public BaseResources $sell;
    public BaseResources $buy;
    public float $factor;
    public int $shipId;
    public int $shipCount;
    public int $timestamp2;

    public static function createFromArray(array $row): MarketReportData
    {
        $data = new MarketReportData();
        $data->id = (int) $row['id'];
        $data->recordId = (int) $row['record_id'];
        $data->subtype = $row['subtype'];
        $data->sell = new BaseResources();
        $data->sell->metal = (int) $row['sell_0'];
        $data->sell->crystal = (int) $row['sell_1'];
        $data->sell->plastic = (int) $row['sell_2'];
        $data->sell->fuel = (int) $row['sell_3'];
        $data->sell->food = (int) $row['sell_4'];
        $data->sell->people = (int) $row['sell_5'];
        $data->buy = new BaseResources();
        $data->buy->metal = (int) $row['buy_0'];
        $data->buy->crystal = (int) $row['buy_1'];
        $data->buy->plastic = (int) $row['buy_2'];
        $data->buy->fuel = (int) $row['buy_3'];
        $data->buy->food = (int) $row['buy_4'];
        $data->buy->people = (int) $row['buy_5'];
        $data->shipId = (int) $row['ship_id'];
        $data->shipCount = (int) $row['ship_count'];
        $data->timestamp2 = (int) $row['timestamp2'];
        $data->fleetId1 = (int) $row['fleet1_id'];
        $data->fleetId2 = (int) $row['fleet2_id'];
        $data->factor = (float) $row['factor'];

        return $data;
    }
}
