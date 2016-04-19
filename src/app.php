<?php

require dirname(__DIR__).'/vendor/autoload.php';

$app = new Pimple\Container([
    'app.root' => dirname(__DIR__),
    'app.config_dir' => sprintf('%s/htdocs/config/', dirname(__DIR__)),
    'db.options.file' => 'db.conf',
]);
(new \EtoA\Core\DoctrineServiceProvider())->register($app);

(new \EtoA\Building\BuildingServiceProvider())->register($app);
(new \EtoA\Defense\DefenseServiceProvider())->register($app);
(new \EtoA\Race\RaceServiceProvider())->register($app);
(new \EtoA\Ship\ShipServiceProvider())->register($app);
(new \EtoA\Technology\TechnologyServiceProvider())->register($app);

return $app;
