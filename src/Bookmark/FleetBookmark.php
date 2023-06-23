<?php declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Universe\Resources\BaseResources;

class FleetBookmark
{
    public int $id;
    public int $userId;
    public string $name;
    public int $targetId;
    /** @var array<int, int> */
    public array $ships;
    public BaseResources $freight;
    public BaseResources $fetch;
    public string $action;
    public int $speed;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->name = $data['name'];
        $this->targetId = (int) $data['target_id'];
        $this->action = $data['action'];
        $this->speed = (int) $data['speed'];

        $this->freight = $this->getBaseResources($data['res']);
        $this->fetch = $this->getBaseResources($data['resfetch']);

        $this->ships = [];
        $ship = array_filter(explode(",", $data['ships']));
        foreach ($ship as $shipdata) {
            $entry = explode(":", $shipdata);
            $this->ships[(int) $entry[0]] = (int) $entry[1];
        }
    }

    private function getBaseResources(string $resourcesString): BaseResources
    {
        $resourceData = explode(',', $resourcesString);
        $resources = new BaseResources();
        $resources->metal = (int) $resourceData[0];
        $resources->crystal = (int) $resourceData[1];
        $resources->plastic = (int) $resourceData[2];
        $resources->fuel = (int) $resourceData[3];
        $resources->food = (int) $resourceData[4];
        $resources->people = (int) $resourceData[5];

        return $resources;
    }
}
