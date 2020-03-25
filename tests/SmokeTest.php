<?php declare(strict_types=1);

namespace EtoA;

use PHPUnit\Framework\TestCase;
use Silex\Application;

class SmokeTest extends TestCase
{
    public function testServiceDefinitions(): void
    {
        $ignoreList = ['twig.runtime.httpkernel'];
        /** @var Application $app */
        $app = require dirname(__DIR__).'/src/app.php';
        foreach ($app->keys() as $serviceId) {
            if (!in_array($serviceId, $ignoreList, true)) {
                $app->offsetGet($serviceId);
            }
        }
    }
}
