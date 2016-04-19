<?php

namespace EtoA\Tests\Technology;

use EtoA\Technology\TechnologyDataRepository;

class TechnologyDataRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var TechnologyDataRepository */
    private $technologyDataRepository;

    protected function setUp()
    {
        parent::setUp();

        $app = require dirname(dirname(__DIR__)).'/src/app.php';

        $this->technologyDataRepository = $app['etoa.technology.datarepository'];
    }

    public function testGetTechnologyNames()
    {
        $names = $this->technologyDataRepository->getTechnologyNames();
        $this->assertInternalType('array', $names);
        $this->assertNotEmpty($names);

        foreach ($names as $technologyId => $technologyName) {
            $this->assertInternalType('int', $technologyId);
            $this->assertInternalType('string', $technologyName);
        }
    }
}
