<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetStatus;
use EtoA\Universe\Resources\ResourceNames;

class ReportContext
{
    /** @var array<string, string>  */
    public array $fleetActions = [];
    /** @var array<int, string>  */
    public array $fleetStatus = [];
    /** @var array<int, string> */
    public array $resourceNames = [];

    public function __construct() {
        $actions = FleetAction::getAll();
        foreach ($actions as $action) {
            $this->fleetActions[$action->code()] = $action->name();
        }
        $this->fleetStatus = FleetStatus::all();
        $this->resourceNames = ResourceNames::NAMES;
    }
}
