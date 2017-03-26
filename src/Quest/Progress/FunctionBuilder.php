<?php

namespace EtoA\Quest\Progress;

use LittleCubicleGames\Quests\Progress\ProgressFunctionBuilderInterface;

class FunctionBuilder implements ProgressFunctionBuilderInterface
{
    public function build($taskName)
    {
        switch ($taskName) {
            case Functions\LaunchMissile::NAME:
                return new Functions\LaunchMissile();
            case Functions\BuyMissile::NAME:
                return new Functions\BuyMissile();
            case Functions\HireSpecialist::NAME:
                return new Functions\HireSpecialist();
            case Functions\DischargeSpecialist::NAME:
                return new Functions\DischargeSpecialist();
            case Functions\RecycleShip::NAME:
                return new Functions\RecycleShip();
            case Functions\RecycleDefense::NAME:
                return new Functions\RecycleDefense();
            case Functions\RenamePlanet::NAME:
                return new Functions\RenamePlanet();
        }
    }
}
