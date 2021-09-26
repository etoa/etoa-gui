<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Missile\MissileBattleHandler;
use EtoA\Missile\MissileFlightRepository;
use EtoA\Missile\MissileFlightSearch;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CheckMissilesTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CheckMissilesHandler implements MessageHandlerInterface
{
    private MissileFlightRepository $missileFlightRepository;
    private MissileBattleHandler $missileBattleHandler;

    public function __construct(MissileFlightRepository $missileFlightRepository, MissileBattleHandler $missileBattleHandler)
    {
        $this->missileFlightRepository = $missileFlightRepository;
        $this->missileBattleHandler = $missileBattleHandler;
    }

    public function __invoke(CheckMissilesTask $task): SuccessResult
    {
        $flights = $this->missileFlightRepository->getFlights(MissileFlightSearch::create()->landed());
        $cnt = count($flights);
        foreach ($flights as $flight) {
            $this->missileBattleHandler->battle($flight);
            $cnt++;
        }

        return SuccessResult::create("$cnt Raketen-Aktionen berechnet");
    }
}
