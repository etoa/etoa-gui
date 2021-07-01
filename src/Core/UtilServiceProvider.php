<?php declare(strict_types=1);

namespace EtoA\Core;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\MarkdownConverterInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UtilServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[MarkdownConverterInterface::class] = function (): MarkdownConverterInterface {
            return new CommonMarkConverter();
        };
    }
}
