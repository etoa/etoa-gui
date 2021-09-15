<?php declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} else {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

require_once __DIR__ . '/../htdocs/inc/functions.inc.php';
