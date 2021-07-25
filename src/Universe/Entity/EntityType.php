<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

class EntityType
{
    public const STAR = 's';
    public const PLANET = 'p';
    public const ASTEROID = 'a';
    public const NEBULA = 'n';
    public const WORMHOLE = 'w';
    public const EMPTY_SPACE = 'e';
    public const MARKET = 'm';
    public const ALLIANCE_MARKET = 'x';
    public const UNEXPLORED = 'u';
}
