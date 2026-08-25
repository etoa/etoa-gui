<?php

namespace EtoA\Components\Core;

use EtoA\Bookmark\BookmarkOrder;
use EtoA\Bookmark\BookmarkRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Entity\Bookmark;
use EtoA\Entity\Entity;
use EtoA\Entity\Planet;
use EtoA\Entity\Report;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\QuickLaunchService;
use EtoA\Message\ReportRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * List of the target favourites including their actions. The spy and analyze probes
 * are launched in place (replaces the legacy xajax launchSypProbe/launchAnalyzeProbe
 * calls of the bookmark page).
 */
#[AsLiveComponent(template: 'components/bookmark_target_list.html.twig', route: 'live_component_game')]
class BookmarkTargetList extends AbstractGameController
{
    use DefaultActionTrait;

    /** Entity codes a fleet can be sent to */
    private const FLEET_TARGET_CODES = [
        EntityType::PLANET,
        EntityType::ASTEROID,
        EntityType::WORMHOLE,
        EntityType::NEBULA,
        EntityType::EMPTY_SPACE,
    ];

    #[LiveProp]
    public ?string $probeMessage = null;

    #[LiveProp]
    public bool $probeSuccess = false;

    public function __construct(
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly EntityRepository $entityRepository,
        private readonly QuickLaunchService $quickLaunchService,
        private readonly PlanetService $planetService,
        private readonly ReportRepository $reportRepository
    ) {
    }

    /**
     * @return Bookmark[]
     */
    public function getBookmarks(): array
    {
        $user = $this->getUser()->getData();
        $properties = $user->getUserProperties();

        return $this->bookmarkRepository->findForUser(
            $user,
            new BookmarkOrder($properties?->getItemOrderBookmark(), $properties?->getItemOrderWay())
        );
    }

    #[LiveAction]
    public function launchSpy(#[LiveArg] int $id): void
    {
        $target = $this->entityRepository->find($id);
        if ($target === null) {
            $this->setProbeResult(false, "Problem beim Finden des Zielobjekts!");

            return;
        }

        $result = $this->quickLaunchService->launchSpyProbe($target);
        $this->setProbeResult($result->success, $result->message);
    }

    #[LiveAction]
    public function launchAnalyze(#[LiveArg] int $id): void
    {
        $target = $this->entityRepository->find($id);
        if ($target === null) {
            $this->setProbeResult(false, "Problem beim Finden des Zielobjekts!");

            return;
        }

        $result = $this->quickLaunchService->launchAnalyzeProbe($target);
        $this->setProbeResult($result->success, $result->message);
    }

    /**
     * A fleet can only be sent to space objects, not to markets for example.
     */
    public function canSendFleet(Entity $entity): bool
    {
        return in_array($entity->getCode(), self::FLEET_TARGET_CODES, true);
    }

    /**
     * True for a colonized planet of another player (message, spy, missiles, crypto).
     */
    public function isForeignPlanet(Entity $entity): bool
    {
        return $entity->getCode() === EntityType::PLANET
            && $entity->getOwner() !== null
            && $entity->getOwner() !== $this->getUser()->getData();
    }

    public function allowsAnalyze(Entity $entity): bool
    {
        $type = $entity->getType();

        // A Doctrine hydrated planet has no injected PlanetService, so its own
        // getAllowedFleetActions() would return an empty list.
        $actions = $type instanceof Planet
            ? $this->planetService->getAllowedFleetActions($type)
            : $type->getAllowedFleetActions();

        return in_array(FleetAction::ANALYZE, $actions, true);
    }

    /**
     * The last spy report of the target, used as tooltip of the analyze action.
     */
    public function getLastSpyReport(Entity $entity): ?Report
    {
        if (!$this->getUser()->getData()->getUserProperties()?->isShowCellreports()) {
            return null;
        }

        // loaded directly instead of via ReportRepository::searchReport(), which does
        // not filter out deleted reports
        return $this->reportRepository->findOneBy(
            [
                'user' => $this->getUser()->getData(),
                'type' => 'spy',
                'entity1' => $entity,
                'deleted' => false,
            ],
            ['timestamp' => 'DESC']
        );
    }

    private function setProbeResult(bool $success, string $message): void
    {
        $this->probeSuccess = $success;
        $this->probeMessage = $message;
    }
}
