<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Alliance\AllianceShipPointsService;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\AllianceShipPointsUpdateTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AllianceShipPointsUpdateHandler implements MessageHandlerInterface
{
    private AllianceShipPointsService $service;

    public function __construct(AllianceShipPointsService $service)
    {
        $this->service = $service;
    }

    public function __invoke(AllianceShipPointsUpdateTask $task): SuccessResult
    {
        $this->service->update();

        return SuccessResult::create("Allianz-Schiffsteile berechnet");
    }
}
