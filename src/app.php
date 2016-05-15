<?php

$app = new Pimple\Container([
    'debug' => false,
    'app.root' => dirname(__DIR__),
    'app.config_dir' => sprintf('%s/htdocs/config/', dirname(__DIR__)),
    'db.options.file' => 'db.conf',
]);
(new \EtoA\Core\MonologServiceProvider())->register($app);

// register error handler
\Monolog\ErrorHandler::register($app['logger']);

(new \EtoA\Core\DoctrineServiceProvider())->register($app);
(new \EtoA\Race\RaceServiceProvider())->register($app);

return $app;
