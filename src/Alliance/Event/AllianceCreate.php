<?php declare(strict_types=1);

namespace EtoA\Alliance\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AllianceCreate extends Event
{
    const CREATE_SUCCESS = 'alliance.create.success';
}
