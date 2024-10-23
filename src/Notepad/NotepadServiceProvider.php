<?php declare(strict_types=1);

namespace EtoA\Notepad;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class NotepadServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[NotepadDataRepository::class] = function (Container $pimple): NotepadDataRepository {
            return new NotepadDataRepository($pimple['db']);
        };
    }
}
