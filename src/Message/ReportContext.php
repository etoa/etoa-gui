<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Fleet;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetStatus;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Resources\ResourceNames;

class ReportContext
{
    /** @var EntityLabel[] */
    public array $entities = [];
    /** @var Fleet[] */
    public array $fleets = [];
    /** @var array<string, string>  */
    public array $fleetActions = [];
    /** @var array<int, string>  */
    public array $fleetStatus = [];
    /** @var array<int, string> */
    public array $resourceNames = [];

    /**
     * @param string[] $userNicks
     * @param string[] $shipNames
     * @param string[] $buildingNames
     * @param string[] $technologyNames
     * @param string[] $defenseNames
     * @param Fleet[] $fleets
     * @param EntityLabel[] $entities
     */
    public function __construct(
        public ConfigurationService $config,
        public array $userNicks,
        public array $shipNames,
        public array $buildingNames,
        public array $technologyNames,
        public array $defenseNames,
        array $fleets,
        array $entities,
    ) {
        $this->entities = [];
        foreach ($entities as $entity) {
            $this->entities[$entity->id] = $entity;
        }

        $this->fleets = [];
        foreach ($fleets as $fleet) {
            $this->fleets[$fleet->getId()] = $fleet;
        }

        $actions = FleetAction::getAll();
        foreach ($actions as $action) {
            $this->fleetActions[$action->code()] = $action->name();
        }

        $this->fleetStatus = FleetStatus::all();
        $this->resourceNames = ResourceNames::NAMES;
    }
}
