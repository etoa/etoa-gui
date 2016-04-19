<?php

namespace EtoA\Tests\Building;

use EtoA\Building\BuildingDataRepository;

class BuildingDataRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var BuildingDataRepository */
    private $buildingDataRepository;

    protected function setUp()
    {
        parent::setUp();

        $app = require dirname(dirname(__DIR__)).'/src/app.php';

        $this->buildingDataRepository = $app['etoa.building.datarepository'];
    }

    public function testGetRaceNames()
    {
        $names = $this->buildingDataRepository->getBuildingNames();
        $this->assertInternalType('array', $names);
        $this->assertNotEmpty($names);

        foreach ($names as $raceId => $raceName) {
            $this->assertInternalType('int', $raceId);
            $this->assertInternalType('string', $raceName);
        }
    }
}

