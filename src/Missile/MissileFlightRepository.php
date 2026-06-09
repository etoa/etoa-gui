<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\MissileFlight;
use EtoA\Entity\MissileFlightObject;
use EtoA\Entity\Planet;

class MissileFlightRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissileFlight::class);
    }

    /**
     * @return MissileFlight[]
     */
    public function getFlights(MissileFlightSearch $search): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->orderBy('q.landTime', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param array<int, int> $missiles
     */
    public function startFlight(Planet $fromEntity, Planet $toEntity, int $duration, array $missiles): void
    {
        $item = new MissileFlight();
        $item->setEntityFrom($fromEntity);
        $item->setTarget($toEntity);
        $item->setStartTime(time());
        $item->setLandTime(time()+$duration);

        $this->persist($item);

        foreach ($missiles as $missile) {
            $flightObj = new MissileFlightObject();
            $flightObj->setFlight($item);
            $flightObj->setMissile($missile->getMissile());
            $flightObj->setCount($missile->getCount());

            $this->persist($flightObj);
            $item->addFlightObject($flightObj);
        }

        $this->save();
    }

    public function deleteFlight(MissileFlight $missileFlight): void
    {
        $this->remove($missileFlight);
        $this->save();
    }
}
