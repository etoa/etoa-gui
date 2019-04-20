<?php declare(strict_types=1);

namespace EtoA\Race;

use EtoA\AbstractDbTestCase;

class RaceDataRepositoryTest extends AbstractDbTestCase
{
    /** @var RaceDataRepository */
    private $raceDataRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->raceDataRepository = $this->app['etoa.race.datarepository'];
    }

    public function testGetRaceNames(): void
    {
        $names = $this->raceDataRepository->getRaceNames();
        $this->assertNotEmpty($names);

        foreach ($names as $raceId => $raceName) {
            $this->assertIsInt($raceId);
        }
    }
}
