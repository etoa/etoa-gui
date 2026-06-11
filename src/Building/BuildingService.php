<?php

namespace EtoA\Building;

use EtoA\Core\ObjectWithImage;
use EtoA\Entity\Building;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\User\UserPropertiesRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use EtoA\Specialist\SpecialistService;

class BuildingService
{
    public function __construct(
        private readonly Security                   $security,
        private readonly UserPropertiesRepository   $userPropertiesRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly RequestStack               $requestStack,
        private readonly BuildingDataRepository     $buildingDataRepository,
        private readonly TechnologyDataRepository   $technologyDataRepository,
        private readonly PlanetRepository           $planetRepository,
        private readonly BuildList                  $buildList,
        private readonly BuildingTypeDataRepository $buildingTypeDataRepository,
        private readonly BuildingCostCalculator     $buildingCostCalculator,
        private readonly SpecialistService          $specialistService,
        private readonly StarRepository             $starRepository
    )
    {
    }

    public function getBuildingsData(): array
    {
        $buildingTypeNames = $this->buildingTypeDataRepository->getTypeNames();
        if (count($buildingTypeNames) === 0) {
            return [];
        }

        $user = $this->security->getUser()->getData();
        $request = $this->requestStack->getCurrentRequest();
        $properties = $this->userPropertiesRepository->getOrCreateProperties($user);
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        $viewMode = $properties->getItemShow();
        $isCompact = $viewMode !== 'full';
        $useImageFilter = $properties->isImageFilter();

        $context = new BuildingCostContext();
        $context->race = $user->getRace();
        $context->specialist = $user->getSpecialist();
        $context->planetType = $cp->getPlanetType();
        $context->solarType = $this->starRepository->findStarForCell($cp->getEntity()->getCell())->getSolarType();

        $categories = [];

        foreach ($buildingTypeNames as $typeId => $typeName) {
            $buildings = [];
            $it = $this->buildList->getCatIterator($typeId);

            while ($it->valid()) {
                $building = $it->current();
                $buildingId = $it->key();
                $requirementsPassed = $this->buildList->requirementsPassed($buildingId);

                $img = $isCompact ? $this->imgPathSmall($building->getId()) : $this->imgPathMiddle($building->getId());

                $filterStyleClass = "";
                $buildType = $building->bl?->getBuildType();
                $currentLevel = $building->bl?->getCurrentLevel() ?? 0;
                $nextLevel = $currentLevel + 1;

                $costs = $this->buildingCostCalculator->calculate($building, $nextLevel, $context);
                $buildTime = $costs->time;

                if (!$requirementsPassed) {
                    $subtitle = 'Voraussetzungen fehlen';
                    $tmtext = '<span style="color:#999">Baue zuerst die nötigen Gebäude und erforsche die nötigen Technologien um diese Gebäude zu bauen!</span><br/>';
                    $color = '#999';
                    $filterStyleClass = $useImageFilter ? "filter-unavailable" : "";
                } elseif ($buildType === 3) {
                    $subtitle = "Ausbau auf Stufe " . $nextLevel;
                    $tmtext = "<span style=\"color:#0f0\">Wird ausgebaut<br/>Dauer: " . StringUtils::formatTimespan($building->endTime - time()) . "</span><br/>";
                    $color = '#0f0';
                    $filterStyleClass = $useImageFilter ? "filter-building" : "";
                } elseif ($buildType === 4) {
                    $subtitle = "Abriss auf Stufe " . ($building->level - 1);
                    $tmtext = "<span style=\"color:#f90\">Wird abgerissen!<br/>Dauer: " . StringUtils::formatTimespan($building->endTime - time()) . "</span><br/>";
                    $color = '#f90';
                    $filterStyleClass = $useImageFilter ? "filter-destructing" : "";
                } else {
                    $waitArr = $this->getWaitingTimeData($costs, $cp);

                    if ($waitArr['hasInsufficientResources']) {
                        $tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen f&uuml;r weiteren Ausbau!</span><br/>";
                        $color = '#f00';
                        $filterStyleClass = $useImageFilter ? "filter-noresources" : "";
                    } else {
                        $tmtext = "";
                        $color = '#fff';
                    }

                    if (!$currentLevel) {
                        $subtitle = "Noch nicht gebaut";
                    } elseif ($currentLevel === $building->getLastLevel()) {
                        $subtitle = 'Vollständig ausgebaut';
                        $tmtext = '';
                    } else {
                        $subtitle = 'Stufe ' . $currentLevel;
                    }
                }

                $buildings[] = [
                    'id' => $buildingId,
                    'name' => $building->building ?? $building->getName(),
                    'level' => $currentLevel,
                    'currentLevel' => $currentLevel,
                    'img' => $img,
                    'filterStyleClass' => $filterStyleClass,
                    'subtitle' => $subtitle,
                    'tmtext' => $tmtext,
                    'color' => $color,
                    'buildType' => $buildType,
                    'requirementsPassed' => $requirementsPassed,
                    'isMaxLevel' => $currentLevel >= $building->getLastLevel(),
                    'buildTime' => $buildTime,
                    'endTime' => $building->endTime ?? 0,
                    'startTime' => $building->startTime ?? 0,
                    'waitArr' => $waitArr ?? null,
                    'isUnderConstruction' => $this->buildList->isUnderConstruction(),
                    'shortComment' => $building->getShortComment() ?? '',
                ];

                $it->next();
            }

            if (count($buildings) > 0) {
                $categories[] = [
                    'id' => $typeId,
                    'name' => $typeName,
                    'buildings' => $buildings,
                ];
            }
        }

        return [
            'categories' => $categories,
            'viewMode' => $viewMode,
            'isCompact' => $isCompact,
            'numBuildingsPerRow' => $isCompact ? 9 : 5,
            'cellWidth' => $isCompact ? null : 120,
            'tableWidth' => $isCompact ? '' : 'auto',
            'helpUrl' => '?page=help&site=buildings',
        ];
    }

    public function getBuildingData(Building $building): array
    {
        dd($this->buildList->item($building->getId()));
    }

    private function getWaitingTimeData($costs, $planet): array
    {
        $hasInsufficientResources = false;
        $waitString = "";

        $resourceMapping = [
            'metal' => 'getResMetal',
            'crystal' => 'getResCrystal',
            'plastic' => 'getResPlastic',
            'fuel' => 'getResFuel',
            'food' => 'getResFood'
        ];

        foreach ($resourceMapping as $resourceKey => $getter) {
            $costValue = $costs->{$resourceKey};
            $currentResource = $planet->{$getter}();

            $notAvStyle = "";
            if ($costValue > $currentResource) {
                $hasInsufficientResources = true;
                $notAvStyle = ' style="color:red;"';
            }

            $waitString .= '<td' . $notAvStyle . '>' . StringUtils::formatNumber((int)ceil($costValue)) . '</td>';
        }

        return [
            'hasInsufficientResources' => $hasInsufficientResources,
            'string' => $waitString,
        ];
    }

    public function imgPathSmall(string $id): string
    {
        return ObjectWithImage::BASE_PATH . "/buildings/building" . $id . "_small.png";
    }

    public function imgPathMiddle(string $id): string
    {
        return ObjectWithImage::BASE_PATH . "/buildings/building" . $id . "_middle.png";
    }

    public function imgPathBig(string $id): string
    {
        return ObjectWithImage::BASE_PATH . "/buildings/building" . $id . ".png";
    }
}