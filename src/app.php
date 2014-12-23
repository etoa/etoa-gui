<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$environment = 'development';

$app = new \Silex\Application(
    [
        'app.environment' => $environment,
        'app.root_dir'      => dirname(__DIR__),
    ]
);

require dirname(__DIR__) . '/config/config.php';

return $app;
