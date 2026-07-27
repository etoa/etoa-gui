<?php

namespace EtoA\Components\Core;

use EtoA\Bookmark\BookmarkRepository;
use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Entity;
use EtoA\Entity\FleetBookmark;
use EtoA\Entity\Ship;
use EtoA\Fleet\FleetAction;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Ship\ShipSort;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * Editor for a fleet favourite (new and edit). Replaces the legacy bookmark page
 * form together with its xajax functions searchShipList, bookmarkTargetInfo and
 * bookmarkBookmark as well as the fleetBookmark* javascript helpers.
 */
#[AsLiveComponent(template: 'components/fleet_bookmark_editor.html.twig', route: 'live_component_game')]
class FleetBookmarkEditor extends AbstractGameController
{
    use DefaultActionTrait;

    private const MAX_SHIP_SUGGESTIONS = 20;

    #[LiveProp]
    public ?int $bookmarkId = null;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp(writable: true)]
    public string $action = FleetAction::FLIGHT;

    /**
     * Selected ships as shipId => count (the count is kept as entered by the user).
     *
     * Do NOT name this property "ships": when hydrating an array prop, Symfony's
     * PropertyAccess prefers an adder/remover pair over writing the property, so
     * "ships" plus the actions addShip()/removeShip() would make it call
     * addShip($count) with the array *values* instead of setting the array.
     *
     * @var array<int, string>
     */
    #[LiveProp(writable: true)]
    public array $shipCounts = [];

    /** Search term of the ship name input */
    #[LiveProp(writable: true)]
    public string $shipQuery = '';

    // ----- target coordinates -----
    #[LiveProp(writable: true)]
    public string $csx = '1';
    #[LiveProp(writable: true)]
    public string $csy = '1';
    #[LiveProp(writable: true)]
    public string $ccx = '1';
    #[LiveProp(writable: true)]
    public string $ccy = '1';
    #[LiveProp(writable: true)]
    public string $psp = '0';

    /** Entity id of the selected favourite / own planet */
    #[LiveProp(writable: true, onUpdated: 'onBookmarkSelected')]
    public ?int $bookmark = null;

    #[LiveProp(writable: true)]
    public int $speedPercent = 100;

    // ----- freight (negative values mean "leave that amount behind") -----
    #[LiveProp(writable: true)]
    public string $res1 = '0';
    #[LiveProp(writable: true)]
    public string $res2 = '0';
    #[LiveProp(writable: true)]
    public string $res3 = '0';
    #[LiveProp(writable: true)]
    public string $res4 = '0';
    #[LiveProp(writable: true)]
    public string $res5 = '0';
    #[LiveProp(writable: true)]
    public string $resPeople = '0';

    // ----- fetch order -----
    #[LiveProp(writable: true)]
    public string $fetch1 = '0';
    #[LiveProp(writable: true)]
    public string $fetch2 = '0';
    #[LiveProp(writable: true)]
    public string $fetch3 = '0';
    #[LiveProp(writable: true)]
    public string $fetch4 = '0';
    #[LiveProp(writable: true)]
    public string $fetch5 = '0';
    #[LiveProp(writable: true)]
    public string $fetchPeople = '0';

    #[LiveProp]
    public ?string $errorMessage = null;

    #[LiveProp]
    public ?string $successMessage = null;

    /** Resolved target of the current coordinates, computed once per request */
    private ?Entity $resolvedTarget = null;
    private bool $targetResolved = false;

    public function __construct(
        private readonly FleetBookmarkRepository $fleetBookmarkRepository,
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly EntityRepository $entityRepository,
        private readonly ShipDataRepository $shipDataRepository,
        private readonly ConfigurationService $config,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        if ($this->bookmarkId === null) {
            return;
        }

        $bookmark = $this->fleetBookmarkRepository->findOneForUser($this->bookmarkId, $this->getUser()->getData());
        if ($bookmark === null) {
            $this->bookmarkId = null;
            $this->errorMessage = "Flottenfavorit konnte nicht gefunden werden!";

            return;
        }

        $this->name = (string) $bookmark->getName();
        $this->action = $bookmark->getAction();
        $this->speedPercent = (int) $bookmark->getSpeed();
        $this->shipCounts = array_map(static fn (int $count) => (string) $count, $bookmark->getShips());

        $target = $bookmark->getTarget();
        if ($target !== null && $target->getCell() !== null) {
            $this->fillCoordinates($target);
        } else {
            $this->errorMessage = "Ziel wurde nicht gefunden!";
        }

        $freight = $bookmark->getFreight();
        $this->res1 = (string) $freight->metal;
        $this->res2 = (string) $freight->crystal;
        $this->res3 = (string) $freight->plastic;
        $this->res4 = (string) $freight->fuel;
        $this->res5 = (string) $freight->food;
        $this->resPeople = (string) $freight->people;

        $fetch = $bookmark->getFetch();
        $this->fetch1 = (string) $fetch->metal;
        $this->fetch2 = (string) $fetch->crystal;
        $this->fetch3 = (string) $fetch->plastic;
        $this->fetch4 = (string) $fetch->fuel;
        $this->fetch5 = (string) $fetch->food;
        $this->fetchPeople = (string) $fetch->people;
    }

    public function isEdit(): bool
    {
        return $this->bookmarkId !== null;
    }

    /**
     * @return array<string, FleetAction>
     */
    public function getFleetActions(): array
    {
        return FleetAction::getAll(true);
    }

    /**
     * The ships of the fleet with their data for the ship table.
     *
     * @return array<int, array{ship: Ship, count: string}>
     */
    public function getShipRows(): array
    {
        $rows = [];
        foreach (array_keys($this->shipCounts) as $shipId) {
            $ship = $this->shipDataRepository->getShip((int) $shipId, false);
            if ($ship !== null) {
                $rows[(int) $shipId] = ['ship' => $ship, 'count' => (string) $this->shipCounts[$shipId]];
            }
        }

        return $rows;
    }

    /**
     * Ship name suggestions for the current search term (replaces xajax searchShipList).
     *
     * @return array<int, string>
     */
    public function getShipSuggestions(): array
    {
        $query = trim($this->shipQuery);
        if ($query === '') {
            return [];
        }

        // ShipDataRepository::searchShipNames() returns ship entities instead of the
        // documented id => name map, so the map is built here.
        $suggestions = [];
        foreach ($this->shipDataRepository->searchShips(
            ShipSearch::create()->showOrBuildable()->nameLike($query),
            ShipSort::name(),
            self::MAX_SHIP_SUGGESTIONS
        ) as $ship) {
            $suggestions[(int) $ship->getId()] = (string) $ship->getName();
        }

        // Ships which are already part of the fleet are not offered again
        return array_diff_key($suggestions, $this->shipCounts);
    }

    /**
     * The target of the current coordinates, or null if there is none (or it is not
     * discovered yet). Replaces the xajax bookmarkTargetInfo response.
     */
    public function getTargetEntity(): ?Entity
    {
        if ($this->targetResolved) {
            return $this->resolvedTarget;
        }

        $this->targetResolved = true;
        $this->resolvedTarget = null;

        $sx = (int) $this->csx;
        $sy = (int) $this->csy;
        $cx = (int) $this->ccx;
        $cy = (int) $this->ccy;
        $pos = (int) $this->psp;

        if ($sx > 0 && $sy > 0 && $cx > 0 && $cy > 0 && $pos >= 0 && $this->isDiscovered($sx, $sy, $cx, $cy)) {
            $this->resolvedTarget = $this->entityRepository->findByCoordinates(new EntityCoordinates($sx, $sy, $cx, $cy, $pos));
        }

        return $this->resolvedTarget;
    }

    public function isTargetDiscovered(): bool
    {
        return $this->isDiscovered((int) $this->csx, (int) $this->csy, (int) $this->ccx, (int) $this->ccy);
    }

    public function isSaveable(): bool
    {
        return $this->getTargetEntity() !== null && count($this->shipCounts) > 0;
    }

    /**
     * Own planets and target favourites for the target dropdown.
     *
     * @return array<int, array{id: int, label: string}>
     */
    public function getTargetChoices(): array
    {
        $user = $this->getUser()->getData();

        $choices = [];
        foreach ($this->planetRepository->findBy(['user' => $user]) as $planet) {
            if ($planet->getEntity() !== null) {
                $choices[] = ['id' => (int) $planet->getEntity()->getId(), 'label' => 'Eigener Planet: ' . $planet->getEntity()->toString()];
            }
        }

        foreach ($this->bookmarkRepository->findForUser($user) as $bookmark) {
            if ($bookmark->getEntity() !== null) {
                $choices[] = [
                    'id' => (int) $bookmark->getEntity()->getId(),
                    'label' => $bookmark->getEntity()->toString() . ($bookmark->getComment() !== '' ? ' (' . $bookmark->getComment() . ')' : ''),
                ];
            }
        }

        return $choices;
    }

    /**
     * Moves the coordinate inputs to the chosen favourite (replaces xajax bookmarkBookmark).
     */
    public function onBookmarkSelected(): void
    {
        if (!$this->bookmark) {
            return;
        }

        $entity = $this->entityRepository->find($this->bookmark);
        if ($entity !== null && $entity->getCell() !== null) {
            $this->fillCoordinates($entity);
            $this->targetResolved = false;
        }
    }

    #[LiveAction]
    public function addShipToFleet(#[LiveArg] int $id): void
    {
        $ship = $this->shipDataRepository->getShip($id, false);
        if ($ship === null) {
            $this->errorMessage = "Schiff nicht gefunden!";

            return;
        }

        if (!isset($this->shipCounts[$id])) {
            $this->shipCounts[$id] = '1';
        }

        $this->shipQuery = '';
        $this->errorMessage = null;
    }

    #[LiveAction]
    public function removeShipFromFleet(#[LiveArg] int $id): void
    {
        unset($this->shipCounts[$id]);
    }

    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $name = trim($this->name);
        if ($name === '') {
            $this->errorMessage = "Bitte gib einen Namen für den Flottenfavoriten ein!";

            return null;
        }

        if (!array_key_exists($this->action, FleetAction::getAll())) {
            $this->errorMessage = "Ungültige Flottenaktion!";

            return null;
        }

        $ships = [];
        foreach ($this->shipCounts as $shipId => $count) {
            $count = StringUtils::parseFormattedNumber((string) $count);
            if ($count > 0) {
                $ships[(int) $shipId] = $count;
            }
        }
        if (count($ships) === 0) {
            $this->errorMessage = "Es muss mindestens ein Schiff mit einer Anzahl grösser Null gewählt werden!";

            return null;
        }

        if (!$this->isTargetDiscovered()) {
            $this->errorMessage = "Ziel wurde noch nicht entdeckt.";

            return null;
        }

        $target = $this->getTargetEntity();
        if ($target === null) {
            $this->errorMessage = "Es existiert kein Objekt an den angegebenen Koordinaten!";

            return null;
        }

        $user = $this->getUser()->getData();
        $bookmark = $this->bookmarkId !== null
            ? $this->fleetBookmarkRepository->findOneForUser($this->bookmarkId, $user)
            : null;

        if ($this->bookmarkId !== null && $bookmark === null) {
            $this->errorMessage = "Flottenfavorit konnte nicht gefunden werden!";

            return null;
        }

        $isNew = $bookmark === null;
        if ($isNew) {
            $bookmark = new FleetBookmark();
            $bookmark->setUser($user);
        }

        $freight = new BaseResources();
        $freight->metal = StringUtils::parseFormattedNumberSigned($this->res1);
        $freight->crystal = StringUtils::parseFormattedNumberSigned($this->res2);
        $freight->plastic = StringUtils::parseFormattedNumberSigned($this->res3);
        $freight->fuel = StringUtils::parseFormattedNumberSigned($this->res4);
        $freight->food = StringUtils::parseFormattedNumberSigned($this->res5);
        $freight->people = StringUtils::parseFormattedNumberSigned($this->resPeople);

        $fetch = new BaseResources();
        $fetch->metal = StringUtils::parseFormattedNumber($this->fetch1);
        $fetch->crystal = StringUtils::parseFormattedNumber($this->fetch2);
        $fetch->plastic = StringUtils::parseFormattedNumber($this->fetch3);
        $fetch->fuel = StringUtils::parseFormattedNumber($this->fetch4);
        $fetch->food = StringUtils::parseFormattedNumber($this->fetch5);
        $fetch->people = StringUtils::parseFormattedNumber($this->fetchPeople);

        $bookmark
            ->setName($name)
            ->setTarget($target)
            ->setShips($ships)
            ->setFreight($freight)
            ->setFetch($fetch)
            ->setAction($this->action)
            ->setSpeed(max(1, min(100, $this->speedPercent)));

        if ($isNew) {
            $this->fleetBookmarkRepository->persist($bookmark);
        }
        $this->fleetBookmarkRepository->save();

        return $this->redirectToRoute('game.bookmarks.fleet');
    }

    private function fillCoordinates(Entity $entity): void
    {
        $this->csx = (string) $entity->getCell()->getSx();
        $this->csy = (string) $entity->getCell()->getSy();
        $this->ccx = (string) $entity->getCell()->getCx();
        $this->ccy = (string) $entity->getCell()->getCy();
        $this->psp = (string) $entity->getPos();
    }

    private function isDiscovered(int $sx, int $sy, int $cx, int $cy): bool
    {
        if ($sx < 1 || $sy < 1 || $cx < 1 || $cy < 1) {
            return false;
        }

        $absX = (($sx - 1) * $this->config->param1Int('num_of_cells')) + $cx;
        $absY = (($sy - 1) * $this->config->param2Int('num_of_cells')) + $cy;

        return $this->userUniverseDiscoveryService->discovered($this->getUser()->getData(), $absX, $absY);
    }
}
