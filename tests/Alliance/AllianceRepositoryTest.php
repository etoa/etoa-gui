<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AllianceRepositoryTest extends AbstractDbTestCase
{
    private AllianceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.alliance.repository'];
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
}
