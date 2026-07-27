<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Entity\Entity;
use EtoA\Entity\FleetBookmark;
use EtoA\Entity\Ship;
use EtoA\Entity\ShipListItem;
use EtoA\Fleet\Event\FleetLaunch as FleetLaunchEvent;
use EtoA\Ship\ShipListRepository;
use EtoA\Support\StringUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Launches predefined fleets without walking through the haven wizard: fleet
 * bookmarks and the spy/analyze probes. Replaces the legacy xajax functions
 * launchBookmarkProbe (bookmarks.xajax.php) and launchSypProbe/launchAnalyzeProbe
 * (cell.xajax.php).
 */
class QuickLaunchService
{
    public function __construct(
        private readonly FleetLaunchService $fleetLaunchService,
        private readonly ShipListRepository $shipListRepository,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Security $security
    ) {
    }

    /**
     * Starts the fleet stored in a fleet bookmark from the current planet.
     */
    public function launchFleetBookmark(FleetBookmark $bookmark): QuickLaunchResult
    {
        $fleetLaunch = $this->startFleetLaunch();
        if (!$this->fleetLaunchService->checkHaven()) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        $target = $bookmark->getTarget();
        if ($target === null) {
            return QuickLaunchResult::failure("Problem beim Finden des Zielobjekts!");
        }

        if (count($bookmark->getShips()) === 0) {
            return QuickLaunchResult::failure("Der Flottenfavorit enthält keine Schiffe!");
        }

        $shipOutput = [];
        foreach ($bookmark->getShips() as $shipId => $count) {
            $shipListItem = $this->findShipListItem((int) $shipId);
            $added = $this->fleetLaunchService->addShip($shipListItem, (int) $count);
            if ($added === false || $added <= 0) {
                return QuickLaunchResult::failure("Auf deinem Planeten befinden sich nicht genug Schiffe der ausgewählten Typen!");
            }

            $shipOutput[] = StringUtils::formatNumber($added) . " " . ($shipListItem?->getShip()?->getName() ?? '');
        }

        if (!$this->fleetLaunchService->fixShips()) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        if (!$this->fleetLaunchService->setTarget($target, $bookmark->getSpeed())) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        $fleetLaunch->setCostsFood((int) $this->fleetLaunchService->getCostsFood());
        if (!$this->fleetLaunchService->checkTarget()) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        if (!$this->fleetLaunchService->setAction($bookmark->getAction())) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        // Freight is loaded from the planet, the fetch order is carried to the target
        $freight = $bookmark->getFreight();
        $fleetLaunch->loadResource(1, $freight->metal);
        $fleetLaunch->loadResource(2, $freight->crystal);
        $fleetLaunch->loadResource(3, $freight->plastic);
        $fleetLaunch->loadResource(4, $freight->fuel);
        $fleetLaunch->loadResource(5, $freight->food);
        $fleetLaunch->loadPeople($freight->people);

        $fetch = $bookmark->getFetch();
        $fleetLaunch->fetchResource(1, $fetch->metal);
        $fleetLaunch->fetchResource(2, $fetch->crystal);
        $fleetLaunch->fetchResource(3, $fetch->plastic);
        $fleetLaunch->fetchResource(4, $fetch->fuel);
        $fleetLaunch->fetchResource(5, $fetch->food);
        $fleetLaunch->fetchResource(6, $fetch->people);

        $fleet = $this->fleetLaunchService->launch();
        if ($fleet === false) {
            return QuickLaunchResult::failure($this->fleetLaunchService->error ?: $fleetLaunch->getError());
        }

        $this->dispatcher->dispatch(new FleetLaunchEvent(), FleetLaunchEvent::LAUNCH_SUCCESS);

        return QuickLaunchResult::success(
            "Folgende Schiffe sind unterwegs: " . implode(", ", $shipOutput)
            . ". Ankunft in " . StringUtils::formatTimespan($fleet->getRemainingTime())
        );
    }

    /**
     * Sends the spy probes configured in the game settings to the given target.
     */
    public function launchSpyProbe(Entity $target): QuickLaunchResult
    {
        $properties = $this->security->getUser()->getData()->getUserProperties();

        return $this->launchProbe(
            FleetAction::SPY,
            $target,
            $properties?->getSpyShip(),
            $properties?->getSpyShipCount() ?? 0,
            'Spionagesonden',
            'Spionagesonde'
        );
    }

    /**
     * Sends the analyzers configured in the game settings to the given target.
     */
    public function launchAnalyzeProbe(Entity $target): QuickLaunchResult
    {
        $properties = $this->security->getUser()->getData()->getUserProperties();

        return $this->launchProbe(
            FleetAction::ANALYZE,
            $target,
            $properties?->getAnalyzeShip(),
            $properties?->getAnalyzeShipCount() ?? 0,
            'Analysatoren',
            'Analysator'
        );
    }

    private function launchProbe(string $action, Entity $target, ?Ship $ship, int $count, string $pluralName, string $singularName): QuickLaunchResult
    {
        $settingsUrl = $this->urlGenerator->generate('game.config.game');

        if ($ship === null || $count < 1) {
            return QuickLaunchResult::failure(
                "Du hast noch keinen Standard-" . $singularName . " gewählt, überprüfe bitte deine "
                . "<a href=\"" . $settingsUrl . "\">Spieleinstellungen</a>!"
            );
        }

        $fleetLaunch = $this->startFleetLaunch();
        if (!$this->fleetLaunchService->checkHaven()) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        $probeCount = $this->fleetLaunchService->addShip($this->findShipListItem((int) $ship->getId()), $count);
        if ($probeCount === false || $probeCount <= 0) {
            return QuickLaunchResult::failure(
                "Auf deinem Planeten befinden sich keine " . $pluralName . " des "
                . "<a href=\"" . $settingsUrl . "\">gewählten</a> Typs!"
            );
        }

        if (!$this->fleetLaunchService->fixShips()) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        if (!$this->fleetLaunchService->setTarget($target)) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        $fleetLaunch->setCostsFood((int) $this->fleetLaunchService->getCostsFood());
        if (!$this->fleetLaunchService->checkTarget()) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        if (!$this->fleetLaunchService->setAction($action)) {
            return QuickLaunchResult::failure($fleetLaunch->getError());
        }

        $fleet = $this->fleetLaunchService->launch();
        if ($fleet === false) {
            return QuickLaunchResult::failure($this->fleetLaunchService->error ?: $fleetLaunch->getError());
        }

        $this->dispatcher->dispatch(new FleetLaunchEvent(), FleetLaunchEvent::LAUNCH_SUCCESS);

        return QuickLaunchResult::success(
            StringUtils::formatNumber($probeCount) . " " . $pluralName . " unterwegs. Ankunft in "
            . StringUtils::formatTimespan($fleet->getRemainingTime())
        );
    }

    /**
     * Starts with an empty fleet so consecutive launches within one request do not
     * inherit ships or cargo from each other.
     */
    private function startFleetLaunch(): FleetLaunch
    {
        $fleetLaunch = new FleetLaunch();
        $this->fleetLaunchService->setFleetLaunch($fleetLaunch);

        return $fleetLaunch;
    }

    /**
     * The ships of the given type which are stationed on the source planet.
     */
    private function findShipListItem(int $shipId): ?ShipListItem
    {
        return $this->shipListRepository->findOneBy([
            'entity' => $this->fleetLaunchService->getFleetLaunch()->getSourceEntity(),
            'ship' => $shipId,
        ]);
    }
}
