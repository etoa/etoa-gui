<?php declare(strict_types=1);

namespace EtoA\Requirement;

use EtoA\Building\BuildingRequirementRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Technology\TechnologyRequirementRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RequirementServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[RequirementRepositoryProvider::class] = function (Container $pimple): RequirementRepositoryProvider {
            return new RequirementRepositoryProvider(
                $pimple[ShipRequirementRepository::class],
                $pimple[DefenseRequirementRepository::class],
                $pimple[BuildingRequirementRepository::class],
                $pimple[TechnologyRequirementRepository::class],
                $pimple[MissileRequirementRepository::class],
            );
        };
    }
}
