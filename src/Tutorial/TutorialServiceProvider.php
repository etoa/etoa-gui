<?php declare(strict_types=1);

namespace EtoA\Tutorial;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TutorialServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[TutorialUserProgressRepository::class] = function (Container $pimple): TutorialUserProgressRepository {
            return new TutorialUserProgressRepository($pimple['db']);
        };

        $pimple[TutorialManager::class] = function (Container $pimple): TutorialManager {
            return new TutorialManager($pimple['db']);
        };
    }
}
