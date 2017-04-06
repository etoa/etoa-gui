<?php

namespace EtoA\Galaxy\Event;

use Symfony\Component\EventDispatcher\Event;

class StarRename extends Event
{
    const RENAME_SUCCESS = 'star.rename.success';
}
