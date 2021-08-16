<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\AbstractRepository;
use EtoA\Fleet\FleetStatus;
use EtoA\Universe\Resources\BaseResources;

class FleetLogRepository extends AbstractRepository
{
    public function addLaunch(int $fleetId, int $userId, int $entityFromId, int $targetEntityId, int $launchTime, int $landTime, string $action, int $pilots, int $fuel, int $food, BaseResources $resource, BaseResources $fetch, string $fleetShipEnd, string $entityResStart, string $entityResEnd): void
    {
        $fleetResEnd = sprintf('%s:%s:%s:%s:%s:%s:0,f,', $resource->metal, $resource->crystal, $resource->plastic, $resource->fuel, $resource->food, $resource->people);
        $fleetResEnd .= sprintf('%s:%s:%s:%s:%s:%s:', $fetch->metal, $fetch->crystal, $fetch->plastic, $fetch->fuel, $fetch->food, $fetch->people);

        $this->getConnection()->executeQuery('INSERT DELAYED INTO logs_fleet (
                fleet_id,
                facility,
                timestamp,
                message,
                user_id,
                entity_user_id,
                entity_from,
                entity_to,
                launchtime,
                landtime,
                action,
                status,
                fleet_res_start,
                fleet_res_end,
                fleet_ships_start,
                fleet_ships_end,
                entity_res_start,
                entity_res_end,
                entity_ships_start,
                entity_ships_end
            ) VALUES (
                :fleetId,
                :facility,
                :timestamp,
                :text,
                :userId,
                :userId,
                :entityFromId,
                :targetEntityId,
                :launchTime,
                :landTime,
                :action,
                :status,
                :fleetResStart,
                :fleetResEnd,
                :fleetShipStart,
                :fleetShipEnd,
                :entityResStart,
                :entityResEnd,
                :entityShipsStart,
                :entityShipsEnd
            )', [
            'fleetId' => $fleetId,
            'facility' => FleetLogFacility::LAUNCH,
            'timestamp' => time(),
            'text' => sprintf('Treibstoff: %s Nahrung: %s Piloten: %s', $fuel, $food, $pilots),
            'userId' => $userId,
            'entityFromId' => $entityFromId,
            'targetEntityId' => $targetEntityId,
            'launchTime' => $launchTime,
            'landTime' => $landTime,
            'action' => $action,
            'status' => FleetStatus::DEPARTURE,
            'fleetResStart' => "0:0:0:0:0:0:0,f,0:0:0:0:0:0:0",
            'fleetResEnd' => $fleetResEnd,
            'fleetShipStart' => '0',
            'fleetShipEnd' => $fleetShipEnd,
            'entityResStart' => $entityResStart,
            'entityResEnd' => $entityResEnd,
            'entityShipsStart' => '',
            'entityShipsEnd' => '',
        ]);
    }

    public function addCancel(int $fleetId, int $userId, int $entityFromId, int $targetEntityId, int $launchTime, int $landTime, string $action, int $status, int $pilots, int $fuel, int $food, BaseResources $resourceStart, BaseResources $resourcesEnd): void
    {
        $this->getConnection()->executeQuery('INSERT DELAYED INTO logs_fleet (
                fleet_id,
                facility,
                timestamp,
                message,
                user_id,
                entity_user_id,
                entity_from,
                entity_to,
                launchtime,
                landtime,
                action,
                status,
                fleet_res_start,
                fleet_res_end,
                fleet_ships_start,
                fleet_ships_end,
                entity_res_start,
                entity_res_end,
                entity_ships_start,
                entity_ships_end
            ) VALUES (
                :fleetId,
                :facility,
                :timestamp,
                :text,
                :userId,
                :userId,
                :entityFromId,
                :targetEntityId,
                :launchTime,
                :landTime,
                :action,
                :status,
                :fleetResStart,
                :fleetResEnd,
                :fleetShipsStart,
                :fleetShipsEnd,
                :entityResStart,
                :entityResEnd,
                :entityShipsStart,
                :entityShipsEnd
            )', [
            'fleetId' => $fleetId,
            'facility' => FleetLogFacility::CANCEL,
            'timestamp' => time(),
            'text' => sprintf('Treibstoff: %s Nahrung: %s Piloten: %s', $fuel, $food, $pilots),
            'userId' => $userId,
            'entityFromId' => $entityFromId,
            'targetEntityId' => $targetEntityId,
            'launchTime' => $launchTime,
            'landTime' => $landTime,
            'action' => $action,
            'status' => $status,
            'fleetResStart' => sprintf('%s:%s:%s:%s:%s:%s:0,f,', $resourceStart->metal, $resourceStart->crystal, $resourceStart->plastic, $resourceStart->fuel, $resourceStart->food, $resourceStart->people),
            'fleetResEnd' => sprintf('%s:%s:%s:%s:%s:%s:0,f,', $resourcesEnd->metal, $resourcesEnd->crystal, $resourcesEnd->plastic, $resourcesEnd->fuel, $resourcesEnd->food, $resourcesEnd->people),
            'fleetShipsStart' => '',
            'fleetShipsEnd' => '',
            'entityResStart' => 'untouched',
            'entityResEnd' => 'untouched',
            'entityShipsStart' => '',
            'entityShipsEnd' => '',
        ]);
    }

    public function cleanup(int $threshold): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('logs_fleet')
            ->where('timestamp < :threshold')
            ->setParameter('threshold', $threshold)
            ->execute();
    }
}
