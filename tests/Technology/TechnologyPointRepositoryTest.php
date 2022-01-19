<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\SymfonyWebTestCase;

class TechnologyPointRepositoryTest extends SymfonyWebTestCase
{
    private TechnologyPointRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(TechnologyPointRepository::class);
    }

    public function testGetAllMap(): void
    {
        $this->repository->add(1, [2 => 3.0]);

        $this->assertSame([1 => [2 => 3.0]], $this->repository->getAllMap());
    }

    public function testAreCalculated(): void
    {
        $this->assertFalse($this->repository->areCalculated());

        $this->repository->add(1, [1 => 1]);

        $this->assertTrue($this->repository->areCalculated());

        $this->repository->deleteAll();

        $this->assertFalse($this->repository->areCalculated());
    }
}
