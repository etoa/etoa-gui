<?php declare(strict_types=1);

namespace EtoA\Specialist;

use EtoA\AbstractDbTestCase;

class SpecialistDataRepositoryTest extends AbstractDbTestCase
{
    private SpecialistDataRepository $specialistDataRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->specialistDataRepository = $this->app['etoa.specialist.datarepository'];
    }

    public function testGetSpecialistNames(): void
    {
        $names = $this->specialistDataRepository->getSpecialistNames();

        $this->assertNotEmpty($names);
    }

    public function testGetSpecialist(): void
    {
        $specialist = $this->specialistDataRepository->getSpecialist(1);

        $this->assertSame(1, $specialist->id);
    }

    public function testGetActiveSpecialists(): void
    {
        $specialists = $this->specialistDataRepository->getActiveSpecialists();

        $this->assertNotEmpty($specialists);
    }
}
