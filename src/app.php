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
\Monolog\ErrorHandler::register($app['logger']);

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
    'cubicle.quests.quests' => [
        0 => [
            'id' => 0,
            'title' => 'Angriff ist die beste Verteidigung',
            'description' => 'Lorem ipsum dolor sit amet, ius tantas causae intellegebat at. Sit eu mucius eleifend pertinacia, ei appetere postulant consetetur usu. An his velit melius placerat. No autem simul conceptam duo, utamur facilisis ut quo. In mandamus omittantur neglegentur quo, error elaboraret deterruisset eos ut, eripuit legendos reformidans ius an. Ad pro tantas indoctum, ut ullum graeci delenit eam. Summo iuvaret has cu. Dicit petentium ex vis, in putent mollis dissentias sed, ad vis sint nihil. Id elit vidit suavitate vix, nec utroque erroribus philosophia ex. Adhuc minimum abhorreant vis ne, sea eu sumo neglegentur conclusionemque.',
            'task' => [
                'id' => 0,
                'type' => 'buy-missile',
                'operator' => 'equal-to',
                'description' => 'Kaufe 10 PHOBOS Raketen',
                'value' => 10,
            ],
            'rewards' => [
                [
                    'type' => 'missile',
                    'value' => 1,
                    'missile_id' => 4,
                ],
            ],
        ],
    ],
]);
$app->register(new \EtoA\Ship\ShipServiceProvider());

return $app;
