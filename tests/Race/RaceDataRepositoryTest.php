<?php

namespace EtoA\Tests\Race;

use EtoA\Race\RaceDataRepository;

class RaceDataRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var RaceDataRepository */
    private $raceDataRepository;

    protected function setUp()
    {
        parent::setUp();

        $app = require_once dirname(dirname(__DIR__)).'/src/app.php';

        $this->raceDataRepository = $app['etoa.race.datarepository'];
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
