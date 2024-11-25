<?php

namespace EtoA\Universe;

use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Report;
use EtoA\Entity\Wormhole;
use EtoA\Image\ImageUtil;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResIcons;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Universe\Star\StarRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CellRenderer
{

    public function __construct(
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly ConfigurationService $config,
        private readonly PlanetRepository $planetRepo,
        private readonly AdminUserRepository $adminUserRepository,
        private readonly Security                 $security,
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly ReportRepository $reportRepository,
        private readonly UserService $userService,
        private readonly UrlGeneratorInterface $router
    )
    {
    }

    public function render(array $entities):string {
        ob_start();
        $cu = $this->security->getUser()->getData();
        $properties = $this->userPropertiesRepository->getOrCreateProperties($cu->getId());

        foreach ($entities as $ent) {
            if ($ent->getPos() == 1) {
                echo "<tr>
                        <td style=\"height:3px;background:#000;\" colspan=\"6\"></td>
                    </tr>";
            }
            $addstyle = " style=\"vertical-align:middle;";
            if (isset($_GET['hl']) && $_GET['hl'] == $ent->getId()) {
                $addstyle .= "background:#003D6F;";
            }
            $addstyle .= "\" ";

            $class = " class=\"";

            $owner = null;

            if ($ent->getCode() === EntityType::PLANET) {
                $owner = $ent->getPlanet()?->getUser();
            }


            if ($owner) {
                //Admin
                if ($this->adminUserRepository->find($owner->getId())) {
                    $class .= "adminColor";
                    $tm_info = "Admin/Entwickler";
                }
                // Krieg
                elseif ($this->allianceDiplomacyRepository->existsDiplomacyBetween($cu->getAlliance()->getId(), $owner->getAlliance()->getId(), AllianceDiplomacyLevel::WAR)) {
                    $class .= "enemyColor";
                    $tm_info = "Krieg";
                }
                // Bündniss
                elseif ($this->allianceDiplomacyRepository->existsDiplomacyBetween($cu->getAlliance()->getId(), $owner->getAlliance()->getId(), AllianceDiplomacyLevel::BND_CONFIRMED)) {
                    $class .= "friendColor";
                    $tm_info = "B&uuml;ndnis";
                }
                // Gesperrt
                elseif ($owner->getBlockedTo() > time() ) {
                    $class .= "userLockedColor";
                    $tm_info = "Gesperrt";
                }
                // Urlaub
                elseif ($owner->getHmodFrom()) {
                    $class .= "userHolidayColor";
                    $tm_info = "Urlaubsmodus";
                }
                // Lange Inaktiv
                elseif ($owner->getLastOnline() < time() - $this->config->param2Int('user_inactive_days') * 86400) {
                    $class .= "userLongInactiveColor";
                    $tm_info = "Lange Inaktiv";
                }
                // Inaktiv
                elseif ($owner->getLastOnline() < time() - $this->config->getInt('user_inactive_days') * 86400) {
                    $class .= "userInactiveColor";
                    $tm_info = "Inaktiv";
                }
                // Eigener Planet
                elseif ($cu === $owner) {
                    $class .= "userSelfColor";
                    $tm_info = "";
                }
                // Allianzmitglied
                elseif ($cu->getAllianceId() == $owner->getAllianceId() && $cu->allianceId()) {
                    $class .= "userAllianceMemberColor";
                    $tm_info = "Allianzmitglied";
                }
                // Alien/NPC
                elseif ($owner->getNpc() > 0) {
                    $class .= "alien";
                    $tm_info = "Alien";
                }
                // Noob
                elseif (!$this->userService->canAttackUser($ent)) {
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

            if ($ent->getCode() === EntityType::PLANET) {
                $planet = $ent->getPlanet();
                $planetType = $planet->getPlanetType();

                $tm = "";
                $tm .= "<b>Felder</b>: " . StringUtils::formatNumber($planet->getFields());
                $tm .= "<br/><b>Bewohnbar</b>: ";
                if ($planetType->isHabitable() == 1) $tm .= "Ja";
                else $tm .= "Nein	";
                if ($planetType->getMetal() != 1)
                    $tm .= "<br/><b>" . ResourceNames::METAL . ":</b> " . StringUtils::formatPercentString($planetType->getMetal(), true);
                if ($planetType->getCrystal() != 1)
                    $tm .= "<br/><b>" . ResourceNames::CRYSTAL . ":</b> " . StringUtils::formatPercentString($planetType->getCrystal(), true);
                if ($planetType->getPlastic() != 1)
                    $tm .= "<br/><b>" . ResourceNames::PLASTIC . ":</b> " . StringUtils::formatPercentString($planetType->getPlastic(), true);
                if ($planetType->getFuel() != 1)
                    $tm .= "<br/><b>" . ResourceNames::FUEL . ":</b> " . StringUtils::formatPercentString($planetType->getFuel(), true);
                if ($planetType->getFood() != 1)
                    $tm .= "<br/><b>" . ResourceNames::FOOD . ":</b> " . StringUtils::formatPercentString($planetType->getFood(), true);
                if ($planetType->getPower() != 1)
                    $tm .= "<br/><b>Energie:</b> " . StringUtils::formatPercentString($planetType->getPower(), true);
                if ($planetType->getPeople() != 1)
                    $tm .= "<br/><b>Bewohner:</b> " . StringUtils::formatPercentString($planetType->getPeople(), true);
                if ($planetType->getResearchTime() != 1)
                    $tm .= "<br/><b>Foschungszeit:</b> " . StringUtils::formatPercentString($planetType->getResearchTime(), true, true);
                if ($planetType->getBuildTime() != 1)
                    $tm .= "<br/><b>Bauzeit:</b> " . StringUtils::formatPercentString($planetType->getBuildTime(), true, true);
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
                        <a href=\"" . $this->router->generate('game.entity',['id'=>$ent->getId()]) . "\">
                            <img src=\"" . $ent->getType()->getImagePath() . "\" alt=\"icon\" />
                        </a>
                    </td>
                    <td $class style=\"text-align:center;vertical-align:middle;background:#000\"><b>" . $ent->getPos() . "</b></td>
                    <td $class $addstyle >";
            if ($ent->getCode() === EntityType::PLANET)
                echo "<span " . tm($planetType->getName(), $tm) . ">" . $planetType->getName() . "</span>";
            else
                echo $ent->getType()->getEntityCodeString();

            if ($ent->getCode() == EntityType::WORMHOLE) {
                if ($ent->isPersistent()) {
                    echo " [stabil]";
                } else {
                    echo " [veränderlich]";
                }
                $tent = $ent->getWormhole();
                echo "<br/>Ziel: <a href=\"?page=cell&amp;id=" . $tent->getCellId() . "\">" . $tent . "</a>";
            } elseif ($ent->getCode() == EntityType::PLANET) {
                $planet = $this->planetRepo->find($ent->getId());
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
                    <td $addstyle><a $class href=\"" . $this->router->generate('game.entity',['id'=>$ent->getId()]) . "\">" . BBCodeUtils::toHTML($ent->displayName()) . "</a></td>
                    <td $addstyle>";
            if ($owner) {
                $header = $owner->getNick();
                $tm = "Punkte: " . StringUtils::formatNumber($owner->getPoints()) . "<br style=\"clear:both\" />";
                if ($owner->getAlliance()->getId() > 0) {
                    $tm .= "Allianz: " . $owner->getAlliance()->toString() . "<br style=\"clear:both\" />";
                }

                if ($tm_info != "")
                    $header .= " (<span $class>" . $tm_info . "</span>)";
                echo "<span style=\"color:#817339;font-weight:bold\" " . tm($header, $tm) . "><a $class href=\"?page=userinfo&amp;id=" . $owner->getId() . "\">" . $owner->getNick() . "</a></span> ";
            } else
                echo 'Niemand';
            echo "</td>
                    <td $addstyle>";

            // Favorit
            if ($cu != $owner) {
                echo "<a href=\"?page=bookmarks&amp;add=" . $ent->getId() . "\" title=\"Zu den Favoriten hinzuf&uuml;gen\">" . ImageUtil::icon("favorite") . "</a> ";
            }

            // Flotte
            if ($ent->getCode() == EntityType::PLANET || $ent->getCode() == EntityType::ASTEROID || $ent->getCode() == EntityType::WORMHOLE || $ent->getCode() == EntityType::NEBULA || $ent->getCode() == EntityType::EMPTY_SPACE) {
                echo "<a href=\"?page=haven&amp;target=" . $ent->getId() . "\" title=\"Flotte hinschicken\">" . ImageUtil::icon('fleet') . "</a> ";
            }


            if ($ent->getCode() == EntityType::PLANET) {
                // Nachrichten-Link
                if ($owner  && $cu != $owner) {
                    echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=" . $owner->getId() . "\" title=\"Nachricht senden\">" . ImageUtil::icon("mail") . "</a> ";
                }

                // Diverse Links
                if ($cu != $owner) {
                    // Besiedelte Planete
                    if ($owner) {
                        echo "<a href=\"javascript:;\" onclick=\"xajax_launchSypProbe(" . $ent->getId() . ");\" title=\"Ausspionieren\">" . ImageUtil::icon("spy") . "</a>";
                        echo "<a href=\"?page=missiles&amp;target=" . $ent->getId() . "\" title=\"Raketenangriff starten\">" . ImageUtil::icon("missile") . "</a> ";
                        echo "<a href=\"?page=crypto&amp;target=" . $ent->getId() . "\" title=\"Flottenbewegungen analysieren\">" . ImageUtil::icon("crypto") . "</a> ";
                    }
                }
            }

            if (in_array("analyze", $ent->getType()->getAllowedFleetActions(), true)) {
                if ($properties->isShowCellreports()) {
                    $report = $this->reportRepository->searchReport(ReportSearch::create()->userId($cu->id)->type('spy')->entity1Id($ent->id()));
                    if ($report !== null) {
                        $r = Report::createFactory($report);
                        echo "<span " . tm($r->subject, $r . "<br style=\"clear:both\" />") . "><a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->id() . ");\" title=\"Analysieren\">" . ImageUtil::icon("spy") . "</a></span>";
                    } else
                        echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->getId() .");\" title=\"Analysieren\">" . ImageUtil::icon("spy") . "</a> ";
                } else
                    echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->getId() . ");\" title=\"Analysieren\">" . ImageUtil::icon("spy") . "</a> ";
            }


            echo "</td></tr>";
        }

        return ob_get_clean();
    }
}