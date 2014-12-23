<?php

$app->register(new \Silex\Provider\ServiceControllerServiceProvider());

$legacy = new \EtoA\Legacy\LegacyProvider();

$app->register($legacy);
$app->mount('/', $legacy);
