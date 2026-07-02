<?php

namespace EtoA\Technology;

use EtoA\Building\BuildingCostContext;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Planet;
use EtoA\Entity\Technology;
use EtoA\Entity\TechnologyListItem;
use EtoA\Entity\User;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\Universe\Star\StarRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TechnologyService
{
    public function __construct(
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly Security                     $security,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly RequestStack                 $requestStack,
        private readonly PlanetRepository             $planetRepository,
        private readonly TechnologyDataRepository     $technologyDataRepository,
        private readonly TechnologyTypeRepository     $technologyTypeRepository,
        private readonly BuildingRepository           $buildingRepository,
        private readonly UrlGeneratorInterface        $router,
        private readonly ConfigurationService         $configurationService,
        private readonly StarRepository               $starRepository,
        private readonly TechnologyCostCalculator     $technologyCostCalculator
    )
    {
    }

    public function requirementsPassed(Technology $technology, ?Planet $planet = null, ?User $user = null): bool
    {
        $requirements = $technology->getObjectRequirements();
        $requirements_passed = true;
        $user = $user ?? $this->security->getUser()->getData();
        $request = $this->requestStack->getCurrentRequest();
        $planet = $planet ?? $this->planetRepository->find($request->getSession()->get('cpid'));

        foreach ($requirements as $requirement) {
            if ($requirement->getTech()) {
                if ($requirement->getLevel() > ($this->technologyListItemRepository->findOneBy(['user' => $user, 'technology' => $requirement->getTech()])?->getCurrentLevel() ?? 0)) {
                    $requirements_passed = false;
                }
            }
            if ($requirement->getBuilding()) {
                if ($requirement->getLevel() > ($this->buildingListItemRepository->findOneBy(['user' => $user, 'entity' => $planet, 'building' => $requirement->getBuilding()])?->getCurrentLevel() ?? 0)) {
                    $requirements_passed = false;
                }
            }
        }

        return $requirements_passed;
    }

    //TODO: refactor
    public function renderResearch()
    {
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $user = $this->security->getUser()->getData();
        $use_img_filter = $user->getUserProperties()->isImageFilter();
        $specialist = $user->getSpecialist();

        $userTechnologies = $this->technologyListItemRepository->findForUser($user);
        $techlist = [];

        foreach ($userTechnologies as $userTechnology) {
            $techlist[$userTechnology->getTechnology()->getId()] = $userTechnology;
        }

        ob_start();

        // Load categories
        /** @var TechnologyTypeRepository $technologyTypeRepository */
        $technologyTypes = $this->technologyTypeRepository->getTypes();
        if (count($technologyTypes) > 0) {
            // Load technologies
            $technologies = $this->technologyDataRepository->getTechnologies();
            /** @var array<int, array<Technology>> $groupedTechnologies */
            $groupedTechnologies = [];
            foreach ($technologies as $tech) {
                if (!array_key_exists($tech->getType()->getId(), $groupedTechnologies))
                    $groupedTechnologies[$tech->getType()->getId()] = [];

                array_unshift($groupedTechnologies[$tech->getType()->getId()], $tech);
            }

            $technologyNames = $this->technologyDataRepository->getTechnologyNames(true);

            $cstr = '';
            echo "<div>";
            echo $cstr;
            foreach ($technologyTypes as $technologyType) {
                echo '<table class="tb" style="width:auto"><caption>' . $technologyType->getName() . '</caption>';
                $cnt = 0; // Counter for current row
                $scnt = 0; // Counter for shown techs

                if (isset($groupedTechnologies[$technologyType->getId()])) {
                    // Run through all techs in this cat
                    foreach ($groupedTechnologies[$technologyType->getId()] as $tech) {

                        // Aktuellen Level feststellen wenn Tech vorhanden
                        if (isset($techlist[$tech->getId()])) {
                            $b_level = $techlist[$tech->getId()]->getCurrentLevel();
                            $start_time = $techlist[$tech->getId()]->getStartTime();
                            $end_time = $techlist[$tech->getId()]->getEndTime();
                        } else {
                            $b_level = 0;
                            $end_time = 0;
                        }

                        // Check requirements for this tech
                        $requirements_passed = true;
                        $b_req_info = array();
                        $t_req_info = array();
                        if (isset($b_req[$tech->getId()]['t']) && count($b_req[$tech->getId()]['t']) > 0) {
                            foreach ($b_req[$tech->getId()]['t'] as $b => $l) {
                                if (!$techlist[$b]->getCurrentLevel() || $techlist[$b]->getCurrentLevel() < $l) {
                                    $t_req_info[] = array($b, $l, false);
                                    $requirements_passed = false;
                                } else
                                    $t_req_info[] = array($b, $l, true);
                            }
                        }
                        if (isset($b_req[$tech->getId()]['b']) && count($b_req[$tech->getId()]['b']) > 0) {
                            foreach ($b_req[$tech->getId()]['b'] as $id => $level) {
                                if (!isset($buildlist[$id]) || $buildlist[$id] < $level) {
                                    $requirements_passed = false;
                                    $b_req_info[] = array($id, $level, false);
                                } else {
                                    $b_req_info[] = array($id, $level, true);
                                }
                            }
                        }

                        $filterStyleClass = "";
                        if (!$tech->isShow() && $b_level > 0) {
                            $subtitle = 'Kann nicht erforscht werden';
                            $tmtext = '<span style="color:#999">Es ist nicht vorgesehen dass diese Technologie erforscht werden kann!</span><br/>';
                            $color = '#999';
                            if ($use_img_filter) {
                                $filterStyleClass = "filter-unavailable";
                            }
                            $img = $tech->getImagePath('other');
                        } elseif ($tech->isShow()) {
                            // Voraussetzungen nicht erfüllt
                            if (!$requirements_passed) {
                                $subtitle = 'Voraussetzungen fehlen';
                                $tmtext = '<span style="color:#999">Baue zuerst die nötigen Gebäude und erforsche die nötigen Technologien um diese Technologie zu erforschen!</span><br/>';

                                /** @var BuildingDataRepository $buildingRepository */
                                $buildingNames = $this->buildingRepository->getBuildingNames(true);
                                foreach ($b_req_info as $v) {
                                    $tmtext .= "<div style=\"color:" . ($v[2] ? '#0f0' : '#f30') . "\">" . $buildingNames[$v[0]] . " Stufe " . $v[1] . "</div>";
                                }

                                foreach ($t_req_info as $v) {
                                    $tmtext .= "<div style=\"color:" . ($v[2] ? '#0f0' : '#f30') . "\">" . $technologyNames[$v[0]] . " Stufe " . $v[1] . "</div>";
                                }

                                $color = '#999';
                                if ($use_img_filter) {
                                    $filterStyleClass = "filter-unavailable";
                                }
                                $img = $tech->getImagePath('other');
                            } // Ist im Bau
                            elseif (isset($techlist[$tech->getId()]) && $techlist[$tech->getId()]->getBuildType() === 3) {
                                $subtitle = "Forschung auf Stufe " . ($b_level + 1);
                                $tmtext = "<span style=\"color:#0f0\">Wird erforscht!<br/>Dauer: " . StringUtils::formatTimespan($end_time - time()) . "</span><br/>";
                                $color = '#0f0';
                                if ($use_img_filter) {
                                    $filterStyleClass = "filter-building";
                                }
                                $img = $tech->getImagePath('other');
                            } // Untätig
                            else {
                                // Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
                                $bc = $this->getResearchCosts($tech, $b_level);

                                // Zuwenig Ressourcen
                                if ($b_level < $tech->getLastLevel() && ($planet->getResMetal() < $bc->metal || $planet->getResCrystal() < $bc->crystal || $planet->getResPlastic() < $bc->plastic || $planet->getResFuel() < $bc->fuel || $planet->getResFood() < $bc->food)) {
                                    $tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen f&uuml;r<br/>weitere Forschungen!</span><br/>";
                                    $color = '#f00';
                                    if ($use_img_filter) {
                                        $filterStyleClass = "filter-noresources";
                                    }
                                    $img = $tech->getImagePath('other');
                                } else {
                                    $tmtext = "";
                                    $color = '#fff';
                                    $img = $tech->getImagePath('other');
                                }

                                if ($b_level == 0) {
                                    $subtitle = "Noch nicht erforscht";
                                } elseif ($b_level >= $tech->getLastLevel()) {
                                    $subtitle = 'Vollständig erforscht';
                                } else {
                                    $subtitle = 'Stufe ' . $b_level . '';
                                }
                            }
                        }


                        // Display all buildings that are buildable or are already built
                        if ($tech->isShow() || $b_level > 0) {
                            $img = $tech->getImagePath('medium');

                            if (!$requirements_passed) {
                                $filterStyleClass = "filter-unavailable";
                            }

                            // Display row starter if needed
                            if ($cnt == 0) {
                                echo "<tr>";
                            }

                            echo "<td style=\"width:120px;height:120px ;padding:0px;\">
                                        <div style=\"position:relative;height:120px;overflow:hidden\">
                                        <div class=\"buildOverviewObjectTitle\">" . $tech->getName() . "</div>";
                            echo "<a href=\"" . $this->router->generate('game.research.detail', ['id' => $tech->getId()]) . "\" " . tm($tech->getName(), "<b>" . $subtitle . "</b><br/>" . $tmtext . $tech->getShortComment()) . " style=\"display:block;height:180px;\"><img class=\"" . $filterStyleClass . "\" src=\"" . $img . "\"/></a>";
                            if ($b_level > 0 || ($b_level == 0 && isset($techlist[$tech->getId()]) && $techlist[$tech->getId()]->getBuildType() === 3)) {
                                echo "<div class=\"buildOverviewObjectLevel\" style=\"color:" . $color . "\">" . $b_level . "</div>";
                            }
                            echo "</div></td>\n";

                            $cnt++;
                            $scnt++;
                        }

                        // Display row finisher if needed
                        if ($cnt == 5) {
                            echo "</tr>";
                            $cnt = 0;
                        }
                    }

                    // Fill up missing cols and end row
                    if ($cnt < 5 && $cnt > 0) {
                        for ($x = 0; $x < 5 - $cnt; $x++) {
                            echo "<td class=\"buildOverviewObjectNone\" style=\"width:120px;padding:0px;\">&nbsp;</td>";
                        }
                        echo '</tr>';
                    }

                    // Display message if no tech can be researched
                    if ($scnt == 0) {
                        echo "<tr>
                                            <td class=\"tbldata\" colspan=\"5\" style=\"text-align:center;border:0;width:100%\">
                                                <i>In dieser Kategorie kann momentan noch nichts geforscht werden!</i>
                                            </td>
                                        </tr>";
                    }
                } else {
                    echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center;border:0;width:100%\"><i>In dieser Kategorie kann momentan noch nichts erforscht werden!</i></td></tr>";
                }
                echo "</table>";
            }
            echo '</div>';
        } else {
            echo "<i>Es k&ouml;nnen noch keine Forschungen erforscht werden!</i>";
        }

        return ob_get_clean();
    }

    public function isCurrentlyResearching(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request?->getSession()?->get('cpid'));
        $user = $planet->getUser();
        $userTechnologies = $this->technologyListItemRepository->findForUser($user);

        foreach ($userTechnologies as $userTechnology) {
            if ($userTechnology->getBuildType() > 2) {
                if ($userTechnology->getTechnology()->getId() !== TechnologyId::GEN)
                    return true;
            }
        }

        return false;
    }

    public function isCurrentlyGenResearching(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request?->getSession()?->get('cpid'));
        $user = $planet->getUser();
        $userTechnologies = $this->technologyListItemRepository->findForUser($user);

        foreach ($userTechnologies as $userTechnology) {
            if ($userTechnology->getBuildType() > 2) {
                if ($userTechnology->getTechnology()->getId() === TechnologyId::GEN)
                    return true;
            }
        }

        return false;
    }

    public function getTechnologyData(Technology $technology): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $user = $this->security->getUser()->getData();

        $technologyListItem = $this->technologyListItemRepository->findOneBy(['user' => $user, 'technology' => $technology]);

        if (!$technologyListItem) {
            $technologyListItem = new TechnologyListItem();
            $technologyListItem->setTechnology($technology);
            $technologyListItem->setUser($user);
            $technologyListItem->setCurrentLevel(0);
        }

        $status = $this->getTechnologyStatus($technologyListItem);

        $technologyInfo = [
            'id' => $technology->getId(),
            'name' => $technology->getName(),
            'description' => $technology->getLongComment(),
            'image' => $technology->getImagePath('other'),
            'currentLevel' => $technologyListItem->getCurrentLevel(),
            'maxLevel' => $technology->getLastLevel(),
            'status' => $status,
        ];

        $requirementsPassed = $this->requirementsPassed($technology, $cp, $user);

        if ($requirementsPassed) {
            $currentLevel = $technologyListItem->getCurrentLevel();
            $nextLevel = $currentLevel + 1;

            $researchCosts = null;
            $nextLevelCosts = null;

            if ($nextLevel <= $technology->getLastLevel()) {
                $costs = $this->getResearchCosts($technology, $currentLevel);
                $researchCosts = $this->formatCosts($costs, $cp);
            }

            if (($technologyListItem->getBuildType() == 3) && $nextLevel + 1 <= $technology->getLastLevel()) {
                $costsNext = $this->getResearchCosts($technology, $nextLevel);
                $nextLevelCosts = $this->formatCosts($costsNext, $cp);
            }

            $researchOptions = [
                'canResearch' => $technologyListItem->getBuildType() == 0 && $currentLevel < $technology->getLastLevel(),
                'canCancelResearch' => $technologyListItem->getBuildType() == 3,
                'isResearchable' => $researchCosts && !$researchCosts['hasInsufficientResources'] && !$this->isCurrentlyResearching(),
                'researchError' => $this->getResearchError($technologyListItem, $researchCosts, $technology),
                'researchCosts' => $researchCosts,
                'nextLevelCosts' => $nextLevelCosts,
                'isUnderResearch' => $technologyListItem->getBuildType() == 3,
                'researchEndTime' => $technologyListItem->getEndTime() ?? 0,
                'researchStartTime' => $technologyListItem->getStartTime() ?? 0,
            ];
        } else {
            $researchOptions = [
                'requirementsNotMet' => true,
                'error' => 'Technologie kann nicht erforscht werden, Voraussetzungen nicht erfüllt!',
            ];
        }

        return [
            'technology' => $technologyInfo,
            'options' => $researchOptions,
            'helpUrl' => $this->router->generate('game.help.research.detail', ['id' => $technology->getId()]),
        ];
    }

    private function getTechnologyStatus(TechnologyListItem $item): array
    {
        if ($item->getBuildType() === 3) {
            return [
                'text' => 'Wird erforscht',
                'color' => '#0f0',
            ];
        }

        return [
            'text' => '',
            'color' => '',
        ];
    }

    private function formatCosts(PreciseResources $costs, $planet): array
    {
        $hasInsufficientResources = false;
        $resourcesAvailable = [];

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
            $isAvailable = $costValue <= $currentResource;

            if (!$isAvailable) {
                $hasInsufficientResources = true;
            }

            $resourcesAvailable[$resourceKey] = [
                'cost' => (int)ceil($costValue),
                'available' => $currentResource,
                'sufficient' => $isAvailable,
            ];
        }

        return [
            'time' => $costs->time ?? 0,
            'power' => $costs->power ?? 0,
            'resources' => $resourcesAvailable,
            'hasInsufficientResources' => $hasInsufficientResources,
        ];
    }

    private function getResearchError(TechnologyListItem $item, ?array $costs, Technology $technology): ?string
    {
        if ($item->getCurrentLevel() >= $technology->getLastLevel()) {
            return 'Maximalstufe erreicht! Keine weitere Forschung möglich!';
        }

        if ($this->isCurrentlyResearching() && $technology->getId() !== TechnologyId::GEN) {
            return 'Es wird bereits an einer anderen Technologie geforscht!';
        }

        if ($this->isCurrentlyGenResearching() && $technology->getId() === TechnologyId::GEN) {
            return 'Es wird bereits an Gentechnik geforscht!';
        }

        if ($costs && $costs['hasInsufficientResources']) {
            return 'Zuwenig Rohstoffe vorhanden!';
        }

        return null;
    }

    public function getPeopleOptimized(TechnologyListItem $technologyListItem): float
    {
        $bc = $this->getResearchCosts($technologyListItem->getTechnology(), $technologyListItem->getCurrentLevel() + 1);

        $maxReduction = $bc->time - $bc->time * $this->minBuildTimeFactor();

        return ceil($maxReduction / $this->configurationService->getInt('people_work_done'));
    }

    public function minBuildTimeFactor(): float
    {
        $user = $this->security->getUser()->getData();
        $gentech = $this->technologyListItemRepository->getTechnologyLevel($user, TechnologyId::GEN) ?? 0;
        return (0.1 - ($gentech / 100));
    }

    private function getResearchCosts(Technology $item, int $level): PreciseResources
    {
        // Build context for cost calculation
        $context = new BuildingCostContext();
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $user = $planet->getUser();
        if ($item->getId() === TechnologyId::GEN)
            $peopleWorking = $this->getPeopleWorkingGen();
        else
            $peopleWorking = $this->getPeopleWorking();

        $context->race = $user->getRace();
        $context->specialist = $user->getSpecialist();
        $context->planetType = $planet->getPlanetType();
        $context->solarType = $this->starRepository->findStarForCell($planet->getEntity()->getCell())->getSolarType();
        $context->gentech = $this->technologyListItemRepository->getTechnologyLevel($user, TechnologyId::GEN) ?? 0;
        $context->peopleWorking = $peopleWorking;

        return $this->technologyCostCalculator->calculate($item, $level, $context);
    }

    public function getPeopleWorking()
    {
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $lab = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::TECHNOLOGY]);

        return $lab?->getPeopleWorking() ?? 0;
    }

    public function getPeopleWorkingGen()
    {
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $lab = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::PEOPLE]);

        return $lab?->getPeopleWorking() ?? 0;
    }
}