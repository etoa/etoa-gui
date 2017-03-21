<?php

$app = new Pimple\Container([
    'debug' => false,
    'app.environment' => isset($environment) ? $environment : 'production',
    'app.root' => dirname(__DIR__),
    'app.config_dir' => sprintf('%s/htdocs/config/', dirname(__DIR__)),
    'db.options.file' => 'db.conf',
]);
//$app->register(new \EtoA\Core\MonologServiceProvider());

// register error handler
//\Monolog\ErrorHandler::register($app['logger']);

$app->register(new \EtoA\Core\DoctrineServiceProvider());
$app->register(new \EtoA\Defense\DefenseServiceProvider());
$app->register(new \EtoA\Missile\MissileServiceProvider());
$app->register(new \EtoA\Race\RaceServiceProvider());
$app->register(new \EtoA\Planet\PlanetServiceProvider());
$app->register(new \EtoA\Quest\QuestServiceProvider());
$app->register(new \EtoA\Ship\ShipServiceProvider());

$app->register(new \LittleCubicleGames\Quests\ServiceProvider());

return $app;
