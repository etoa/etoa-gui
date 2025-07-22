<?php

namespace EtoA\Technology;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Entity\Planet;
use EtoA\Entity\Technology;
use EtoA\Entity\User;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TechnologyService
{
    public function __construct(
        private readonly TechnologyRequirementRepository $technologyRequirementRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly Security                 $security,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly RequestStack $requestStack,
        private readonly PlanetRepository $planetRepository,
        private readonly TechnologyDataRepository $technologyDataRepository,
        private readonly TechnologyTypeRepository $technologyTypeRepository,
        private readonly BuildingRepository $buildingRepository,
        private readonly UrlGeneratorInterface $router,
    )
    {
    }

    public function requirementsPassed(Technology $technology, ?Planet $planet = null, ?User $user = null):bool
    {
        $requirements = $this->technologyRequirementRepository->findBy(['object'=>$technology],['requiredLevel'=>'DESC']);
        $requirements_passed = true;
        $user = $user??$this->security->getUser()->getData();
        $request = $this->requestStack->getCurrentRequest();
        $planet = $planet??$this->planetRepository->find($request->getSession()->get('cpid'));

        foreach ($requirements as $requirement) {
            if ($requirement->getRequiredTechnology()) {
                if ($requirement->getRequiredLevel() > ($this->technologyListItemRepository->findOneBy(['user'=>$user,'technology'=>$requirement->getRequiredTechnology()])?->getCurrentLevel() ?? 0)) {
                    $requirements_passed = false;
                }
            }
            if ($requirement->getRequiredBuilding()) {
                if ($requirement->getRequiredLevel() > ($this->buildingListItemRepository->findOneBy(['user'=>$user,'entity'=>$planet,'building'=>$requirement->getRequiredBuilding()])?->getCurrentLevel() ?? 0)) {
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
                if(!array_key_exists($tech->getType()->getId(),$groupedTechnologies))
                    $groupedTechnologies[$tech->getType()->getId()] = [];

                array_unshift($groupedTechnologies[$tech->getType()->getId()],$tech);
            }

            $technologyNames = $this->technologyDataRepository->getTechnologyNames(true);

            $cstr = '';
            echo "<div>";
            echo $cstr;
            foreach ($technologyTypes as $technologyType) {
                echo '<table class="tb" style="width:auto"><caption>'.$technologyType->getName().'</caption>';
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
                            $subtitle =  'Kann nicht erforscht werden';
                            $tmtext = '<span style="color:#999">Es ist nicht vorgesehen dass diese Technologie erforscht werden kann!</span><br/>';
                            $color = '#999';
                            if ($use_img_filter) {
                                $filterStyleClass = "filter-unavailable";
                            }
                            $img = $tech->getImagePath('other');
                        } elseif ($tech->isShow()) {
                            // Voraussetzungen nicht erfüllt
                            if (!$requirements_passed) {
                                $subtitle =  'Voraussetzungen fehlen';
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
                            }
                            // Ist im Bau
                            elseif (isset($techlist[$tech->getId()]) && $techlist[$tech->getId()]->getBuildType() === 3) {
                                $subtitle =  "Forschung auf Stufe " . ($b_level + 1);
                                $tmtext = "<span style=\"color:#0f0\">Wird erforscht!<br/>Dauer: " . StringUtils::formatTimespan($end_time - time()) . "</span><br/>";
                                $color = '#0f0';
                                if ($use_img_filter) {
                                    $filterStyleClass = "filter-building";
                                }
                                $img = $tech->getImagePath('other');
                            }
                            // Untätig
                            else {
                                // Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
                                $bc = $this->calcTechCosts($tech, $b_level, $specialist ? $specialist->getCostsTechnologies() : 1);

                                // Zuwenig Ressourcen
                                if ($b_level < $tech->getLastLevel() && ($planet->getResMetal() < $bc['metal'] || $planet->getResCrystal() < $bc['crystal']  || $planet->getResPlastic() < $bc['plastic']  || $planet->getResFuel() < $bc['fuel']  || $planet->getResFood() < $bc['food'])) {
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
                            echo "<a href=\"".$this->router->generate('game.research.detail',['id'=>$tech->getId()])."\" " . tm($tech->getName(), "<b>" . $subtitle . "</b><br/>" . $tmtext . $tech->getShortComment()) . " style=\"display:block;height:180px;\"><img class=\"" . $filterStyleClass . "\" src=\"" . $img . "\"/></a>";
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

    private function calcTechCosts(Technology $technology, $l, $fac = 1)
    {
        // Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
        $bc = array();
        $bc['metal'] = $fac * $technology->getCostsMetal() * pow($technology->getBuildCostsFactor(), $l);
        $bc['crystal'] = $fac * $technology->getCostsCrystal() * pow($technology->getBuildCostsFactor(), $l);
        $bc['plastic'] = $fac * $technology->getCostsPlastic() * pow($technology->getBuildCostsFactor(), $l);
        $bc['fuel'] = $fac * $technology->getCostsFuel() * pow($technology->getBuildCostsFactor(), $l);
        $bc['food'] = $fac * $technology->getCostsFood() * pow($technology->getBuildCostsFactor(), $l);
        return $bc;
    }
}