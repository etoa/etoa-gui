<?php

namespace EtoA;

use EtoA\Core\Database\DataTransformer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();
        date_default_timezone_set('Europe/Berlin');

        DataTransformer::setRegistry($this->getContainer()->get('doctrine'));
    }
}
