<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AllianceDiplomacyRepositoryTest extends AbstractDbTestCase
{
    private AllianceDiplomacyRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AllianceDiplomacyRepository::class];
    }

    public function testGetDiplomacies(): void
    {
        $this->repository->add(1, 2, AllianceDiplomacyLevel::BND_REQUEST, 'Text', 'Test', 3);

        $this->assertNotEmpty($this->repository->getDiplomacies(1));
    }

    public function testGetDiplomacy(): void
    {
        $diplomacyId = $this->repository->add(1, 2, AllianceDiplomacyLevel::BND_REQUEST, 'Text', 'Test', 3);

        $this->assertNotNull($this->repository->getDiplomacy($diplomacyId, 1));
    }

    public function testExistsDiplomacyBetween(): void
    {
        $this->repository->add(1, 2, AllianceDiplomacyLevel::BND_CONFIRMED, 'Text', 'Test', 3);

        $this->assertTrue($this->repository->existsDiplomacyBetween(1, 2));
        $this->assertTrue($this->repository->existsDiplomacyBetween(2, 1));
    }

    public function testAcceptBnd(): void
    {
        $diplomacyId = $this->repository->add(1, 2, AllianceDiplomacyLevel::BND_REQUEST, 'Text', 'Test', 3);

        $this->repository->acceptBnd($diplomacyId, 20);

        $diplomacy = $this->repository->getDiplomacy($diplomacyId, 1);

        $this->assertNotNull($diplomacy);
        $this->assertSame(20, $diplomacy->points);
        $this->assertSame(AllianceDiplomacyLevel::BND_CONFIRMED, $diplomacy->level);
    }

    public function testUpdatePublicText(): void
    {
        $diplomacyId = $this->repository->add(1, 2, AllianceDiplomacyLevel::WAR, 'Text', 'Test', 3);

        $this->repository->updatePublicText($diplomacyId, 1, AllianceDiplomacyLevel::WAR, 'Public Test');

        $diplomacy = $this->repository->getDiplomacy($diplomacyId, 1);

        $this->assertNotNull($diplomacy);
        $this->assertSame('Public Test', $diplomacy->publicText);
    }
}
