<?php

$debug = true;
require_once __DIR__ . '/../vendor/autoload.php';
// Embed this for settings up old constants and classes
require_once __DIR__ . '/inc/bootstrap.inc.php';
$app = require __DIR__ . '/../src/app.php';

$app->run();
