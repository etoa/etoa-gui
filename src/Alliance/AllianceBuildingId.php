<?php declare(strict_types=1);

namespace EtoA\Alliance;

enum AllianceBuildingId: int
{
    case MAIN = 1;
    case MARKET = 2;
    case SHIPYARD = 3;
    case FLEET_CONTROL = 4;
    case RESEARCH = 5;
    case CRYPTO = 6;
}