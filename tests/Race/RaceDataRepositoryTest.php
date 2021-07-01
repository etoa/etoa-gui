<?php declare(strict_types=1);

namespace EtoA\Race;

use EtoA\AbstractDbTestCase;

class RaceDataRepositoryTest extends AbstractDbTestCase
{
    private RaceDataRepository $raceDataRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->raceDataRepository = $this->app[RaceDataRepository::class];
    }

    public function testGetRaceNames(): void
    {
        $names = $this->raceDataRepository->getRaceNames();

        $this->assertNotEmpty($names);
    }

    public function testGetRaceLeaderTitles(): void
    {
        $leaderTitles = $this->raceDataRepository->getRaceLeaderTitles();

        $this->assertNotEmpty($leaderTitles);
    }

    public function testGetRace(): void
    {
        $race = $this->raceDataRepository->getRace(1);

        $this->assertSame(1, $race->id);
    }

    public function testGetActiveRaces(): void
    {
        $races = $this->raceDataRepository->getActiveRaces();

        $this->assertNotEmpty($races);
    }
}
