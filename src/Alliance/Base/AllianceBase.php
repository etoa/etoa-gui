<?php declare(strict_types=1);

namespace EtoA\Alliance\Base;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Alliance\AllianceWithMemberCount;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\AllianceBuilding;
use EtoA\Entity\AllianceBuildListItem;
use EtoA\Entity\AllianceTechnology;
use EtoA\Entity\AllianceTechnologyListItem;
use EtoA\Entity\User;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use EtoA\UI\Tooltip;
use EtoA\UI\Countdown;

class AllianceBase
{
    private array $resName = array(
        0 => ResourceNames::METAL,
        1 => ResourceNames::CRYSTAL,
        2 => ResourceNames::PLASTIC,
        3 => ResourceNames::FUEL,
        4 => ResourceNames::FOOD
    );

    public function __construct(
        private readonly ConfigurationService $config,
        private readonly AllianceRepository $allianceRepository,
        private readonly AllianceHistoryRepository $allianceHistoryRepository,
        private readonly AllianceTechnologyRepository $allianceTechnologyRepository,
        private readonly AllianceBuildingRepository $allianceBuildingRepository,
        private readonly AllianceService $allianceService,
        private readonly UserRepository $userRepository,
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly Security                 $security,
        private readonly Tooltip                  $tooltip,
    )
    {}

    public function getTechnologyBuildStatus(AllianceWithMemberCount $alliance, AllianceTechnology $technology, ?AllianceTechnologyListItem $item, AllianceItemRequirementStatus $requirementStatus): AllianceItemBuildStatus
    {
        if (!$requirementStatus->requirementsMet($technology->getId())) {
            return AllianceItemBuildStatus::missingRequirements();
        }

        $level = $item !== null ? $item->getLevel() + 1 : 1;
        if ($technology->getLastLevel() <= $level) {
            return AllianceItemBuildStatus::maxLevel();
        }

        if ($item !== null && $item->getBuildEndTime() > 0) {
            return AllianceItemBuildStatus::itemUnderConstruction();
        }

        if ($requirementStatus->isUnderConstruction()) {
            return AllianceItemBuildStatus::underConstruction();
        }

        $allianceResources = $alliance->getResources();
        $costs = $technology->calculateCosts($level, $alliance->memberCount, $this->config->getFloat('alliance_membercosts_factor'));

        $missingResources = $costs->missing($allianceResources);
        if ($missingResources->getSum() > 0) {
            return AllianceItemBuildStatus::missingResources($missingResources);
        }

        return AllianceItemBuildStatus::ok();
    }

    public function buildTechnology(User $user, AllianceWithMemberCount $alliance, AllianceTechnology $technology, ?AllianceTechnologyListItem $item, AllianceItemRequirementStatus $requirementStatus): BaseResources
    {
        $status = $this->getTechnologyBuildStatus($alliance, $technology, $item, $requirementStatus);
        if ($status->status !== AllianceItemBuildStatus::STATUS_OK) {
            throw new \RuntimeException(AllianceItemBuildStatus::STATUS_MESSAGES[$status->status]);
        }

        $level = $item !== null ? $item->getLevel() + 1 : 1;
        $costs = $technology->calculateCosts($level, $alliance->memberCount, $this->config->getFloat('alliance_membercosts_factor'));

        $startTime = time();
        $endTime = $startTime + $technology->getBuildTime() * $level;
        $this->allianceRepository->addResources($alliance, -$costs->metal, -$costs->crystal, -$costs->plastic, -$costs->fuel, -$costs->food);
        if ($level === 1) {
            $this->allianceTechnologyRepository->addToAlliance($alliance->getId(), $technology->getId(), 0, $alliance->memberCount, $startTime, $endTime);
        } else {
            $this->allianceTechnologyRepository->updateForAlliance($alliance->getId(), $technology->getId(), $level - 1, $alliance->memberCount, $startTime, $endTime);
        }

        $this->allianceHistoryRepository->addEntry($alliance, "[b]" . $user->getNick() . "[/b] hat die Forschung [b]" . $technology->getName() . " (" . $level . ")[/b] in Auftrag gegeben.");

        return $costs;
    }

