<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AllianceRepositoryTest extends AbstractDbTestCase
{
    private AllianceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[AllianceRepository::class];
    }

    public function testGetAlliance(): void
    {
        $this->connection->executeQuery("INSERT INTO alliances (alliance_id, alliance_name, alliance_tag) VALUES (42, 'Alliance', 'tag')");

        $alliance = $this->repository->getAlliance(42);

        $this->assertNotNull($alliance);
    }

    public function testGetAllianceNames(): void
    {
        $this->connection->executeQuery("INSERT INTO alliances (alliance_id, alliance_name, alliance_tag) VALUES (12, 'Alliance', 'tag')");

        $allianceNames = $this->repository->getAllianceNames();

        $this->assertSame([12 => 'Alliance'], $allianceNames);
    }

    public function testGetAllianceTags(): void
    {
        $this->connection->executeQuery("INSERT INTO alliances (alliance_id, alliance_name, alliance_tag) VALUES (12, 'Alliance', 'tag')");

        $allianceTags = $this->repository->getAllianceTags();

        $this->assertSame([12 => 'tag'], $allianceTags);
    }

    public function testGetAllianceNamesWithTags(): void
    {
        $this->connection->executeQuery("INSERT INTO alliances (alliance_id, alliance_name, alliance_tag) VALUES (13, 'Alliance', 'tag')");

        $allianceNames = $this->repository->getAllianceNamesWithTags();

        $this->assertSame([13 => '[tag] Alliance'], $allianceNames);
    }

    public function testExists(): void
    {
        $allianceId = $this->repository->create('tag', 'Alliance', 1);

        $this->assertTrue($this->repository->exists('tag', 'Test'));
        $this->assertTrue($this->repository->exists('other', 'Alliance'));
        $this->assertFalse($this->repository->exists('tag', 'Alliance', $allianceId));
    }

    public function testUpdate(): void
    {
        $allianceId = $this->repository->create('tag', 'Alliance', 1);

        $this->repository->update($allianceId, 'new', 'New', 'Text', 'Yes', '', 1, 'test.png', true, true, true);

        $alliance = $this->repository->getAlliance($allianceId);

        $this->assertNotNull($alliance);

        $this->assertTrue($alliance->imageCheck);
    }
}
