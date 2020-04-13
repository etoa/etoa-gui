<?php declare(strict_types=1);

namespace EtoA\Core;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UtilServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['etoa.util.markdown'] = function (): ConverterInterface {
            return new CommonMarkConverter();
        };
    }
}