    public function getBuildingBuildStatus(AllianceWithMemberCount $alliance, AllianceBuilding $building, ?AllianceBuildListItem $item, AllianceItemRequirementStatus $requirementStatus): AllianceItemBuildStatus
    {
        if (!$requirementStatus->requirementsMet($building->getId())) {
            return AllianceItemBuildStatus::missingRequirements();
        }

        $level = $item !== null ? $item->getLevel() + 1 : 1;
        if ($building->getLastLevel() <= $level) {
            return AllianceItemBuildStatus::maxLevel();
        }

        if ($item !== null && $item->getBuildEndTime() > 0) {
            return AllianceItemBuildStatus::itemUnderConstruction();
        }

        if ($requirementStatus->isUnderConstruction()) {
            return AllianceItemBuildStatus::underConstruction();
        }

        $allianceResources = $alliance->getResources();
        $costs = $this->allianceService->calculateCosts($building,$level, $alliance->memberCount, $this->config->getFloat('alliance_membercosts_factor'));

        $missingResources = $costs->missing($allianceResources);
        if ($missingResources->getSum() > 0) {
            return AllianceItemBuildStatus::missingResources($missingResources);
        }

        return AllianceItemBuildStatus::ok();
    }

    public function buildBuilding(User $user, AllianceWithMemberCount $alliance, AllianceBuilding $building, ?AllianceBuildListItem $item, AllianceItemRequirementStatus $requirementStatus): BaseResources
    {
        $status = $this->getBuildingBuildStatus($alliance, $building, $item, $requirementStatus);
        if ($status->status !== AllianceItemBuildStatus::STATUS_OK) {
            throw new \RuntimeException(AllianceItemBuildStatus::STATUS_MESSAGES[$status->status]);
        }

        $level = $item !== null ? $item->getLevel() + 1 : 1;
        $costs = $building->calculateCosts($level, $alliance->memberCount, $this->config->getFloat('alliance_membercosts_factor'));

        $startTime = time();
        $endTime = $startTime + $building->getBuildTime() * $level;
        $this->allianceRepository->addResources($alliance, -$costs->metal, -$costs->crystal, -$costs->plastic, -$costs->fuel, -$costs->food);
        if ($level === 1) {
            $this->allianceBuildingRepository->addToAlliance($alliance, $building, 0, $alliance->memberCount, $startTime, $endTime);
        } else {
            $this->allianceBuildingRepository->updateForAlliance($alliance, $building, $level - 1, $alliance->memberCount, $startTime, $endTime);
        }

        $this->allianceHistoryRepository->addEntry($alliance, "[b]" . $user->getNick() . "[/b] hat die Forschung [b]" . $building->getName() . " (" . $level . ")[/b] in Auftrag gegeben.");

        return $costs;
    }

