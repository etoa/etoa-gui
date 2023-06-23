<?php declare(strict_types=1);

namespace EtoA\Message\ReportData;

use EtoA\Core\Database\DataTransformer;
use EtoA\Universe\Resources\BaseResources;

class OtherReportData
{
    public int $id;
    public string $subtype;
    public int $fleetId;
    public BaseResources $resources;
    /** @var array<int, int> */
    public array $ships;
    public string $action;
    public int $status;

    public static function createFromArray(array $row): OtherReportData
    {
        $data = new OtherReportData();
        $data->id = (int) $row['id'];
        $data->subtype = $row['subtype'];
        $data->resources = new BaseResources();
        $data->resources->metal = (int) $row['res_0'];
        $data->resources->crystal = (int) $row['res_1'];
        $data->resources->plastic = (int) $row['res_2'];
        $data->resources->fuel = (int) $row['res_3'];
        $data->resources->food = (int) $row['res_4'];
        $data->resources->people = (int) $row['res_5'];
        $data->ships = DataTransformer::dataString($row['ships']);
        $data->action = $row['action'];
        $data->status = (int) $row['status'];
        $data->fleetId = (int) $row['fleet_id'];

        return $data;
    }
}
