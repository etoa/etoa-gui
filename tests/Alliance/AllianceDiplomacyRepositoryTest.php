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
}
