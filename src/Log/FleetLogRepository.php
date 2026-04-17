<?php declare(strict_types=1);

namespace EtoA\Log;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\Fleet;
use EtoA\Entity\FleetLog;
use EtoA\Entity\User;
use EtoA\Fleet\FleetStatus;
use EtoA\Universe\Resources\BaseResources;

class FleetLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FleetLog::class);
    }

    /**
     * @return FleetLog[]
     */
    public function searchLogs(FleetLogSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.timestamp', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function addLaunch(Fleet $fleet, User $user, Entity $entityFrom, Entity $targetEntity, int $launchTime, int $landTime, string $action, int $pilots, int $fuel, int $food, BaseResources $resource, BaseResources $fetch, string $fleetShipEnd, string $entityResStart, string $entityResEnd): void
    {
        $fleetResEnd = sprintf('%s:%s:%s:%s:%s:%s:0,f,', $resource->metal, $resource->crystal, $resource->plastic, $resource->fuel, $resource->food, $resource->people);
        $fleetResEnd .= sprintf('%s:%s:%s:%s:%s:%s:', $fetch->metal, $fetch->crystal, $fetch->plastic, $fetch->fuel, $fetch->food, $fetch->people);
        $fleetLog = new FleetLog();
        $fleetLog->setFleet($fleet);
        $fleetLog->setFacility(FleetLogFacility::LAUNCH);
        $fleetLog->setTimestamp(time());
        $fleetLog->setMessage(sprintf('Treibstoff: %s Nahrung: %s Piloten: %s', $fuel, $food, $pilots));
        $fleetLog->setUser($user);
        $fleetLog->setEntityUser($user);
        $fleetLog->setEntityFrom($entityFrom);
        $fleetLog->setEntityTo($entityFrom);
        $fleetLog->setLaunchTime($launchTime);
        $fleetLog->setLandTime($landTime);
        $fleetLog->setAction($action);
        $fleetLog->setStatus(FleetStatus::DEPARTURE->value);
        $fleetLog->setFleetResStart("0:0:0:0:0:0:0,f,0:0:0:0:0:0:0");
        $fleetLog->setFleetResEnd($fleetResEnd);
        $fleetLog->setFleetShipsStart('0');
        $fleetLog->setFleetShipsEnd($fleetShipEnd);
        $fleetLog->setEntityResStart($entityResStart);
        $fleetLog->setEntityResEnd($entityResEnd);
        $fleetLog->setEntityShipsStart('');
        $fleetLog->setEntityShipsEnd('');

        $this->persist($fleetLog);
        $this->save();
    }

    public function addCancel(int $fleetId, int $userId, int $entityFromId, int $targetEntityId, int $launchTime, int $landTime, string $action, int $status, int $pilots, int $fuel, int $food, BaseResources $resourceStart, BaseResources $resourcesEnd): void
    {
        $log = new FleetLog();
        $log->setFleetId($fleetId);
        $log->setFacility(FleetLogFacility::CANCEL);
        $log->setTimestamp(time());
        $log->setMessage(sprintf('Treibstoff: %s Nahrung: %s Piloten: %s', $fuel, $food, $pilots));
        $log->setUserId($userId);
        $log->setEntityUserId($userId);
        $log->setEntityFrom($entityFromId);
        $log->setEntityTo($targetEntityId);
        $log->setLaunchTime($launchTime);
        $log->setLandTime($landTime);
        $log->setAction($action);
        $log->setStatus($status);
        $log->setFleetResStart(sprintf('%s:%s:%s:%s:%s:%s:0,f,', $resourceStart->metal, $resourceStart->crystal, $resourceStart->plastic, $resourceStart->fuel, $resourceStart->food, $resourceStart->people));
        $log->setFleetResEnd(sprintf('%s:%s:%s:%s:%s:%s:0,f,', $resourcesEnd->metal, $resourcesEnd->crystal, $resourcesEnd->plastic, $resourcesEnd->fuel, $resourcesEnd->food, $resourcesEnd->people));
        $log->setFleetShipsStart('');
        $log->setFleetShipsEnd('');
        $log->setEntityResStart('untouched');
        $log->setEntityResEnd('untouched');
        $log->setEntityShipsStart('');
        $log->setEntityShipsEnd('');

        $this->persist($log);
        $this->save();
    }

    public function cleanup(int $threshold): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.timestamp < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();
    }

    public function countBySearch(FleetLogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
