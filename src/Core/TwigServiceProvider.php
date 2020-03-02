<?php declare(strict_types=1);

namespace EtoA\Core;

use Pimple\Container;
use Twig\Environment;

class TwigServiceProvider extends \Silex\Provider\TwigServiceProvider
{
    public function register(Container $app)
    {
        parent::register($app);

        $app->extend('twig', function(Environment $twig, $app): Environment {
            $twig->addGlobal('version', getAppVersion());
            $twig->addGlobal('serverTime', date('H:i:s'));
            $twig->addGlobal('serverTimeUnix', time());

            return $twig;
        });
    }

}
