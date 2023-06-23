<?php declare(strict_types=1);

namespace EtoA\Planet\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PlanetRename extends Event
{
    const RENAME_SUCCESS = 'planet.rename.success';
}
