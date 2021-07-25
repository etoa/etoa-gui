<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AllianceApplicationRepositoryTest extends AbstractDbTestCase
{
    private AllianceApplicationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AllianceApplicationRepository::class];
    }

    public function testCountApplications(): void
    {
        $this->repository->addApplication(1, 2, 'Application');

        $this->assertSame(1, $this->repository->countApplications(2));
    }

    public function testGetUserApplication(): void
    {
        $this->repository->addApplication(1, 2, 'Application');

        $this->assertNotNull($this->repository->getUserApplication(1));
    }

    public function testGetAllianceApplications(): void
    {
        $this->repository->addApplication(1, 2, 'Application');
        $this->createUser(1);

        $this->assertNotEmpty($this->repository->getAllianceApplications(2));
    }

    public function testDeleteApplication(): void
    {
        $this->repository->addApplication(1, 2, 'Application');

        $this->assertTrue($this->repository->deleteApplication(1, 2));
    }

    public function testDeleteAllianceApplication(): void
    {
        $this->repository->addApplication(1, 2, 'Application');

        $this->assertSame(1, $this->repository->deleteAllianceApplication(2));
    }

    public function testDeleteUserApplication(): void
    {
        $this->repository->addApplication(1, 2, 'Application');

        $this->assertTrue($this->repository->deleteUserApplication(1));
    }
}
