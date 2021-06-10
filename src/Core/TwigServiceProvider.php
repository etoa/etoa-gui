<?php declare(strict_types=1);

namespace EtoA\Core;

use EtoA\Core\Twig\TwigExtension;
use Pimple\Container;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class TwigServiceProvider extends \Silex\Provider\TwigServiceProvider
{
    public function register(Container $app)
    {
        parent::register($app);

        $app['twig.loader.filesystem'] = function ($app): FilesystemLoader {
            $loader = new FilesystemLoader();
            foreach (is_array($app['twig.path']) ? $app['twig.path'] : [$app['twig.path']] as $key => $val) {
                if (is_string($key)) {
                    $loader->addPath($key, $val);
                } else {
                    $loader->addPath($val);
                }
            }

            return $loader;
        };

        $app['twig.loader.array'] = function ($app): ArrayLoader {
            return new ArrayLoader($app['twig.templates']);
        };

        $app['twig.loader'] = function ($app): ChainLoader {
            return new ChainLoader([
                $app['twig.loader.array'],
                $app['twig.loader.filesystem'],
            ]);
        };

        $app['twig.environment_factory'] = $app->protect(function ($app): Environment {
            return new Environment($app['twig.loader'], array_replace([
                'charset' => $app['charset'],
                'debug' => $app['debug'],
                'strict_variables' => $app['debug'],
            ], $app['twig.options']));
        });

        $app->extend('twig', function (Environment $twig): Environment {
            $twig->addExtension(new TwigExtension());

            return $twig;
        });
    }
}
