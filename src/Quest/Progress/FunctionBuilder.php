<?php declare(strict_types=1);

namespace EtoA\Quest\Progress;

use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;
use LittleCubicleGames\Quests\Progress\ProgressFunctionBuilderInterface;

class FunctionBuilder implements ProgressFunctionBuilderInterface
{
    public function build(string $taskName, array $attributes): ?HandlerFunctionInterface
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
            case Functions\SendMessage::NAME:
                return new Functions\SendMessage();
            case Functions\LaunchFleet::NAME:
                return new Functions\LaunchFleet();
            case Functions\UpgradeShip::NAME:
                return new Functions\UpgradeShip();
            case Functions\RenameStar::NAME:
                return new Functions\RenameStar();
            case Functions\CreateAlliance::NAME:
                return new Functions\CreateAlliance();
        }

        return null;
    }
}
