<?php declare(strict_types=1);

namespace EtoA\Core;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Lock\BlockingStoreInterface;
use Symfony\Component\Lock\Exception\InvalidArgumentException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\SemaphoreStore;

class LockServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[BlockingStoreInterface::class] = function (): BlockingStoreInterface {
            try {
                return new SemaphoreStore();
            } catch (InvalidArgumentException $e) {
                return new FlockStore();
            }
        };

        $pimple[LockFactory::class] = function (Container $pimple): LockFactory {
            $factory = new LockFactory($pimple[BlockingStoreInterface::class]);
            $factory->setLogger($pimple['logger']);

            return $factory;
        };
    }
}
