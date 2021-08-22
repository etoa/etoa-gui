<?php

declare(strict_types=1);

namespace EtoA\Specialist;

use EtoA\User\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SpecialistServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[SpecialistDataRepository::class] = function (Container $pimple): SpecialistDataRepository {
            return new SpecialistDataRepository($pimple['db']);
        };

        $pimple[SpecialistService::class] = function (Container $pimple): SpecialistService {
            return new SpecialistService(
                $pimple[UserRepository::class],
                $pimple[SpecialistDataRepository::class]
            );
        };
    }
}
