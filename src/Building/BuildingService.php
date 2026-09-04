<?php

namespace EtoA\Building;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Entity\Building;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Planet;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Universe\Star\StarRepository;
use EtoA\User\UserPropertiesRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BuildingService
{
    public function __construct(
        private readonly Security                     $security,
        private readonly UserPropertiesRepository     $userPropertiesRepository,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly RequestStack                 $requestStack,
        private readonly PlanetRepository             $planetRepository,
        private readonly BuildList                    $buildList,
        private readonly BuildingTypeDataRepository   $buildingTypeDataRepository,
        private readonly BuildingCostCalculator       $buildingCostCalculator,
        private readonly StarRepository               $starRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly GameLogRepository            $gameLogRepository,
        private readonly ConfigurationService         $configurationService,
        private readonly UrlGeneratorInterface        $router,
    )
    {
    }

    private string $errorMsg = '';

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
                    $tmtext = "<span style=\"color:#0f0\">Wird ausgebaut<br/>Dauer: " . StringUtils::formatTimespan($building->bl->getEndTime() - time()) . "</span><br/>";
                    $color = '#0f0';
                    $filterStyleClass = $useImageFilter ? "filter-building" : "";
                } elseif ($buildType === 4) {
                    $subtitle = "Abriss auf Stufe " . ($building->bl->getCurrentLevel() - 1);
                    $tmtext = "<span style=\"color:#f90\">Wird abgerissen!<br/>Dauer: " . StringUtils::formatTimespan($building->bl->getEndTime() - time()) . "</span><br/>";
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
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        $bid = $building->getId();
        $item = $this->buildList->item($bid);

        if (!$item) {
            return [];
        }

        $status = $this->getBuildingStatus($item);

        // Basic building information
        $buildingInfo = [
            'id' => $bid,
            'name' => $item->getName(),
            'description' => $item->getLongComment(),
            'image' => $this->imgPathBig($item->getId()),
            'fieldsPerLevel' => $item->getFields(),
            'fieldsTotalUsed' => $item->getFields() * $item->bl?->getCurrentLevel(),
            'currentLevel' => $item->bl?->getCurrentLevel(),
            'maxLevel' => $item->getLastLevel(),
            'status' => $status,
        ];

        // Check requirements
        $requirementsPassed = $this->buildList->requirementsPassed($bid);

        $buildOptions = [];

        if ($requirementsPassed) {
            $currentLevel = $item->bl?->getCurrentLevel() ?? 0;
            $nextLevel = $currentLevel + 1;

            // Calculate costs for building
            $buildCosts = null;
            $demolishCosts = null;
            $nextLevelCosts = null;

            if ($nextLevel <= $item->getLastLevel()) {
                $costs = $this->getBuildCosts($item->bl, $nextLevel);
                $buildCosts = $this->formatCosts($costs, $cp);
            }
            // Calculate demolish costs if applicable
            if ($item->bl->getCurrentLevel() > 0) {

                $demolishCostsRaw = $this->getBuildCosts($item->bl, $currentLevel);
                // Apply demolish factor
                $demolishCostsAdjusted = clone $demolishCostsRaw;
                $demolishCostsAdjusted->metal *= $item->getDemolishCostsFactor();
                $demolishCostsAdjusted->crystal *= $item->getDemolishCostsFactor();
                $demolishCostsAdjusted->plastic *= $item->getDemolishCostsFactor();
                $demolishCostsAdjusted->fuel *= $item->getDemolishCostsFactor();
                $demolishCostsAdjusted->food *= $item->getDemolishCostsFactor();
                $demolishCostsAdjusted->time *= $item->getDemolishCostsFactor();

                $demolishCosts = $this->formatCosts($demolishCostsAdjusted, $cp);
            }

            // Calculate next level costs if currently building
            if (($item->bl->getBuildType() == 3 || $item->bl->getBuildType() == 4) && $nextLevel + 1 <= $item->getLastLevel()) {
                $costsNext = $this->getBuildCosts($item->bl, $nextLevel + 1);
                $nextLevelCosts = $this->formatCosts($costsNext, $cp);
            }

            // Build action information
            $buildOptions = [
                'canBuild' => $item->bl->getBuildType() == 0 && $item->bl->getCurrentLevel() < $item->getLastLevel(),
                'canDemolish' => $item->bl->getCurrentLevel() > 0 && $item->getDemolishCostsFactor() !== 0 && $item->bl->getBuildType() === 0,
                'canCancelBuild' => $item->bl->getBuildType() == 3,
                'canCancelDemolish' => $item->bl->getBuildType() == 4,
                'isBuildable' => $this->checkBuildable($item->bl) > 0,
                'isDemolishable' => $demolishCosts && $this->checkDemolishable($item->bl),
                'buildError' => $this->checkBuildable($item->bl) <= 0 ? $this->errorMsg : null,
                'demolishError' => $demolishCosts && !$this->checkDemolishable($item->bl) ? $this->errorMsg : null,
                'buildCosts' => $buildCosts,
                'demolishCosts' => $demolishCosts,
                'nextLevelCosts' => $nextLevelCosts,
                'isUnderConstruction' => $this->isUnderConstruction($item->bl->getEntity()),
                'buildEndTime' => $item->bl->getEndTime() ?? 0,
                'buildStartTime' => $item->bl->getStartTime() ?? 0,
            ];
        } else {
            $buildOptions = [
                'requirementsNotMet' => true,
                'error' => 'Gebäude kann nicht (aus)gebaut werden, Voraussetzungen nicht erfüllt!',
            ];
        }

        return [
            'building' => $buildingInfo,
            'options' => $buildOptions,
            'helpUrl' => $this->router->generate('game.help.buildings.detail',['building'=>$bid])
        ];
    }

    private function getBuildingStatus(object $item): array
    {
        if ($item->bl !== null && $item->bl->getBuildType() === 3 && $item->bl->getCurrentLevel() > 0) {
            return [
                'text' => 'Wird ausgebaut',
                'color' => '#0f0',
            ];
        } elseif ($item->bl !== null && $item->bl->getBuildType() === 3) {
            return [
                'text' => 'Wird gebaut',
                'color' => '#0f0',
            ];
        } elseif ($item->bl !== null && $item->bl->getBuildType() === 4) {
            return [
                'text' => 'Wird abgerissen',
                'color' => '#f80',
            ];
        }

        return [
            'text' => '',
            'color' => '',
        ];
    }

    private function formatCosts($costs, $planet): array
    {
        $hasInsufficientResources = false;
        $resourcesAvailable = [];

        $resourceMapping = [
            'metal' => ['getResMetal', 'getProdMetal'],
            'crystal' => ['getResCrystal', 'getProdCrystal'],
            'plastic' => ['getResPlastic', 'getProdPlastic'],
            'fuel' => ['getResFuel', 'getProdFuel'],
            'food' => ['getResFood', 'getProdFood'],
        ];

        foreach ($resourceMapping as $resourceKey => [$resGetter, $prodGetter]) {
            $costValue = $costs->{$resourceKey};
            $currentResource = $planet->{$resGetter}();
            $isAvailable = $costValue <= $currentResource;

            $waitTime = 0;
            if (!$isAvailable) {
                $hasInsufficientResources = true;

                $production = $planet->{$prodGetter}();
                if ($production > 0) {
                    $waitTime = (int) ceil(($costValue - $currentResource) / $production * 3600);
                }
            }

            $resourcesAvailable[$resourceKey] = [
                'cost' => (int)ceil($costValue),
                'available' => $currentResource,
                'sufficient' => $isAvailable,
                'waitTime' => $waitTime,
            ];
        }

        return [
            'time' => $costs->time,
            'power' => $costs->power ?? 0,
            'resources' => $resourcesAvailable,
            'hasInsufficientResources' => $hasInsufficientResources,
        ];
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

    /**
     * Check wether an item is buildable. Conditions are
     * no building under construction, enough resources, not maxed out level, enough fieldsUsed,
     * and satisfied prerequisites.
     *
     *
     * @return int 1=buildable,0=not buildable but show resbox, -1= not buildable & no res box
     */
    private function checkBuildable(BuildingListItem $buildingListItem, bool $uncheckConstruction = false): int
    {
        // check all the buildings
        if (!$this->isUnderConstruction($buildingListItem->getEntity()) || $uncheckConstruction) {
            // check max level
            if ($buildingListItem->getCurrentLevel() < $buildingListItem->getBuilding()->getLastLevel()) {
                // Build context for cost calculation
                $cst = $this->getBuildCosts($buildingListItem, $buildingListItem->getCurrentLevel() + 1);
                // Check costs
                if (
                    $cst->metal <= $buildingListItem->getEntity()->getResMetal()
                    && $cst->crystal <= $buildingListItem->getEntity()->getResCrystal()
                    && $cst->plastic <= $buildingListItem->getEntity()->getResPlastic()
                    && $cst->fuel <= $buildingListItem->getEntity()->getResFuel()
                    && $cst->food <= $buildingListItem->getEntity()->getResFood()
                ) {
                    // check fields
                    if ($buildingListItem->getBuilding()->getFields() === 0 || $buildingListItem->getEntity()->getFieldsUsed() + $buildingListItem->getBuilding()->getFields() <= $buildingListItem->getEntity()->getFields() + $buildingListItem->getEntity()->getFieldsExtra()) {
                        if ($this->requirementsPassed($buildingListItem))
                            $buildableStatus = 1;
                        else {
                            $this->errorMsg = 'Voraussetzungen nicht erf&uuml;llt!';
                            $buildableStatus = -1;
                        }
                    } else {
                        $this->errorMsg = 'Nicht gen&uuml;gend Felder vorhanden!';
                        $buildableStatus = 0;
                    }
                } else {
                    $this->errorMsg = 'Zuwenig Rohstoffe vorhanden!';
                    $buildableStatus = 0;
                }
            } else {
                $this->errorMsg = 'Maximalstufe erreicht! Kein weiterer Ausbau m&ouml;glich!';
                $buildableStatus = -1;
            }
        } else {
            $this->errorMsg = 'Es wird gerade an einem Geb&auml;ude gebaut!';
            $buildableStatus = 0;
        }

        return $buildableStatus;
    }

    public function isUnderConstruction(Planet $planet): bool
    {
        $buildings = $this->buildingListItemRepository->findBy(['entity' => $planet]);
        foreach ($buildings as $building) {
            if (($building->getBuildType() === 3 || $building->getBuildType() === 4) && $building->getEndTime() > time()) {
                return true;
            }
        }

        return false;
    }

    private function getBuildCosts(BuildingListItem $item, int $level): PreciseResources
    {
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $user = $planet->getUser();

        // Build context for cost calculation
        $context = new BuildingCostContext();
        $context->race = $item->getEntity()->getUser()->getRace();
        $context->specialist = $item->getEntity()->getUser()->getSpecialist();
        $context->planetType = $item->getEntity()->getPlanetType();
        $context->solarType = $this->starRepository->findStarForCell($item->getEntity()->getEntity()->getCell())->getSolarType();
        $context->peopleWorking = $this->getPeopleWorking();
        $context->gentech = $this->technologyListItemRepository->getTechnologyLevel($user, TechnologyId::GEN) ?? 0;

        return $this->buildingCostCalculator->calculate($item->getBuilding(), $level, $context);
    }

    private function getDemolishCosts(BuildingListItem $item): float|int|PreciseResources
    {
        $demolishCosts = $this->getBuildCosts($item, $item->getCurrentLevel());

        $demolishCosts->metal = $demolishCosts->metal * $item->getBuilding()->getDemolishCostsFactor();
        $demolishCosts->crystal = $demolishCosts->crystal * $item->getBuilding()->getDemolishCostsFactor();
        $demolishCosts->plastic = $demolishCosts->plastic * $item->getBuilding()->getDemolishCostsFactor();
        $demolishCosts->fuel = $demolishCosts->fuel * $item->getBuilding()->getDemolishCostsFactor();
        $demolishCosts->food = 0;

        return $demolishCosts;
    }

    private function requirementsPassed(BuildingListItem $item): bool
    {
        $requirements = $item->getBuilding()->getObjectRequirements();
        foreach ($requirements as $requirement) {
            if ($requirement->getBuilding() && $requirement->getLevel() > $this->buildingListItemRepository->findOneBy(['user' => $item->getUser(), 'entity' => $item->getEntity(), 'building' => $requirement->getBuilding()])?->getCurrentLevel()) {
                return false;
            }

            if ($requirement->getTech() && $requirement->getLevel() > $this->technologyListItemRepository->findOneBy(['user' => $item->getUser(), 'technology' => $requirement->getTech()])?->getCurrentLevel()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check wether an item is demolishable. Conditions are
     * no building under construction and enough resources.
     */
    private function checkDemolishable(BuildingListItem $item): bool
    {
        if (!$this->getDeactivated($item)) {
            if (!$this->isUnderConstruction($item->getEntity())) {
                $cst = $this->getDemolishCosts($item);

                // Check costs
                if (
                    $cst->metal <= $item->getEntity()->getResMetal()
                    && $cst->crystal <= $item->getEntity()->getResCrystal()
                    && $cst->plastic <= $item->getEntity()->getResPlastic()
                    && $cst->fuel <= $item->getEntity()->getResFuel()
                    && $cst->food <= $item->getEntity()->getResFood()
                ) {
                    return true;
                } else
                    $this->errorMsg = "Zuwenig Rohstoffe vorhanden!";
            } else
                $this->errorMsg = "Es wird gerade an einem Geb&auml;ude gebaut!";
        } else {
            $this->errorMsg = "Das Geb&auml;ude wurde deaktiviert!";
        }
        return false;
    }

    private function getDeactivated(BuildingListItem $buildingListItem): bool|int
    {
        if ($buildingListItem->getDeactivated() > time()) {
            return $buildingListItem->getDeactivated();
        }

        return false;
    }

    public function build(Building $building): bool
    {
        $item = $this->buildList->item($building->getId())->bl;

        if ($this->checkBuildable($item) > 0 && $item->getBuildType() === 0) {
            if ($building->isShow()) {
                $cp = $item->getEntity();
                $specialist = $item->getUser()->getSpecialist();

                $costs = $this->getBuildCosts($item, $item->getCurrentLevel() + 1);
                $item->setStartTime(time());

                $item->setEndTime($item->getStartTime() + $costs->time);
                $item->setBuildType(3);

                $this->buildingListItemRepository->persist($item);
                $item->setPeopleWorkingStatus(true);

                $this->planetRepository->addResources($item->getEntity(), -$costs->metal, -$costs->crystal, -$costs->plastic, -$costs->fuel, -$costs->food);

                $base = $this->buildingListItemRepository->findOneBy(['entity' => $cp, 'building' => BuildingId::BUILDING]);
                $peopleWorking = $base?->getPeopleWorking() ?? 0;

                //Log schreiben
                $log_text = "[b]Gebäudebau[/b]

        [b]Baudauer:[/b] " . StringUtils::formatTimespan($costs->time) . "
        [b]Ende:[/b] " . date("d.m.Y H:i:s", $item->getEndTime()) . "
        [b]Eingesetzte Bewohner:[/b] " . StringUtils::formatNumber($peopleWorking) . "
        [b]Gen-Tech Level:[/b] " . BuildList::$GENTECH . "
        [b]Eingesetzter Spezialist:[/b] " . ($specialist ? $specialist->getName() : "Kein Spezialist") . "

        [b]Kosten[/b]
        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($costs->metal) . "
        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs->crystal) . "
        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($costs->plastic) . "
        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($costs->fuel) . "
        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($costs->food) . "

        [b]Restliche Rohstoffe auf dem Planeten[/b]
        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->getResMetal()) . "
        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->getResCrystal()) . "
        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->getResPlastic()) . "
        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->getResFuel()) . "
        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->getResFood());

                //Log Speichern
                $this->gameLogRepository->add(GameLogFacility::BUILD, LogSeverity::INFO, $log_text, $item->getUser(), $item->getUser()->getAlliance(), $cp->getEntity(), $item->getBuilding()->getId(), 3, $item->getCurrentLevel());
                return true;

            }
        }
        return false;
    }

    public function cancelBuild(BuildingListItem $item): bool
    {
        if ($item->getEndTime() > time()) {
            $cp = $item->getEntity();
            $cu = $item->getUser();
            $costs = $this->getBuildCosts($item, $item->getCurrentLevel()+1);
            $fac = ($item->getEndTime() - time()) / ($item->getEndTime() - $item->getStartTime());

            $this->buildingListItemRepository->updateBuildingListEntry($item, $item->getCurrentLevel(), 0, 0, 0);
            $this->buildingListItemRepository->markBuildingWorkingStatus($cu, $cp, BuildingId::BUILDING->value, false);
            $this->planetRepository->addResources($item->getEntity(), $costs->metal * $fac, $costs->crystal * $fac, $costs->plastic * $fac, $costs->fuel * $fac, $costs->food * $fac);

            //Log schreiben
            $log_text = "[b]Gebäudebau Abbruch[/b]

[b]Start des Gebädes:[/b] " . date("d.m.Y H:i:s", $item->getStartTime()) . "
[b]Ende des Gebädes:[/b] " . date("d.m.Y H:i:s", $item->getEndTime()) . "

[b]Erhaltene Rohstoffe[/b]
[b]Faktor:[/b] " . $fac . "
[b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($costs->metal * $fac) . "
[b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs->crystal * $fac) . "
[b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($costs->plastic * $fac) . "
[b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($costs->fuel * $fac) . "
[b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($costs->food * $fac) . "

[b]Rohstoffe auf dem Planeten[/b]
[b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->getResMetal()) . "
[b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->getResCrystal()) . "
[b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->getResPlastic()) . "
[b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->getResFuel()) . "
[b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->getResFood());

            //Log Speichern
            $this->gameLogRepository->add(GameLogFacility::BUILD, LogSeverity::INFO, $log_text, $cu, $cu?->getAlliance(), $cp->getEntity(), $item->getBuilding()->getId(), 1, $item->getCurrentLevel());

            return true;
        } else
            return false;
    }

    public function demolish(BuildingListItem $item): bool
    {
        if ($this->checkDemolishable($item)) {

            $costs = $this->getDemolishCosts($item);
            $item->setStartTime(time());
            $item->setEndTime($item->getStartTime() + $costs->time);
            $item->setBuildType(4);
            $cp = $item->getEntity();

            $this->buildingListItemRepository->updateBuildingListEntry($item, $item->getCurrentLevel(), $item->getBuildType(), $item->getStartTime(), $item->getEndTime());
            $this->planetRepository->addResources($cp, -$costs->metal, -$costs->crystal, -$costs->plastic, -$costs->fuel, -$costs->food);

            //Log schreiben
            $log_text = "[b]Gebäudeabriss[/b]

        [b]Abrissdauer:[/b] " . StringUtils::formatTimespan($costs->time) . "
        [b]Ende:[/b] " . date("d.m.Y H:i:s", $item->getEndTime()) . "

        [b]Kosten[/b]
        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($costs->metal) . "
        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs->crystal) . "
        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($costs->plastic) . "
        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($costs->fuel) . "
        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($costs->food) . "

        [b]Restliche Rohstoffe auf dem Planeten[/b]
        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->getResMetal()) . "
        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->getResCrystal()) . "
        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->getResPlastic()) . "
        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->getResFuel()) . "
        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->getResFood());

            //Log Speichern
            $this->gameLogRepository->add(GameLogFacility::BUILD, LogSeverity::INFO, $log_text, $item->getUser(), $item->getUser()?->getAlliance(), $cp->getEntity(), $item->getBuilding()->getId(), 4, $item->getCurrentLevel());
            return true;
        }

        return false;
    }

    public function cancelDemolish(BuildingListItem $item): bool
    {
        if ($item->getEndTime() > time()) {
            $costs = $this->getDemolishCosts($item);
            $fac = ($item->getEndTime() - time()) / ($item->getEndTime() - $item->getStartTime());
            $cp = $item->getEntity();
            $cu = $item->getUser();

            $this->buildingListItemRepository->updateBuildingListEntry($item, $item->getCurrentLevel(), 0, 0, 0);
            $this->planetRepository->addResources($cp, $costs->metal * $fac, $costs->crystal * $fac, $costs->plastic * $fac, $costs->fuel * $fac, $costs->food * $fac);

            //Log schreiben
            $log_text = "[b]Gebäudeabriss Abbruch[/b]

            [b]Start des Gebädes:[/b] " . date("d.m.Y H:i:s", $item->getStartTime()) . "
            [b]Ende des Gebädes:[/b] " . date("d.m.Y H:i:s", $item->getEndTime()) . "

            [b]Erhaltene Rohstoffe[/b]
            [b]Faktor:[/b] " . $fac . "
            [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($costs->metal * $fac) . "
            [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs->crystal * $fac) . "
            [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($costs->plastic * $fac) . "
            [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($costs->fuel * $fac) . "
            [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($costs->food * $fac) . "

            [b]Rohstoffe auf dem Planeten[/b]
            [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->getResMetal()) . "
            [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->getResCrystal()) . "
            [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->getResPlastic()) . "
            [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->getResFuel()) . "
            [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->getResFood());

            //Log Speichern
            $this->gameLogRepository->add(GameLogFacility::BUILD, LogSeverity::INFO, $log_text, $cu, $cu?->getAlliance(), $cp->getEntity(), $item->getBuilding()->getId(), 2, $item->getCurrentLevel());

            return true;
        } else
            return false;
    }

    public function getPeopleOptimized(BuildingListItem $buildingListItem): float
    {
        $bc = $this->getBuildCosts($buildingListItem, $buildingListItem->getCurrentLevel() + 1);

        $maxReduction = $bc->time - $bc->time * $this->minBuildTimeFactor();

        return ceil($maxReduction / $this->configurationService->getInt('people_work_done'));
    }

    public function minBuildTimeFactor(): float
    {
        $user = $this->security->getUser()->getData();
        $gentech = $this->technologyListItemRepository->getTechnologyLevel($user, TechnologyId::GEN) ?? 0;
        return (0.1 - ($gentech / 100));
    }

    public function getPeopleWorking()
    {
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $base = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::BUILDING]);

        return $base?->getPeopleWorking() ?? 0;
    }
}