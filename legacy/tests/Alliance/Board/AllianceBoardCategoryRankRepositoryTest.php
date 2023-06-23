<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use EtoA\SymfonyWebTestCase;

class AllianceBoardCategoryRankRepositoryTest extends SymfonyWebTestCase
{
    private AllianceBoardCategoryRankRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(AllianceBoardCategoryRankRepository::class);
    }

    public function testGetRanksForCategories(): void
    {
        $this->repository->replaceRanks(1, 0, [1, 2, 3]);

        $this->assertSame([1, 2, 3], $this->repository->getRanksForCategories(1));
    }

    public function testGetRanksForBnd(): void
    {
        $this->repository->replaceRanks(0, 1, [1, 2, 3]);

        $this->assertSame([1, 2, 3], $this->repository->getRanksForBnd(1));
    }
}