    public function renderBuildings(array $buildings): bool|string
    {
        ob_start();


        if (count($buildings) > 0) {
            $cu = $this->security->getUser();
            $buildingList = $this->allianceBuildListRepository->findBy(['alliance'=>$cu->getData()->getAlliance()]);
            $requirementStatus = AllianceItemRequirementStatus::createForBuildings($buildings, $buildingList);
            foreach ($buildings as $building) {
                $currentBuildingListItem = $buildingList[$building->getId()] ?? null;
                $itemStatus = $this->getBuildingBuildStatus($this->allianceRepository->getAlliance($cu->getData()->getAlliance()->getId()), $building, $currentBuildingListItem, $requirementStatus);

                if ($itemStatus->status === AllianceItemBuildStatus::STATUS_MISSING_REQUIREMENTS) {
                    continue;
                }

                $level = $currentBuildingListItem !== null ? $currentBuildingListItem->level : null;
                echo '<table class="tb"><caption>'.$building->getName() . ' <span id="buildlevel">' . ($level > 0 ? $level : '') . '</span></caption>';

                echo "<tr>
            <td style=\"width:120px;background:#000;vertical-align:middle;padding:0;\">
            <img src=\"" . $building->getImagePath() . "\" style=\"width:120px;height:120px;\" alt=\"" . $building->getName() . "\"/>
            </td>
            <td style=\"vertical-align:top;height:100px;\" colspan=\"6\">
            " . $building->getLongComment() . "
            </td>
                </tr>";
                //
                // Baumenü
                //

                echo "<tr>";
                if ($itemStatus->status === AllianceItemBuildStatus::STATUS_MAX_LEVEL) {
                    echo "<td colspan=\"7\" style=\"text-align:center;\">Maximallevel erreicht!</td>";
                } else {
                    $costs = $this->allianceService->calculateCosts($building,$level + 1, $this->userRepository->count(['alliance'=>$cu->getData()->getAlliance()]), $this->config->getFloat('alliance_membercosts_factor'));
                    $style = array_fill(0, count($this->resName), '');

                    $message = '';
                    $style_message = '';
                    switch ($itemStatus->status) {
                        case AllianceItemBuildStatus::STATUS_ITEM_UNDER_CONSTRUCTION:
                            $style_message = "color: rgb(0, 255, 0);";
                            $message = Countdown::script($currentBuildingListItem->buildEndTime - time(), 'build_message_building_' . $building->getId() . '', 0, 'Wird ausgebaut auf Stufe ' . ($level + 1) . ' (TIME)');
                            break;
                        case AllianceItemBuildStatus::STATUS_UNDER_CONSTRUCTION:
                            $message = "Es wird bereits gebaut!";
                            $style_message = "color: rgb(255, 0, 0);";
                            break;
                        case AllianceItemBuildStatus::STATUS_MISSING_RESOURCE:
                            $need = $itemStatus->missingResources;
                            $message = "<input type=\"button\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Fehlende Rohstoffe einzahlen\" " . $this->tooltip->mTT("Nicht genügend Rohstoffe", "Es sind nicht genügend Rohstoffe vorhanden!<br>Klick auf den Button um die fehlenden Rohstoffe einzuzahlen.") . " onclick=\"setSpends(" . $need->metal . ", " . $need->crystal . ", " . $need->plastic . ", " . $need->fuel . ", " . $need->food . ");\"/>";
                            foreach ($this->resName as $id => $resourceName) {
                                if ($need->get($id) > 0) {
                                    $style[$id] = "style=\"color:red;\" " . $this->tooltip->mTT("Fehlender Rohstoff", "" . StringUtils::formatNumber($need->get($id)) . " " . $resourceName . "") . "";
                                }
                            }
                            break;
                        case AllianceItemBuildStatus::STATUS_OK;
                            $build_button = $level === 0 ? "Bauen" : "Ausbauen";

                            // Generiert Baubutton, mit welchem vor dem Absenden noch die Objekt ID übergeben wird
                            $message = "<input type=\"submit\" class=\"button\" name=\"building_submit\" id=\"building_submit\" value=\"" . $build_button . "\" onclick=\"document.getElementById('building_id').value=" . $building->id . ";\"/>";
                            break;
                    }

                    echo "<th>Stufe</th>
                <th>Zeit</th>
                <th>" . ResourceNames::METAL . "</th>
                <th>" . ResourceNames::CRYSTAL . "</th>
                <th>" . ResourceNames::PLASTIC . "</th>
                <th>" . ResourceNames::FUEL . "</th>
                <th>" . ResourceNames::FOOD . "</th>
            </tr><tr>
                <td>" . ($level + 1) . "</th>
                <td>" . StringUtils::formatTimespan($building->calculateBuildTime($level+ 1)) . "</th>
                <td " . $style[0] . ">" . StringUtils::formatNumber($costs->metal) . "</td>
                <td " . $style[1] . ">" . StringUtils::formatNumber($costs->crystal) . "</td>
                <td " . $style[2] . ">" . StringUtils::formatNumber($costs->plastic) . "</td>
                <td " . $style[3] . ">" . StringUtils::formatNumber($costs->fuel) . "</td>
                <td " . $style[4] . ">" . StringUtils::formatNumber($costs->food) . "</td>
            </tr>
            <tr>
                <td colspan=\"7\" style=\"text-align:center;" . $style_message . "\" id=\"build_message_building_" . $building->getId() . "\">" . $message . "</td>";
                }
                echo "</tr></table>";
            }
        }

        return ob_get_clean();
    }
}
