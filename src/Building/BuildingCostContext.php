<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Entity\PlanetType;
use EtoA\Entity\Race;
use EtoA\Entity\Specialist;
use EtoA\Universe\Star\SolarType;

class BuildingCostContext
{
    public ?PlanetType $planetType = null;
    public ?Race $race = null;
    public ?Specialist $specialist = null;
    public ?SolarType $solarType = null;

    public static function admin(): BuildingCostContext
    {
        return new BuildingCostContext();
    }
}
