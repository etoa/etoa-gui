<?php

namespace EtoA;

use PHPUnit\Framework\TestCase;
use Silex\Application;

class SmokeTest extends TestCase
{
    public function testServiceDefinitions()
    {
        /** @var Application $app */
        $app = require dirname(__DIR__).'/src/app.php';
        foreach ($app->keys() as $serviceId) {
            $app->offsetGet($serviceId);
        }

        $this->assertTrue(true);
    }
}
