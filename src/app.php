<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$environment = 'development';

$app = new \Silex\Application(
    array(
        'app.environment' => $environment,
        'app.root_dir'      => dirname(__DIR__),
    )
);

require dirname(__DIR__) . '/config/config.php';

return $app;
