<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;

class BookmarkService
{
    private BookmarkRepository $repository;
    private PlanetService $planetService;
    private EntityRepository $entityRepository;

    public function __construct(
        BookmarkRepository $repository,
        PlanetService $planetService,
        EntityRepository $entityRepository
    ) {
        $this->repository = $repository;
        $this->planetService = $planetService;
        $this->entityRepository = $entityRepository;
    }

    function drawSelector(int $userId, string $formElementId, string $js = ""): string
    {
        $userPlanets = $this->planetService->getUserPlanetNames($userId);
        $bookmarks = $this->repository->findForUser($userId);

        ob_start();

        echo "<select id=\"" . $formElementId . "\" onchange=\"" . $js . "\">";
        echo "<option value=\"\">Wählen...</option>";

        foreach ($userPlanets as $id => $name) {
            $entity = $this->entityRepository->findIncludeCell($id);
                echo "<option
                value=\"" . $entity->id . "\"
                data-sx=\"".$entity->sx."\"
                data-sy=\"".$entity->sy."\"
                data-cx=\"".$entity->cx."\"
                data-cy=\"".$entity->cy."\"
                data-pos=\"".$entity->pos."\"
            >" . $entity->toString() ." (" . $name . ")</option>";
        }

        echo "<option value=\"\">-----------------------------</option>";

        foreach ($bookmarks as $bookmark) {
            $entity = $this->entityRepository->findIncludeCell($bookmark->entityId);
            echo "<option
                value=\"" . $entity->id . "\"
                data-sx=\"".$entity->sx."\"
                data-sy=\"".$entity->sy."\"
                data-cx=\"".$entity->cx."\"
                data-cy=\"".$entity->cy."\"
                data-pos=\"".$entity->pos."\"
            >";
            echo $entity->toString();
            if (filled($bookmark->comment)) {
                echo " (" . $bookmark->comment . ")";
            }
            echo "</option>";
        }
        echo "</select>";

        return ob_get_clean();
    }
}
