<?php

$app = new \Silex\Application([
    'debug' => $debug ?? true,
    'app.environment' => $environment ?? 'production',
    'app.root' => dirname(__DIR__),
    'app.config_dir' => sprintf('%s/htdocs/config/', dirname(__DIR__)),
    'db.options.file' => 'db.conf',
]);
$app->register(new \EtoA\Core\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../templates',
    'twig.options' => [
        'cache' => __DIR__ . '/../var/cache/' . $app['app.environment'] . '/twig/',
        'debug' => $app['debug'],
        'auto_reload' => true, // Remove this once we delete the cache on composer install
    ],
]);
$app->boot();
