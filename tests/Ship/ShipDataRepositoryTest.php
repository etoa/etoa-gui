<?php

namespace EtoA\Tests\Ship;

use EtoA\Ship\ShipDataRepository;

class ShipDataRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ShipDataRepository */
    private $shipDataRepository;

    protected function setUp()
    {
        parent::setUp();

        $app = require dirname(dirname(__DIR__)).'/src/app.php';

        $this->shipDataRepository = $app['etoa.ship.datarepository'];
    }

    public function testGetShipNames()
    {
        $names = $this->shipDataRepository->getShipNames();
        $this->assertInternalType('array', $names);
        $this->assertNotEmpty($names);

        foreach ($names as $shipId => $shipName) {
            $this->assertInternalType('int', $shipId);
            $this->assertInternalType('string', $shipName);
        }
    }
}
