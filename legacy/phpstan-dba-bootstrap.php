<?php

use staabm\PHPStanDba\QueryReflection\MysqliQueryReflector;
use staabm\PHPStanDba\QueryReflection\RuntimeConfiguration;
use staabm\PHPStanDba\QueryReflection\QueryReflection;
use staabm\PHPStanDba\QueryReflection\RecordingQueryReflector;
use staabm\PHPStanDba\QueryReflection\ReflectionCache;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$cacheFile = __DIR__.'/.phpstan-dba.cache';

(new Dotenv())->bootEnv(__DIR__ .'/.env', 'test');

$config = new RuntimeConfiguration();
$config->debugMode(true);
$config->stringifyTypes(true); // TODO remove when upgrading to PHP 8.1

$dsn = parse_url($_SERVER['DATABASE_URL']);

QueryReflection::setupReflector(
    new RecordingQueryReflector(
        ReflectionCache::create(
            $cacheFile
        ),
        new MysqliQueryReflector(new mysqli($dsn['host'], $dsn['user'], $dsn['pass'] ?? '', trim($dsn['path'], '/'), $dsn['port'] ?? 3306))
    ),
    $config
);
