<?php

use staabm\PHPStanDba\QueryReflection\RuntimeConfiguration;
use staabm\PHPStanDba\QueryReflection\QueryReflection;
use staabm\PHPStanDba\QueryReflection\RecordingQueryReflector;
use staabm\PHPStanDba\QueryReflection\ReflectionCache;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$cacheFile = __DIR__.'/.phpstan-dba.cache';

(new Dotenv())->bootEnv(__DIR__ .'/.env');

$config = new RuntimeConfiguration();
$config->debugMode(true);

$databaseParts = parse_url($_SERVER['DATABASE_URL']);

QueryReflection::setupReflector(
    new RecordingQueryReflector(
        ReflectionCache::create(
            $cacheFile
        ),
        new \staabm\PHPStanDba\QueryReflection\PdoQueryReflector(new \PDO(sprintf('mysql:dbname=%s;host=%s', trim($databaseParts['path'], '/'), $databaseParts['host']), $databaseParts['user'], $databaseParts['pass']))
    ),
    $config
);
