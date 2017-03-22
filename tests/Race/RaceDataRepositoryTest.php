<?php

namespace EtoA\Race;

use EtoA\AbstractDbTestCase;

class RaceDataRepositoryTest extends AbstractDbTestCase
{
    /** @var RaceDataRepository */
    private $raceDataRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->raceDataRepository = $this->app['etoa.race.datarepository'];
    }

    public function testGetRaceNames()
    {
        $names = $this->raceDataRepository->getRaceNames();
        $this->assertInternalType('array', $names);
        $this->assertNotEmpty($names);

        foreach ($names as $raceId => $raceName) {
            $this->assertInternalType('int', $raceId);
            $this->assertInternalType('string', $raceName);
        }
    }
}
