<?php

namespace EtoA\Tests\Defense;

use EtoA\Defense\DefenseDataRepository;

class DefenseDataRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var DefenseDataRepository */
    private $defenseDataRepository;

    protected function setUp()
    {
        parent::setUp();

        $app = require dirname(dirname(__DIR__)).'/src/app.php';

        $this->defenseDataRepository = $app['etoa.defense.datarepository'];
    }

    public function testGetRaceNames()
    {
        $names = $this->defenseDataRepository->getDefenseNames();
        $this->assertInternalType('array', $names);
        $this->assertNotEmpty($names);

        foreach ($names as $raceId => $raceName) {
            $this->assertInternalType('int', $raceId);
            $this->assertInternalType('string', $raceName);
        }
    }
}

