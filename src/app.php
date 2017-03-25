<?php

$app = new \Silex\Application([
    'debug' => false,
    'app.environment' => isset($environment) ? $environment : 'production',
    'app.root' => dirname(__DIR__),
    'app.config_dir' => sprintf('%s/htdocs/config/', dirname(__DIR__)),
    'db.options.file' => 'db.conf',
]);
$app->register(new \EtoA\Core\MonologServiceProvider());

// register error handler
//\Monolog\ErrorHandler::register($app['logger']);

$app->register(new \LittleCubicleGames\Quests\ServiceProvider());

$app->register(new \EtoA\Core\DoctrineServiceProvider());
$app->register(new \EtoA\Defense\DefenseServiceProvider());
$app->register(new \EtoA\Missile\MissileServiceProvider());
$app->register(new \EtoA\Race\RaceServiceProvider());
$app->register(new \EtoA\Planet\PlanetServiceProvider());
$app->register(new \EtoA\Quest\QuestServiceProvider(), [
    'cubicle.quests.slots' => [
        [
            'id' => 'test',
            'registry' => 'test',
        ],
    ],
    'cubicle.quests.quests' => require __DIR__ . '/../data/quests.php',
]);
$app->register(new \EtoA\Ship\ShipServiceProvider());

return $app;
