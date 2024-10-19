<?php

namespace EtoA\Universe;

use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Report;
use EtoA\Image\ImageUtil;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Resources\ResIcons;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\Wormhole;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CellRenderer
{

    public function __construct(
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly ConfigurationService $config,
        private readonly PlanetRepository $planetRepo,
        private readonly AllianceRepository $allianceRepository,
        private readonly AdminUserRepository $adminUserRepository,
        private readonly Security                 $security,
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly ReportRepository $reportRepository,
        private readonly UserRepository $userRepository,
        private readonly StarRepository $starRepository,
        private readonly PlanetTypeRepository $planetTypeRepository,
        private readonly EntityService $entityService
    )
    {
    }

    public function render($entities):string {
        ob_start();
        $admins = $this->adminUserRepository->getAdminPlayerIds();
        $hasPlanetInSystem = false;
        $starNameEmpty = false;
        $cu = $this->security->getUser()->getData();
        $properties = $this->userPropertiesRepository->getOrCreateProperties($cu->getId());

        foreach ($entities as $ent) {
            $fullEnt = $this->entityService->getEntity($ent);
            if ($ent->pos == 1) {
                echo "<tr>
                        <td style=\"height:3px;background:#000;\" colspan=\"6\"></td>
                    </tr>";
            }
            $addstyle = " style=\"vertical-align:middle;";
            if (isset($_GET['hl']) && $_GET['hl'] == $ent->id()) {
                $addstyle .= "background:#003D6F;";
            }
            $addstyle .= "\" ";

            $class = " class=\"";
            $ownerId = $this->planetRepo->getPlanetUserId($ent->id);

            if ($ownerId) {
                //Admin
                if (in_array((int) $ownerId, $admins, true)) {
                    $class .= "adminColor";
                    $tm_info = "Admin/Entwickler";
                }
                // Krieg
                elseif ($this->allianceDiplomacyRepository->existsDiplomacyBetween($cu->getAllianceId(), $this->userRepository->getUser($ownerId)->getAllianceId(), AllianceDiplomacyLevel::WAR)) {
                    $class .= "enemyColor";
                    $tm_info = "Krieg";
                }
                // Bündniss
                elseif ($this->allianceDiplomacyRepository->existsDiplomacyBetween($cu->getAllianceId(), $this->userRepository->getUser($ownerId)->getAllianceId(), AllianceDiplomacyLevel::BND_CONFIRMED)) {
                    $class .= "friendColor";
                    $tm_info = "B&uuml;ndnis";
                }
                // Gesperrt
                elseif ($this->userRepository->getUser($ownerId)->getBlockedTo() > time() ) {
                    $class .= "userLockedColor";
                    $tm_info = "Gesperrt";
                }
                // Urlaub
                elseif ($this->userRepository->getUser($ownerId)->getHmodFrom()) {
                    $class .= "userHolidayColor";
                    $tm_info = "Urlaubsmodus";
                }
                // Lange Inaktiv
                elseif ($this->userRepository->getUser($ownerId)->getLastOnline() < time() - $this->config->param2Int('user_inactive_days') * 86400) {
                    $class .= "userLongInactiveColor";
                    $tm_info = "Lange Inaktiv";
                }
                // Inaktiv
                elseif ($this->userRepository->getUser($ownerId)->getLastOnline() < time() - $this->config->getInt('user_inactive_days') * 86400) {
                    $class .= "userInactiveColor";
                    $tm_info = "Inaktiv";
                }
                // Eigener Planet
                elseif ($cu->id == $ownerId) {
                    $class .= "userSelfColor";
                    $tm_info = "";
                }
                // Allianzmitglied
                elseif ($cu->allianceId() == $this->userRepository->getUser($ownerId)->getAllianceId() && $cu->allianceId()) {
                    $class .= "userAllianceMemberColor";
                    $tm_info = "Allianzmitglied";
                }
                // Alien/NPC
                elseif ($this->userRepository->getUser($ownerId)->getNpc() > 0) {
                    $class .= "alien";
                    $tm_info = "Alien";
                }
                // Noob
                elseif (!$cu->canAttackPlanet($ent)) {
                    $class .= "noobColor";
                    $tm_info = "Anf&auml;ngerschutz";
                } else {
                    $class .= "";
                    $tm_info = "";
                }
            } else {
                $class .= "";
                $tm_info = "";
            }
            $class .= "\" ";

            if ($ent->code === EntityType::PLANET) {
                $planet = $this->planetRepo->find($ent->id);
                $planetType = $this->planetTypeRepository->get($planet->typeId);

                $tm = "";
                $tm .= "<b>Felder</b>: " . StringUtils::formatNumber($planet->fields);
                $tm .= "<br/><b>Bewohnbar</b>: ";
                if ($planetType->habitable == 1) $tm .= "Ja";
                else $tm .= "Nein	";
                if ($planetType->metal != 1)
                    $tm .= "<br/><b>" . ResourceNames::METAL . ":</b> " . StringUtils::formatPercentString($planetType->metal, true);
                if ($planetType->crystal != 1)
                    $tm .= "<br/><b>" . ResourceNames::CRYSTAL . ":</b> " . StringUtils::formatPercentString($planetType->crystal, true);
                if ($planetType->plastic != 1)
                    $tm .= "<br/><b>" . ResourceNames::PLASTIC . ":</b> " . StringUtils::formatPercentString($planetType->plastic, true);
                if ($planetType->fuel != 1)
                    $tm .= "<br/><b>" . ResourceNames::FUEL . ":</b> " . StringUtils::formatPercentString($planetType->fuel, true);
                if ($planetType->food != 1)
                    $tm .= "<br/><b>" . ResourceNames::FOOD . ":</b> " . StringUtils::formatPercentString($planetType->food, true);
                if ($planetType->power != 1)
                    $tm .= "<br/><b>Energie:</b> " . StringUtils::formatPercentString($planetType->power, true);
                if ($planetType->people != 1)
                    $tm .= "<br/><b>Bewohner:</b> " . StringUtils::formatPercentString($planetType->people, true);
                if ($planetType->researchTime != 1)
                    $tm .= "<br/><b>Foschungszeit:</b> " . StringUtils::formatPercentString($planetType->researchTime, true, true);
                if ($planetType->buildTime != 1)
                    $tm .= "<br/><b>Bauzeit:</b> " . StringUtils::formatPercentString($planetType->buildTime, true, true);
                $tm .= "<br /><br/><b>Wärmebonus</b>: ";
                $solarProdBonus = $planet->solarPowerBonus();
                $color = $solarProdBonus >= 0 ? '#0f0' : '#f00';
                $tm .= "<span style=\"color:" . $color . "\">" . ($solarProdBonus > 0 ? '+' : '') . $solarProdBonus . "</span>";
                $tm .= " Energie pro Solarsatellit";
                $tm .= "<br /><b>Kältebonus</b>: ";
                $fuelProdBonus = $planet->fuelProductionBonus();
                $color = $fuelProdBonus >= 0 ? '#0f0' : '#f00';
                $tm .= "<span style=\"color:" . $color . "\">" . ($fuelProdBonus > 0 ? '+' : '') . $fuelProdBonus . "%</span>";
                $tm .= " " . ResourceNames::FUEL . "-Produktion";
            }

            echo "<tr>
                    <td $class style=\"width:40px;background:#000;\">
                        <a href=\"?page=entity&amp;id=" . $ent->id . "\">
                            <img src=\"" . $fullEnt->getImagePath() . "\" alt=\"icon\" />
                        </a>
                    </td>
                    <td $class style=\"text-align:center;vertical-align:middle;background:#000\"><b>" . $ent->pos . "</b></td>
                    <td $class $addstyle >";
            if ($ent->code === EntityType::PLANET)
                echo "<span " . tm($planetType->name, $tm) . ">" . $planetType->name . "</span>";
            else
                echo $fullEnt->getEntityCodeString();

            if ($ent->code == EntityType::WORMHOLE) {
                if ($ent->isPersistent()) {
                    echo " [stabil]";
                } else {
                    echo " [veränderlich]";
                }
                $tent = new Wormhole($ent->targetId());
                echo "<br/>Ziel: <a href=\"?page=cell&amp;id=" . $tent->cellId() . "\">" . $tent . "</a>";
            } elseif ($ent->code == EntityType::PLANET) {
                $planet = $this->planetRepo->find($ent->id);
                if ($planet->hasDebrisField()) {
                    echo "<br/><span style=\"color:#817339;font-weight:bold\" " . tm(
                            "Trümmerfeld",
                            ResIcons::METAL . StringUtils::formatNumber($planet->wfMetal) . " " .
                            ResourceNames::METAL . "<br style=\"clear:both\" />" .
                            ResIcons::CRYSTAL . StringUtils::formatNumber($planet->wfCrystal) . " " .
                            ResourceNames::CRYSTAL . "<br style=\"clear:both\" />" .
                            ResIcons::PLASTIC . StringUtils::formatNumber($planet->wfPlastic) . " " .
                            ResourceNames::PLASTIC . "<br style=\"clear:both\" />"
                        ) . ">Trümmerfeld</span> ";
                }
            }
            echo "</td>
                    <td $addstyle><a $class href=\"?page=entity&amp;id=" . $ent->id . "\">" . BBCodeUtils::toHTML($ent->toString()) . "</a></td>
                    <td $addstyle>";
            if ($ownerId) {
                $header = $this->userRepository->getUser($ownerId)->getNick();
                $tm = "Punkte: " . StringUtils::formatNumber($this->userRepository->getUser($ownerId)->getPoints()) . "<br style=\"clear:both\" />";
                if ($this->userRepository->getUser($ownerId)->getAllianceId() > 0) {
                    $ownerAlliance = $this->allianceRepository->getAlliance($this->userRepository->getUser($ownerId)->getAllianceId());
                    $tm .= "Allianz: " . $ownerAlliance->nameWithTag . "<br style=\"clear:both\" />";
                }

                if ($tm_info != "")
                    $header .= " (<span $class>" . $tm_info . "</span>)";
                echo "<span style=\"color:#817339;font-weight:bold\" " . tm($header, $tm) . "><a $class href=\"?page=userinfo&amp;id=" . $this->userRepository->getUser($ownerId)->getId() . "\">" . $this->userRepository->getUser($ownerId)->getNick() . "</a></span> ";
            } else
                echo $this->userRepository->getUser($ownerId)?$this->userRepository->getUser($ownerId)->getNick():'Niemand';
            echo "</td>
                    <td $addstyle>";

            // Favorit
            if ($cu->getId() != $ownerId) {
                echo "<a href=\"?page=bookmarks&amp;add=" . $ent->id . "\" title=\"Zu den Favoriten hinzuf&uuml;gen\">" . ImageUtil::icon("favorite") . "</a> ";
            }

            // Flotte
            if ($ent->code == EntityType::PLANET || $ent->code == EntityType::ASTEROID || $ent->code == EntityType::WORMHOLE || $ent->code == EntityType::NEBULA || $ent->code == EntityType::EMPTY_SPACE) {
                echo "<a href=\"?page=haven&amp;target=" . $ent->id . "\" title=\"Flotte hinschicken\">" . ImageUtil::icon('fleet') . "</a> ";
            }


            if ($ent->code == EntityType::STAR) {
                if ($this->starRepository->find($ent->id)->name) {
                    $starNameEmpty = true;
                    $starToBeNamed = $ent->id();
                }
            } elseif ($ent->code == EntityType::PLANET) {
                if ($ownerId > 0 && $cu->getId() == $ownerId) {
                    $hasPlanetInSystem = true;
                }

                // Nachrichten-Link
                if ($ownerId > 0 && $cu->getId() != $ownerId) {
                    echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=" . $ownerId . "\" title=\"Nachricht senden\">" . ImageUtil::icon("mail") . "</a> ";
                }

                // Diverse Links
                if ($cu->getId() != $ownerId) {
                    // Besiedelte Planete
                    if ($ownerId > 0) {
                        echo "<a href=\"javascript:;\" onclick=\"xajax_launchSypProbe(" . $ent->id . ");\" title=\"Ausspionieren\">" . ImageUtil::icon("spy") . "</a>";
                        echo "<a href=\"?page=missiles&amp;target=" . $ent->id . "\" title=\"Raketenangriff starten\">" . ImageUtil::icon("missile") . "</a> ";
                        echo "<a href=\"?page=crypto&amp;target=" . $ent->id . "\" title=\"Flottenbewegungen analysieren\">" . ImageUtil::icon("crypto") . "</a> ";
                    }
                }
            }

            if (in_array("analyze", $fullEnt->getAllowedFleetActions(), true)) {
                if ($properties->showCellreports) {
                    $report = $this->reportRepository->searchReport(ReportSearch::create()->userId($cu->id)->type('spy')->entity1Id($ent->id()));
                    if ($report !== null) {
                        $r = Report::createFactory($report);
                        echo "<span " . tm($r->subject, $r . "<br style=\"clear:both\" />") . "><a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->id() . ");\" title=\"Analysieren\">" . ImageUtil::icon("spy") . "</a></span>";
                    } else
                        echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->id .");\" title=\"Analysieren\">" . ImageUtil::icon("spy") . "</a> ";
                } else
                    echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->id . ");\" title=\"Analysieren\">" . ImageUtil::icon("spy") . "</a> ";
            }


            echo "</td></tr>";
        }

        return ob_get_clean();
    }
}