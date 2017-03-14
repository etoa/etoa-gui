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
        }
    }
}
