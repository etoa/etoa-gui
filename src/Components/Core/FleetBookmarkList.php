<?php

namespace EtoA\Components\Core;

use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Entity\FleetBookmark;
use EtoA\Fleet\QuickLaunchService;
use EtoA\Ship\ShipDataRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * List of the fleet favourites. Starting a stored fleet happens in place
 * (replaces the legacy xajax launchBookmarkProbe call).
 */
#[AsLiveComponent(template: 'components/fleet_bookmark_list.html.twig', route: 'live_component_game')]
class FleetBookmarkList extends AbstractGameController
{
    use DefaultActionTrait;

    /**
     * Launch results per bookmark id: [id => ['success' => bool, 'message' => string]]
     *
     * @var array<int, array{success: bool, message: string}>
     */
    #[LiveProp]
    public array $launchResults = [];

    public function __construct(
        private readonly FleetBookmarkRepository $fleetBookmarkRepository,
        private readonly ShipDataRepository $shipDataRepository,
        private readonly QuickLaunchService $quickLaunchService
    ) {
    }

    /**
     * @return FleetBookmark[]
     */
    public function getBookmarks(): array
    {
        return $this->fleetBookmarkRepository->findForUser($this->getUser()->getData());
    }

    /**
     * The id => name map the template needs; the repository returns ship entities.
     *
     * @return array<int, string>
     */
    public function getShipNames(): array
    {
        $names = [];
        foreach ($this->shipDataRepository->getAllShips(true) as $ship) {
            $names[(int) $ship->getId()] = (string) $ship->getName();
        }

        return $names;
    }

    #[LiveAction]
    public function launch(#[LiveArg] int $id): void
    {
        $bookmark = $this->fleetBookmarkRepository->findOneForUser($id, $this->getUser()->getData());
        if ($bookmark === null) {
            $this->launchResults[$id] = ['success' => false, 'message' => "Der ausgewählte Flottenfavorit ist ungültig!"];

            return;
        }

        $result = $this->quickLaunchService->launchFleetBookmark($bookmark);
        $this->launchResults[$id] = ['success' => $result->success, 'message' => $result->message];
    }
}
