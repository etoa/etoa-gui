<?php

namespace EtoA\Components\Core;

use EtoA\Controller\Game\AbstractGameController;
use EtoA\Entity\MessageData;
use EtoA\Fleet\Event\FleetLaunch as FleetLaunchEvent;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetLaunch;
use EtoA\Fleet\FleetLaunchService;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * Action selection step of the fleet launch wizard (replaces the legacy xajax
 * havenShowAction / havenShowLaunch / havenCheckAction / havenCheckRes /
 * havenCheckPeople / havenSetResAll / havenSetFetchAll / havenCheckSupport functions).
 */
#[AsLiveComponent(template: 'components/haven_action.html.twig', route: 'live_component_game')]
class HavenAction extends AbstractGameController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $action = null;

    #[LiveProp(writable: true, onUpdated: 'clampCargoProps')]
    public string $res1 = '0';
    #[LiveProp(writable: true, onUpdated: 'clampCargoProps')]
    public string $res2 = '0';
    #[LiveProp(writable: true, onUpdated: 'clampCargoProps')]
    public string $res3 = '0';
    #[LiveProp(writable: true, onUpdated: 'clampCargoProps')]
    public string $res4 = '0';
    #[LiveProp(writable: true, onUpdated: 'clampCargoProps')]
    public string $res5 = '0';
    #[LiveProp(writable: true, onUpdated: 'clampCargoProps')]
    public string $people = '0';

    /** Percentage of the source resources that fit into the free capacity (set by "Alles einladen") */
    #[LiveProp]
    public ?float $loadPercent = null;

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
    public string $fetchp = '0';

    #[LiveProp(writable: true)]
    public string $supportHour = '0';
    #[LiveProp(writable: true)]
    public string $supportMin = '0';
    #[LiveProp(writable: true)]
    public string $supportSecond = '0';

    #[LiveProp(writable: true)]
    public ?int $fakeShip = null;

    #[LiveProp(writable: true)]
    public array $msgUser = [];

    #[LiveProp(writable: true)]
    public string $messageText = '';

    // Result state after a successful launch
    #[LiveProp]
    public bool $launched = false;
    #[LiveProp]
    public ?int $launchedFleetId = null;
    #[LiveProp]
    public string $launchColor = '';
    #[LiveProp]
    public string $launchName = '';
    #[LiveProp]
    public array $launchedRes = [];

    #[LiveProp]
    public ?string $errorMessage = null;

    private ?FleetLaunch $computedFleet = null;
    private bool $computed = false;
    /** @var array<string, FleetAction> */
    private array $allowedActions = [];

    public function __construct(
        private readonly FleetLaunchService $fleetLaunchService,
        private readonly EntityRepository $entityRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly RequestStack $requestStack,
        private readonly SerializerInterface $serializer,
        private readonly ShipDataRepository $shipDataRepository,
        private readonly UserRepository $userRepository,
        private readonly MessageRepository $messageRepository,
        private readonly MessageCategoryRepository $messageCategoryRepository,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    /**
     * Returns the fleet with the currently selected action and cargo applied.
     * Computed once per request (lazy) so every re-render reflects the live props.
     */
    public function getFleetLaunch(): ?FleetLaunch
    {
        if (!$this->computed) {
            $this->computed = true;
            $this->computedFleet = $this->recalculate();
        }

        return $this->computedFleet;
    }

    private function loadFromSession(): ?FleetLaunch
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if (!$session->has('fleetLaunch')) {
            return null;
        }

        /** @var FleetLaunch $fleetLaunch */
        $fleetLaunch = $this->serializer->deserialize($session->get('fleetLaunch'), FleetLaunch::class, 'json', [
            'allow_extra_attributes' => true,
        ]);

        // The deserialized entities are not managed Doctrine entities, so rebuild them
        // from the database to get a working getType()/getOwner().
        $fleetLaunch->setTargetEntity($this->rebuildEntity($fleetLaunch->getTargetEntity()));
        if ($fleetLaunch->getWormholeEntryEntity() !== null) {
            $fleetLaunch->setWormholeEntryEntity($this->rebuildEntity($fleetLaunch->getWormholeEntryEntity()));
        }
        if ($fleetLaunch->getWormholeExitEntity() !== null) {
            $fleetLaunch->setWormholeExitEntity($this->rebuildEntity($fleetLaunch->getWormholeExitEntity()));
        }

        // Reload the source planet fresh so resource clamping uses current values
        $fleetLaunch->setSourceEntity($this->planetRepository->find($session->get('cpid')));

        // getSpeed()/getCostsPerHundredAE() are computed getters but setSpeed()/... store raw
        // values, so the serializer corrupts them on every round-trip (the speed factor gets
        // re-applied). Recompute the raw values from the ships array, which survives serialization.
        $this->restoreRawFleetValues($fleetLaunch);

        return $fleetLaunch;
    }

    private function restoreRawFleetValues(FleetLaunch $fleetLaunch): void
    {
        $ships = $fleetLaunch->getShips();
        if (!empty($ships)) {
            $fleetLaunch->setSpeed(min(array_map(static fn ($s) => $s['speed'], $ships)));
            $fleetLaunch->setCostsPerHundredAE(array_sum(array_map(static fn ($s) => $s['costs_per_ae'] ?? 0, $ships)));
        }
    }

    private function rebuildEntity(?\EtoA\Entity\Entity $entity): ?\EtoA\Entity\Entity
    {
        if ($entity === null || $entity->getCell() === null) {
            return $entity;
        }

        $rebuilt = $this->entityRepository->findByCoordinates(new EntityCoordinates(
            $entity->getCell()->getSx(),
            $entity->getCell()->getSy(),
            $entity->getCell()->getCx(),
            $entity->getCell()->getCy(),
            $entity->getPos()
        ));

        return $rebuilt ?? $entity;
    }

    private function recalculate(): ?FleetLaunch
    {
        $fleetLaunch = $this->loadFromSession();
        if ($fleetLaunch === null) {
            return null;
        }

        $this->fleetLaunchService->setFleetLaunch($fleetLaunch);

        // Recompute the flight parameters (distance + duration) for the managed target entity;
        // the serialized values are not reliable after the session round-trip.
        if ($fleetLaunch->getTargetEntity() !== null) {
            $this->fleetLaunchService->setTarget($fleetLaunch->getTargetEntity(), $fleetLaunch->getSpeedPercent());
        }

        $this->fleetLaunchService->checkTarget();

        $this->allowedActions = $this->fleetLaunchService->getAllowedActions();

        // Default to the first available action
        if ($this->action === null || !isset($this->allowedActions[$this->action])) {
            $this->action = array_key_first($this->allowedActions);
        }

        // Base flight food cost and fuel-used cache (both are needed by getCapacity())
        $fleetLaunch->setCostsFood((int) $this->fleetLaunchService->getCostsFood());
        $fleetLaunch->getCosts();

        // Support time reserves capacity before the cargo is loaded
        $fleetLaunch->resetSupport();
        if ($this->action === FleetAction::SUPPORT) {
            $supportTime = (int) $this->supportSecond + (int) $this->supportMin * 60 + (int) $this->supportHour * 3600;
            $maxTime = $this->fleetLaunchService->getSupportMaxTime();
            $supportTime = (int) min($supportTime, $maxTime);
            $fleetLaunch->setSupportTime($supportTime);
        }

        // Cargo
        if ($this->action === FleetAction::FETCH) {
            for ($id = 1; $id <= 5; $id++) {
                $fleetLaunch->fetchResource($id, StringUtils::parseFormattedNumber($this->{'fetch' . $id}));
            }
            $fleetLaunch->fetchResource(6, StringUtils::parseFormattedNumber($this->fetchp));
            for ($id = 1; $id <= 5; $id++) {
                $fleetLaunch->loadResource($id, 0);
            }
            $fleetLaunch->loadPeople(0);
        } else {
            // Only load into the fleet (for the capacity/cost display). The clamped values are
            // written back to the live props in clampCargoProps(), which runs BEFORE rendering
            // (the writable props are already serialized by {{ attributes }} at render start).
            for ($id = 1; $id <= 5; $id++) {
                $fleetLaunch->loadResource($id, StringUtils::parseFormattedNumber($this->{'res' . $id}));
            }
            $fleetLaunch->loadPeople(StringUtils::parseFormattedNumber($this->people));
        }

        // Fake attack: which ship should the fleet be disguised as
        if ($this->action === FleetAction::FAKE_ATTACK && $this->fakeShip) {
            $fleetLaunch->setFakeId($this->fakeShip);
        }

        return $fleetLaunch;
    }

    /**
     * @return array<string, FleetAction>
     */
    public function getAllowedActionObjects(): array
    {
        $this->getFleetLaunch();

        return $this->allowedActions;
    }

    public function getServiceError(): string
    {
        $this->getFleetLaunch();

        return $this->fleetLaunchService->error;
    }

    /**
     * @return array<int, string>
     */
    public function getFakeableShips(): array
    {
        return $this->shipDataRepository->getFakeableShipNames();
    }

    /**
     * @return array<int, string>
     */
    public function getAllianceMembers(): array
    {
        $alliance = $this->getFleetLaunch()?->getOwner()?->getAlliance();
        if ($alliance === null) {
            return [];
        }

        return $this->userRepository->searchUserNicknames(UserSearch::create()->allianceId($alliance));
    }

    public function isFetchMode(): bool
    {
        return $this->action === FleetAction::FETCH;
    }

    public function isFakeAttack(): bool
    {
        return $this->action === FleetAction::FAKE_ATTACK;
    }

    public function isSupportAction(): bool
    {
        return $this->action === FleetAction::SUPPORT;
    }

    public function isAllianceMessage(): bool
    {
        $fleetLaunch = $this->getFleetLaunch();

        return $this->action === FleetAction::ALLIANCE
            && $fleetLaunch?->getLeader() === null
            && $fleetLaunch?->getOwner()?->getAlliance() !== null;
    }

    #[LiveAction]
    public function reset(): RedirectResponse
    {
        $this->requestStack->getCurrentRequest()->getSession()->remove('fleetLaunch');

        return $this->redirectToRoute('game.haven.show');
    }

    #[LiveAction]
    public function backToTarget(): RedirectResponse
    {
        $fleetLaunch = $this->loadFromSession();
        if ($fleetLaunch !== null) {
            $fleetLaunch->unsetWormhole();
            $this->saveToSession($fleetLaunch);
        }

        return $this->redirectToRoute('game.haven.target');
    }

    #[LiveAction]
    public function loadAll(): void
    {
        $fleet = $this->loadFromSession();
        if ($fleet === null) {
            return;
        }

        // Apply flight/food/support costs so getCapacity() reflects the truly free space (res = 0)
        $this->fleetLaunchService->setFleetLaunch($fleet);
        $this->fleetLaunchService->checkTarget();
        $fleet->setCostsFood((int) $this->fleetLaunchService->getCostsFood());
        $fleet->getCosts();
        $fleet->resetSupport();
        if ($this->action === FleetAction::SUPPORT) {
            $supportTime = (int) $this->supportSecond + (int) $this->supportMin * 60 + (int) $this->supportHour * 3600;
            $fleet->setSupportTime((int) min($supportTime, $this->fleetLaunchService->getSupportMaxTime()));
        }
        for ($id = 1; $id <= 5; $id++) {
            $fleet->loadResource($id, 0);
        }

        $source = $fleet->getSourceEntity();
        $sum = floor($source->getResMetal()) + floor($source->getResCrystal()) + floor($source->getResPlastic()) + floor($source->getResFuel()) + floor($source->getResFood());
        $loadPerc = $sum > 0 ? min(1.0, $fleet->getCapacity() / $sum) : 0.0;
        $this->loadPercent = round($loadPerc * 100, 2);

        // Metal/crystal/plastic/fuel proportionally, food and passengers fully (as in the legacy havenSetResAll)
        $this->res1 = StringUtils::formatNumber(floor($source->getResMetal() * $loadPerc));
        $this->res2 = StringUtils::formatNumber(floor($source->getResCrystal() * $loadPerc));
        $this->res3 = StringUtils::formatNumber(floor($source->getResPlastic() * $loadPerc));
        $this->res4 = StringUtils::formatNumber(floor($source->getResFuel() * $loadPerc));
        $this->res5 = StringUtils::formatNumber(floor($source->getResFood() * $loadPerc));
        $this->people = StringUtils::formatNumber(floor($source->getPeople()));
    }

    /**
     * Clamps the resource/passenger props to the free capacity and writes the clamped values
     * back. Runs BEFORE rendering (from onUpdated hooks / the max actions) so the serialized
     * live props actually reflect what fits. Also clears the "Alles einladen" percentage.
     */
    public function clampCargoProps(): void
    {
        $this->loadPercent = null;

        $fleet = $this->getFleetLaunch();
        if ($fleet === null || $this->isFetchMode()) {
            return;
        }
        for ($id = 1; $id <= 5; $id++) {
            $this->{'res' . $id} = StringUtils::formatNumber($fleet->getLoadedRes($id));
        }
        $this->people = StringUtils::formatNumber($fleet->getCapacityPeopleLoaded());
    }

    #[LiveAction]
    public function maxRes(#[LiveArg] int $id): void
    {
        $fleet = $this->loadFromSession();
        if ($fleet === null) {
            return;
        }
        $source = $fleet->getSourceEntity();
        $amount = match ($id) {
            1 => $source->getResMetal(),
            2 => $source->getResCrystal(),
            3 => $source->getResPlastic(),
            4 => $source->getResFuel(),
            5 => $source->getResFood(),
            default => 0,
        };
        $this->{'res' . $id} = StringUtils::formatNumber(floor($amount));
        // Clamp to the free capacity right away (before rendering)
        $this->clampCargoProps();
    }

    #[LiveAction]
    public function maxPeople(): void
    {
        $fleet = $this->loadFromSession();
        if ($fleet === null) {
            return;
        }
        $this->people = StringUtils::formatNumber(floor($fleet->getSourceEntity()->getPeople()));
        $this->clampCargoProps();
    }

    #[LiveAction]
    public function maxFetch(#[LiveArg] int $id): void
    {
        $fleet = $this->loadFromSession();
        if ($fleet === null) {
            return;
        }
        $this->{'fetch' . $id} = StringUtils::formatNumber(floor($fleet->getTotalCapacity()));
    }

    #[LiveAction]
    public function maxFetchp(): void
    {
        $fleet = $this->loadFromSession();
        if ($fleet === null) {
            return;
        }
        $this->fetchp = StringUtils::formatNumber(floor($fleet->getTotalPeopleCapacity()));
    }

    #[LiveAction]
    public function fetchAll(): void
    {
        $fleet = $this->getFleetLaunch();
        if ($fleet === null) {
            return;
        }
        $capacity = StringUtils::formatNumber(floor($fleet->getTotalCapacity()));
        $this->fetch1 = $capacity;
        $this->fetch2 = $capacity;
        $this->fetch3 = $capacity;
        $this->fetch4 = $capacity;
        $this->fetch5 = $capacity;
        $this->fetchp = StringUtils::formatNumber(floor($fleet->getTotalPeopleCapacity()));
    }

    #[LiveAction]
    public function launch(): ?RedirectResponse
    {
        $this->errorMessage = null;

        $fleetLaunch = $this->getFleetLaunch();
        if ($fleetLaunch === null || $this->action === null) {
            return null;
        }

        if (!$this->fleetLaunchService->setAction($this->action)) {
            $this->errorMessage = 'Ungültige Aktion! ' . $fleetLaunch->getError();

            return null;
        }

        // Alliance attacks can only be joined while the leader fleet is still reachable.
        // Reload the alliance fleets fresh (the serialized ones are not real Fleet entities).
        $this->fleetLaunchService->loadAllianceFleets();
        $duration = $fleetLaunch->getSpeed() > 0 ? ceil($fleetLaunch->getDistance() / $fleetLaunch->getSpeed() * 3600) : 0;
        $maxTime = 0;
        $aFleets = $fleetLaunch->getAFleets();
        if (count($aFleets) > 0) {
            $maxTime = $aFleets[0]->getLandTime() - time() - $fleetLaunch->getTimeLaunchLand() - $fleetLaunch->getDuration1();
        }
        if (!($duration < $maxTime || $this->action !== FleetAction::ALLIANCE || $maxTime < 0)) {
            $this->errorMessage = 'Angriff kann nicht mehr erreicht werden!';

            return null;
        }

        $fleet = $this->fleetLaunchService->launch();
        if ($fleet === false) {
            $this->errorMessage = 'Start nicht möglich! ' . $this->fleetLaunchService->error . ' ' . $fleetLaunch->getError();

            return null;
        }

        // Notify the selected alliance members about the alliance attack
        if ($this->action === FleetAction::ALLIANCE
            && $fleetLaunch->getLeader() === null
            && $fleetLaunch->getOwner()->getAlliance() !== null
            && count($this->msgUser) > 0
        ) {
            $sender = $this->getUser()->getData();
            $alliance = $fleetLaunch->getOwner()->getAlliance();
            $subject = "Allianzangriff (" . $fleetLaunch->getTargetEntity()->toString() . ")";
            $text = "[b]Angriffsdaten:[/b][table]"
                . "[tr][td]Flottenkennzeichen:[/td][td]" . $alliance->getTag() . "-" . $fleet->getId() . "[/td][/tr]"
                . "[tr][td]Flottenleader:[/td][td]" . $fleetLaunch->getOwner()->getNick() . "[/td][/tr]"
                . "[tr][td]Zielplanet:[/td][td]" . $fleetLaunch->getTargetEntity()->toString() . "[/td][/tr]"
                . "[tr][td]Ankunftszeit:[/td][td]" . date("d.m.y, H:i:s", $fleet->getLandTime()) . "[/td][/tr][/table]"
                . $this->messageText;
            $category = $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE);
            foreach ($this->msgUser as $uid) {
                $receiver = $this->userRepository->find((int) $uid);
                if ($receiver !== null) {
                    $messageData = new MessageData();
                    $messageData->setSubject($subject);
                    $messageData->setText($text);
                    $this->messageRepository->sendFromUserToUser($sender, $receiver, $messageData, $category, $fleet);
                }
            }
        }

        // Capture the result for the confirmation view
        $ac = FleetAction::createFactory($this->action);
        $this->launchedFleetId = $fleet->getId();
        $this->launchColor = $ac ? FleetAction::$attitudeColor[$ac->attitude()] : '';
        $this->launchName = $ac ? $ac->name() : '';
        $this->launchedRes = [
            1 => StringUtils::formatNumber($fleetLaunch->getLoadedRes(1)),
            2 => StringUtils::formatNumber($fleetLaunch->getLoadedRes(2)),
            3 => StringUtils::formatNumber($fleetLaunch->getLoadedRes(3)),
            4 => StringUtils::formatNumber($fleetLaunch->getLoadedRes(4)),
            5 => StringUtils::formatNumber($fleetLaunch->getLoadedRes(5)),
        ];
        $this->launched = true;

        // Reset the wizard state and fire the launch event (parity with the legacy flow)
        $this->requestStack->getCurrentRequest()->getSession()->remove('fleetLaunch');
        $this->dispatcher->dispatch(new FleetLaunchEvent(), FleetLaunchEvent::LAUNCH_SUCCESS);

        return null;
    }

    private function saveToSession(FleetLaunch $fleetLaunch): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->set('fleetLaunch', $this->serializer->serialize($fleetLaunch, 'json', [
            'circular_reference_handler' => function ($object) {
                if (is_a($object, AbstractEntity::class)) {
                    return $object->getEntity()->getId();
                }

                return $object->getId();
            },
            'ignored_attributes' => ['__initializer__', '__cloner__', '__isInitialized__', 'lazyObjectState', 'lazyObjectInitialized', 'lazyObjectAsInitialized'],
            'skip_null_values' => true,
        ]));
    }
}
