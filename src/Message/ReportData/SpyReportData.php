<?php declare(strict_types=1);

namespace EtoA\Message\ReportData;

use EtoA\Core\Database\DataTransformer;
use EtoA\Universe\Resources\BaseResources;

class SpyReportData
{
    public int $id;
    public string $subtype;
    public int $fleetId;
    public BaseResources $resources;
    /** @var array<int, int> */
    public array $ships = [];
    /** @var array<int, int> */
    public array $defense = [];
    /** @var array<int, int> */
    public array $buildings = [];
    /** @var array<int, int> */
    public array $technologies = [];
    public int $spyDefense;
    public int $coverage;
    public bool $showShips;
    public bool $showDefense;
    public bool $showBuildings;
    public bool $showTechnologies;

    public static function createFromArray(array $row): SpyReportData
    {
        $data = new SpyReportData();
        $data->id = (int) $row['id'];
        $data->subtype = $row['subtype'];
        $data->resources = new BaseResources();
        $data->resources->metal = (int) $row['res_0'];
        $data->resources->crystal = (int) $row['res_1'];
        $data->resources->plastic = (int) $row['res_2'];
        $data->resources->fuel = (int) $row['res_3'];
        $data->resources->food = (int) $row['res_4'];
        $data->resources->people = (int) $row['res_5'];
        $data->showShips = $row['ships'] !== '';
        $data->showDefense = $row['defense'] !== '';
        $data->showBuildings = $row['buildings'] !== '';
        $data->showTechnologies = $row['technologies'] !== '';
        if ($row['ships'] !== '0') {
            $data->ships = DataTransformer::dataString($row['ships']);
        }
        if ($row['defense'] !== '0') {
            $data->defense = DataTransformer::dataString($row['defense']);
        }
        if ($row['buildings'] !== '0') {
            $data->buildings = DataTransformer::dataString($row['buildings']);
        }
        if ($row['technologies'] !== '0') {
            $data->technologies = DataTransformer::dataString($row['technologies']);
        }
        $data->spyDefense = (int) $row['spydefense'];
        $data->coverage = (int) $row['coverage'];
        $data->fleetId = (int) $row['fleet_id'];

        return $data;
    }
}
