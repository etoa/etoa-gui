<?php

$app = new Pimple\Container([
    'app.root' => dirname(__DIR__),
    'app.config_dir' => sprintf('%s/htdocs/config/', dirname(__DIR__)),
    'db.options.file' => 'db.conf',
]);
(new \EtoA\Core\DoctrineServiceProvider())->register($app);

return $app;
