<?php declare(strict_types=1);

$app = new \Silex\Application([
    'debug' => $debug ?? false,
    'app.environment' => $environment ?? 'production',
    'app.root' => dirname(__DIR__),
    'app.config_dir' => sprintf('%s/htdocs/config/', dirname(__DIR__)),
    'db.options.file' => 'db.conf',
    'etoa.quests.enabled' => $questSystemEnabled ?? true,
]);
if ((bool) $app['debug']) {
    \Symfony\Component\Debug\Debug::enable();
}
$app->register(new \EtoA\Core\MonologServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \EtoA\Core\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../templates',
    'twig.options' => [
        'cache' => __DIR__ . '/../var/cache/' . $app['app.environment'] . '/twig/',
        'debug' => $app['debug'],
        'auto_reload' => true, // Remove this once we delete the cache on composer install
    ],
]);

if ((bool) $app['etoa.quests.enabled']) {
    $app->register(new \LittleCubicleGames\Quests\ServiceProvider());
}

// register error handler
//\Monolog\ErrorHandler::register($app['logger']);

$app->register(new \EtoA\Building\BuidingServiceProvider());
$app->register(new \EtoA\Core\DoctrineServiceProvider());
$app->register(new \EtoA\Core\ParamConverterServiceProvider());
$app->register(new \EtoA\Core\SessionServiceProvider());
$app->register(new \EtoA\Defense\DefenseServiceProvider());
$app->register(new \EtoA\Missile\MissileServiceProvider());
$app->register(new \EtoA\Race\RaceServiceProvider());
$app->register(new \EtoA\Planet\PlanetServiceProvider());
$app->register($questProvider = new \EtoA\Quest\QuestServiceProvider(), [
    'cubicle.quests.autostart' => true,
    'cubicle.quests.slots' => [
        [
            'id' => 'test',
            'registry' => 'test',
        ],
    ],
    'cubicle.quests.quests' => require __DIR__ . '/../data/quests.php',
]);
$app->register(new \EtoA\Ship\ShipServiceProvider());
$app->register(new \EtoA\Technology\TechnologyServiceProvider());
$app->register($tutorialProvider = new \EtoA\Tutorial\TutorialServiceProvider());
$app->register(new \EtoA\User\UserServiceProvider());

$app->mount('/', $questProvider);
$app->mount('/', $tutorialProvider);

return $app;
