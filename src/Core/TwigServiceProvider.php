<?php declare(strict_types=1);

namespace EtoA\Core;

use EtoA\Core\Twig\TwigExtension;
use Pimple\Container;
use Twig\Environment;

class TwigServiceProvider extends \Silex\Provider\TwigServiceProvider
{
    public function register(Container $app)
    {
        parent::register($app);

        $app->extend('twig', function(Environment $twig): Environment {
            $twig->addExtension(new TwigExtension());

            return $twig;
        });
    }

}
