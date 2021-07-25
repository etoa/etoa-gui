<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AllianceRankRepositoryTest extends AbstractDbTestCase
{
    private AllianceRankRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AllianceRankRepository::class];
    }

    public function testGetRanks(): void
    {
        $this->repository->add(1);

        $this->assertNotEmpty($this->repository->getRanks(1));
    }

    public function testGetRank(): void
    {
        $rankId = $this->repository->add(1);

        $this->assertNotNull($this->repository->getRank($rankId, 1));

        $this->repository->updateRank($rankId, 'Name', 1);

        $rank = $this->repository->getRank($rankId, 1);

        $this->assertSame('Name', $rank->name);
    }

    public function testGetRightIds(): void
    {
        $rankId = $this->repository->add(1);
        $this->repository->addRankRight($rankId, 1);

        $this->assertSame([1], $this->repository->getRightIds($rankId));
    }

    public function testGetAvailableRightIds(): void
    {
        $rankId = $this->repository->add(1);
        $this->repository->addRankRight($rankId, 2);

        $this->assertSame([2], $this->repository->getAvailableRightIds(1, $rankId));
    }

    public function testHasActionRights(): void
    {
        $rankId = $this->repository->add(1);
        $this->repository->addRankRight($rankId, 2);

        $this->assertTrue($this->repository->hasActionRights(1, $rankId, 'viewmembers'));
        $this->assertFalse($this->repository->hasActionRights(1, $rankId, 'editdata'));
    }

    public function testDeleteAllianceRanks(): void
    {
        $rankId = $this->repository->add(1);
        $this->repository->addRankRight($rankId, 2);

        $this->repository->deleteAllianceRanks(1);

        $this->assertEmpty($this->repository->getRanks(1));
    }
}
