<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Universe\Resources\ResourceNames;

class FleetLog
{
    public int $id;
    public int $userId;
    public string $action;
    public int $entityFromId;
    public int $entityToId;
    public int $timestamp;
    public int $facility;
    public int $severity;
    public int $status;
    public int $launchTime;
    public int $landTime;
    /** @var array<int, int> */
    public array $fleetShipsStart;
    /** @var array<int, int> */
    public array $fleetShipsEnd;
    /** @var array<int, int> */
    public array $entityShipsStart;
    /** @var array<int, int> */
    public array $entityShipsEnd;
    public string $fleetResStart;
    public string $fleetResEnd;
    public string $entityResStart;
    public string $entityResEnd;
    public string $message;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->action = $data['action'];
        $this->entityFromId = (int) $data['entity_from'];
        $this->entityToId = (int) $data['entity_to'];
        $this->timestamp = (int) $data['timestamp'];
        $this->facility = (int) $data['facility'];
        $this->severity = (int) $data['severity'];
        $this->status = (int) $data['status'];
        $this->launchTime = (int) $data['launchtime'];
        $this->landTime = (int) $data['landtime'];
        $this->fleetShipsStart = $this->transformShips($data['fleet_ships_start']);
        $this->fleetShipsEnd = $this->transformShips($data['fleet_ships_end']);
        $this->entityShipsStart = $this->transformShips($data['entity_ships_start']);
        $this->entityShipsEnd = $this->transformShips($data['entity_ships_end']);
        $this->fleetResStart = $data['fleet_res_start'];
        $this->fleetResEnd = $data['fleet_res_end'];
        $this->entityResStart = $data['entity_res_start'];
        $this->entityResEnd = $data['entity_res_end'];
        $this->message = $data['message'];
    }

    /**
     * @return array<int, int>
     */
    private function transformShips(string $shipString): array
    {
        $ships = [];
        $shipEntries = array_filter(explode(',', $shipString));
        foreach ($shipEntries as $entry) {
            [$shipId, $count] = explode(":", $entry);
            if ($shipId > 0) {
                $ships[(int) $shipId] = (int) $count;
            }
        }

        return $ships;
    }

    /**
     * @return iterable<int, array{0: int, 1: int}>
     */
    public function iterateFleetShips(): iterable
    {
        $shipIds = array_unique(array_merge(array_keys($this->fleetShipsStart), array_keys($this->fleetShipsEnd)));
        foreach ($shipIds as $shipId) {
            yield $shipId => [$this->fleetShipsStart[$shipId] ?? 0, $this->fleetShipsEnd[$shipId] ?? 0];
        }
    }

    /**
     * @return iterable<int, array{0: int, 1: int}>
     */
    public function iterateEntityShips(): iterable
    {
        $shipIds = array_unique(array_merge(array_keys($this->entityShipsStart), array_keys($this->entityShipsEnd)));
        foreach ($shipIds as $shipId) {
            yield $shipId => [$this->entityShipsStart[$shipId] ?? 0, $this->entityShipsEnd[$shipId] ?? 0];
        }
    }

    /**
     * @return iterable<string, array{0: int, 1: int}>
     */
    public function iterateFleetResources(): iterable
    {
        $startResources = explode(":", $this->fleetResStart);
        $endResources = explode(":", $this->fleetResEnd);
        foreach (ResourceNames::NAMES as $k => $v) {
            yield $v => [(int) $startResources[$k], (int) $endResources[$k]];
        }
    }

    /**
     * @return iterable<string, array{0: int, 1: int}>
     */
    public function iterateEntityResources(): iterable
    {
        $startResources = explode(":", $this->entityResStart);
        $endResources = explode(":", $this->entityResEnd);
        foreach (ResourceNames::NAMES as $k => $v) {
            yield $v => [(int) $startResources[$k], (int) $endResources[$k]];
        }
    }
}
