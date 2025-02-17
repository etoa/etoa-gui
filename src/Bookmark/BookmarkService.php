<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Entity\Bookmark;
use EtoA\Entity\Report;
use EtoA\Message\ReportRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetService;
use Symfony\Bundle\SecurityBundle\Security;

class BookmarkService
{
    public function __construct(
        private readonly BookmarkRepository $repository,
        private readonly PlanetService $planetService,
        private readonly EntityRepository $entityRepository,
        private readonly ReportRepository $reportRepository,
        private readonly Security $security
    ) {
    }

    public function drawSelector(int $userId, string $formElementId, string $js = ""): string
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
                data-sx=\"" . $entity->sx . "\"
                data-sy=\"" . $entity->sy . "\"
                data-cx=\"" . $entity->cx . "\"
                data-cy=\"" . $entity->cy . "\"
                data-pos=\"" . $entity->pos . "\"
            >" . $entity->toString() . " (" . $name . ")</option>";
        }

        echo "<option value=\"\">-----------------------------</option>";

        foreach ($bookmarks as $bookmark) {
            $entity = $this->entityRepository->findIncludeCell($bookmark->entityId);
            echo "<option
                value=\"" . $entity->id . "\"
                data-sx=\"" . $entity->sx . "\"
                data-sy=\"" . $entity->sy . "\"
                data-cx=\"" . $entity->cx . "\"
                data-cy=\"" . $entity->cy . "\"
                data-pos=\"" . $entity->pos . "\"
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

    public function getAnalyzeReports(Bookmark $bookmark)
    {   $report = $this->reportRepository->findOneBy(['user'=>$this->security->getUser()->getData(),'type'=>'spy', 'entity1'=>$bookmark->getEntity()],['timestamp'=>'DESC'])->get;
        if ($report) {
            #$r = Report::createFactory($report);
            #echo "<span " . tm($r->subject, $r . "<br style=\"clear:both\" />") . "><a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->id() . ");\" title=\"Analysieren\">" . icon("spy") . "</a></span>";
        }
    }
}
