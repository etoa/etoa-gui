<?php declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\SymfonyWebTestCase;
use EtoA\Universe\Entity\EntityService;

class EntityServiceTest extends SymfonyWebTestCase
{
    private EntityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = self::getContainer()->get(EntityService::class);
    }

    public function testDistanceNull(): void
    {
        $this->assertSame(0.0, $this->service->distance(null, null));
    }
}
