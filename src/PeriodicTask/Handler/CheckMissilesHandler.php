<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Missile\MissileFlightRepository;
use EtoA\Missile\MissileFlightSearch;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CheckMissilesTask;
use MissileBattleHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CheckMissilesHandler implements MessageHandlerInterface
{
    private MissileFlightRepository $missileFlightRepository;

    public function __construct(MissileFlightRepository $missileFlightRepository)
    {
        $this->missileFlightRepository = $missileFlightRepository;
    }

    public function __invoke(CheckMissilesTask $task): SuccessResult
    {
        $flights = $this->missileFlightRepository->getFlights(MissileFlightSearch::create()->landed());
        $cnt = count($flights);
        foreach ($flights as $flight) {
            MissileBattleHandler::battle($flight);
            $cnt++;
        }

        return SuccessResult::create("$cnt Raketen-Aktionen berechnet");
    }
}
