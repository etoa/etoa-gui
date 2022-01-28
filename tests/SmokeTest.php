<?php declare(strict_types=1);

namespace EtoA;

use PHPUnit\Framework\TestCase;

class SmokeTest extends TestCase
{
    use DbTestTrait;

    public function testServiceDefinitions(): void
    {
        $ignoreList = ['twig.runtime.httpkernel'];
        $app = $this->setupApplication();
        foreach ($app->keys() as $serviceId) {
            if (!in_array($serviceId, $ignoreList, true)) {
                $app->offsetGet($serviceId);
            }
        }

        // Drop monolog and symfony error handlers
        restore_error_handler();
        restore_error_handler();
    }

    public function tearDown(): void
    {
    }
}
