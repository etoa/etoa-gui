<?php declare(strict_types=1);

namespace EtoA\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use EtoA\Entity\Planet;
use EtoA\Universe\Planet\PlanetService;
use Symfony\Component\Serializer\Annotation\Ignore;


#[AsEntityListener(event: Events::postLoad, entity: Planet::class)]
class PlanetEntityListener
{
    public function __construct(
        private readonly PlanetService $planetService
    ) {
    }

    #[Ignore]
    public function postLoad(Planet $planet): void
    {
        $planet->setPlanetService($this->planetService);
    }
}
