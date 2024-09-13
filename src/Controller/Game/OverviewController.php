<?php

namespace EtoA\Controller\Game;

use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserPropertiesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Fleet\ForeignFleetLoader;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologyListItemSearch;
use EtoA\Technology\TechnologyId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Fleet\FleetStatus;
use EtoA\Fleet\FleetAction;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\User\UserLoginFailureRepository;
use EtoA\Universe\Resources\ResourceNames;

class OverviewController extends AbstractGameController
{
    public function __construct(
        private readonly UserPropertiesRepository     $userPropertiesRepository,
        private readonly PlanetRepository             $planetRepository,
        private readonly FleetRepository              $fleetRepository,
        private readonly TextRepository               $textRepo,
        private readonly UserLoginFailureRepository   $userLoginFailureRepository,
        private readonly AllianceNewsRepository       $allianceNewsRepository,
        private readonly ForeignFleetLoader           $foreignFleetLoader,
        private readonly TechnologyDataRepository     $technologyDataRepository,
        private readonly TechnologyRepository         $technologyRepository,
        private readonly AllianceBuildingRepository   $allianceBuildingRepository,
        private readonly AllianceTechnologyRepository $allianceTechnologyRepository,
        private readonly BuildingDataRepository       $buildingDataRepository,
        private readonly ShipDataRepository           $shipDataRepository,
        private readonly DefenseDataRepository        $defenseDataRepository,
        private readonly BuildingRepository           $buildingRepository,
        private readonly ShipQueueRepository          $shipQueueRepository,
        private readonly DefenseQueueRepository       $defenseQueueRepository,
    )
    {
    }

    #[Route('/game/overview', name: 'game.overview')]
    public function overview(Request $request): Response
    {

        $s = $request->getSession();

        $failureCount = $this->userLoginFailureRepository->countLoginFailuresSince($this->getUser()->getId(), $this->getUser()->getData()->getLastOnline());

        // Admin-Infos
        $infoText = $this->textRepo->find('info');
        // Rathaus
        $newsCounts = $this->allianceNewsRepository->countNewEntriesSince($this->getUser()->getData()->getAllianceId(), $this->getUser()->getData()->getLastOnline());

        // Eigene Flotten
        $ownFleets = $this->fleetRepository->count(FleetSearch::create()->user($this->getUser()->getId()));

        // Fremde Flotten
        $result = $this->foreignFleetLoader->getVisibleFleets($this->getUser()->getId());

        // Technologien
        $technologyNames = $this->technologyDataRepository->getTechnologyNames(true);

        //Lädt forschende Tech
        $technologyInProgress = $this->technologyRepository->searchEntry(TechnologyListItemSearch::create()->userId($this->getUser()->getId())->notTechnologyId(TechnologyId::GEN)->underConstruction());

        //
        //Gentech
        //
        $genTechnologyInProgress = $this->technologyRepository->searchEntry(TechnologyListItemSearch::create()->userId($this->getUser()->getId())->technologyId(TechnologyId::GEN)->underConstruction());

        // Allianzegebäude
        if ($this->getUser()->getData()->getAllianceId() != 0) {

            // Lädt bauende Allianzgebäude
            $allianceBuildingInProgress = $this->allianceBuildingRepository->getInProgress($this->getUser()->getData()->getAllianceId());

            // Supportflotten Flotten
            $allianceSupportFleetCount = $this->fleetRepository->count(FleetSearch::create()->actionIn([\EtoA\Fleet\FleetAction::SUPPORT])->allianceId($this->getUser()->getData()->getAllianceId()));

            // Allianzangriffs
            $allianceAttackFleetCount = $this->fleetRepository->count(FleetSearch::create()->actionIn([FleetAction::ALLIANCE])->nextId($this->getUser()->getData()->getAllianceId())->status(FleetStatus::DEPARTURE)->isLeader());

            // Lädt forschende Allianztech
            $allianceTechnologyInProgress = $this->allianceTechnologyRepository->getInProgress($this->getUser()->getData()->getAllianceId());
        }

        // Planetkreis
        $properties = $this->userPropertiesRepository->getOrCreateProperties($this->getUser()->getId());
        //Kreis Definitionen
        $division = 15;            //Kreis Teilung: So hoch wie die maximale Anzahl Planeten
        $d_planets = $properties->planetCircleWidth;    //Durchmesser der Bilder (in Pixel)
        $d_infos = $properties->planetCircleWidth;        //Durchmesser der Infos (in Pixel)
        $pic_height = 75;            //Planet Bildhöhe
        $pic_width = 75;            //Planet Bildbreite
        $info_box_height = 50;    //Info Höhe
        $info_box_width = 150;    //Info Breite
        $degree = 0;                //Winkel des Startplanetes (0=Senkrecht (Oben))

        $middle_left = $d_planets / 2 - $pic_height / 2;
        $middle_top = $d_planets / 2 - $pic_width / 2;
        $absolute_width = $d_infos + $info_box_width + $pic_width;
        $absolute_height = $d_infos + $info_box_height + $pic_height;

        //Liest alle Planeten des Besitzers aus und gibt benötigte infos
        $userPlanets = $this->planetRepository->getUserPlanets($this->getUser()->getId());
        $buildingNames = $this->buildingDataRepository->getBuildingNames(true);
        $shipNames = $this->shipDataRepository->getShipNames(true);
        $defenseNames = $this->defenseDataRepository->getDefenseNames(true);

        $shipyard_rest_time = [];
        $shipyard_name = [];
        $shipyard_zeit = [];
        $shipyard_time = [];
        $defense_rest_time = [];
        $defense_name = [];
        $defense_zeit = [];
        $defense_time = [];

        $planets = '';
        foreach ($userPlanets as $userPlanet) {
            // Bauhof infos
            $buildingEntries = $this->buildingRepository->findForUser($this->getUser()->getId(), $userPlanet->id, time());
            if (count($buildingEntries) > 0) {
                $entry = $buildingEntries[0];

                //infos über den Bauhof
                $building_rest_time = $entry->endTime - time();
                $building_h = floor($building_rest_time / 3600);
                $building_m = floor(($building_rest_time - $building_h * 3600) / 60);
                $building_s = $building_rest_time - $building_h * 3600 - $building_m * 60;
                $building_zeit = "(" . $building_h . "h " . $building_m . "m " . $building_s . "s)";

                $building_time = $building_zeit;
                $building_name = $buildingNames[$entry->buildingId];

                // Zeigt Ausbaulevel bei Abriss
                if ($entry->buildType == 4) {
                    $building_level = $entry->currentLevel - 1;
                } // Bei Ausbau
                else {
                    $building_level = $entry->currentLevel + 1;
                }

                if ($building_rest_time <= 0) {
                    $building_time = "Fertig";
                }
            } else {
                $building_time = "";
                $building_rest_time = "";
                $building_name = "";
                $building_level = "";
            }


            // Schiffswerft infos
            $queueEntries = $this->shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->entityId($userPlanet->id)->endAfter(time()), 1);
            if (count($queueEntries) > 0) {
                $queueItem = $queueEntries[0];

                //Verbleibende Zeit bis zur fertigstellung des aktuellen Auftrages
                $shipyard_rest_time[$userPlanet->id] = $queueItem->endTime - time();
                //Schiffsname
                $shipyard_name[$userPlanet->id] = $shipNames[$queueItem->shipId];

                //infos über den raumschiffswerft
                $shipyard_h = floor($shipyard_rest_time[$userPlanet->id] / 3600);
                $shipyard_m = floor(($shipyard_rest_time[$userPlanet->id] - $shipyard_h * 3600) / 60);
                $shipyard_s = $shipyard_rest_time[$userPlanet->id] - $shipyard_h * 3600 - $shipyard_m * 60;
                $shipyard_zeit[$userPlanet->id] = "(" . $shipyard_h . "h " . $shipyard_m . "m " . $shipyard_s . "s)";

                $shipyard_time[$userPlanet->id] = $shipyard_zeit[$userPlanet->id];
                if ($shipyard_rest_time[$userPlanet->id] <= 0) {
                    $shipyard_time[$userPlanet->id] = "Fertig";
                }
            } else {
                $shipyard_time[$userPlanet->id] = "";
                $shipyard_name[$userPlanet->id] = "";
            }

            // waffenfabrik infos
            $queueEntries = $this->defenseQueueRepository->searchQueueItems(DefenseQueueSearch::create()->entityId($userPlanet->id)->endAfter(time()), 1);
            if (count($queueEntries) > 0) {
                $queueItem = $queueEntries[0];

                //Verbleibende Zeit bis zur fertigstellung des aktuellen Auftrages
                $defense_rest_time[$userPlanet->id] = $queueItem->endTime - time();
                //Defname
                $defense_name[$userPlanet->id] = $defenseNames[$queueItem->defenseId];

                // Infos über die Waffenfabrik
                $defense_h = floor($defense_rest_time[$userPlanet->id] / 3600);
                $defense_m = floor(($defense_rest_time[$userPlanet->id] - $defense_h * 3600) / 60);
                $defense_s = $defense_rest_time[$userPlanet->id] - $defense_h * 3600 - $defense_m * 60;
                $defense_zeit[$userPlanet->id] = "(" . $defense_h . "h " . $defense_m . "m " . $defense_s . "s)";

                $defense_time[$userPlanet->id] = $defense_zeit[$userPlanet->id];
                if ($defense_rest_time[$userPlanet->id] <= 0) {
                    $defense_time[$userPlanet->id] = "Fertig";
                }
            } else {
                $defense_time[$userPlanet->id] = "";
                $defense_name[$userPlanet->id] = "";
            }

            $planet_info = "<b class=\"planet_name\">" . StringUtils::encodeDBStringToPlaintext($userPlanet->displayName()) . "</b><br>" . $building_name . " " . $building_level;
            $planet_image_path = $userPlanet->getImagePath('medium');

            // Planet bild mit link zum bauhof und der informationen übergabe beim mouseover
            $planet_link = "<a href=\"?page=buildings&change_entity=" . $userPlanet->id . "\"><img id=\"Planet\" src=\"" . $planet_image_path . "\" width=\"" . $pic_width . "\" height=\"" . $pic_height . "\"
        onMouseOver=\"show_info(
            '" . $userPlanet->id . "',
            '" . StringUtils::encodeDBStringToJS($userPlanet->displayName()) . "',
            '" . $building_name . "',
            '" . $building_time . "',
            '" . $shipyard_name[$userPlanet->id] . "',
            '" . $shipyard_time[$userPlanet->id] . "',
            '" . $defense_name[$userPlanet->id] . "',
            '" . $defense_time[$userPlanet->id] . "',
            '" . floor($userPlanet->people) . "',
            '" . floor($userPlanet->resMetal) . "',
            '" . floor($userPlanet->resCrystal) . "',
            '" . floor($userPlanet->resPlastic) . "',
            '" . floor($userPlanet->resFuel) . "',
            '" . floor($userPlanet->resFood) . "',
            '" . floor($userPlanet->usePower) . "',
            '" . floor($userPlanet->prodPower) . "',
            '" . floor($userPlanet->storeMetal) . "',
            '" . floor($userPlanet->storeCrystal) . "',
            '" . floor($userPlanet->storePlastic) . "',
            '" . floor($userPlanet->storeFuel) . "',
            '" . floor($userPlanet->storeFood) . "',
            '" . floor($userPlanet->peoplePlace) . "'
            );\"/></a>";

            if ($degree == 0)
                $text = "center";
            elseif ($degree > 0 && $degree <= 180)
                $text = "left";
            else
                $text = "right";

            $left2 = $middle_left + (($d_planets / 2) * cos(deg2rad($degree + 270)));
            $top2 = $middle_top + (($d_planets / 2) * sin(deg2rad($degree + 270)));

            $planets .= "<div style=\"position:absolute; left:" . $left2 . "px; top:" . $top2 . "px; text-align:center; vertical-align:middle;\">" . $planet_link . "</div>";

            if ($degree == 0) {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - ($info_box_width - $pic_width) / 2;
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) - $info_box_height;
            } elseif ($degree > 0 && $degree <= 45) {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) + $pic_width;
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) - $pic_height / 2;
            } elseif ($degree > 45 && $degree < 135) {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) + $pic_width;
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270)));
            } elseif ($degree >= 135 && $degree < 160) {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) + $pic_width;
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) + $pic_height / 2;
            } elseif ($degree >= 160 && $degree < 180) {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) + 15;
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) + $pic_height;
            } elseif ($degree >= 180 && $degree <= 210) {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - ($info_box_width + 15 - $pic_width);
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) + $pic_height;
            } elseif ($degree > 210 && $degree <= 225) {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - $pic_width - ($info_box_width - $pic_width);
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) + $pic_height / 2;
            } elseif ($degree > 225 && $degree < 315) {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - $pic_width - ($info_box_width - $pic_width);
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270)));
            } else //315<$degree<360
            {
                $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - $pic_width - ($info_box_width - $pic_width);
                $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) - $pic_height / 2;
            }

            $planets .= "<div id=\"planet_info_" . $userPlanet->id . "\" style=\"position:absolute; left:" . $left . "px; top:" . $top . "px; width:" . $info_box_width . "px; height:" . $info_box_height . "px; text-align:" . $text . "; vertical-align:middle;\">";

            $planets .= $planet_info;
            $planets .= '<span id="planet_timer_' . $userPlanet->id . '">';

            // Stellt Zeit Counter dar, wenn ein Gebäude in bau ist
            if ($building_rest_time > 0) {
                $planets .= startTime($building_rest_time, "planet_timer_" . $userPlanet->id . "", 0, "<br>(TIME)") . "";
            }

            $planets .= "</span></div>";
            $degree = $degree + (360 / $division);
        }

        return $this->render('game/overview/overview.html.twig', [
            'failureCount' => $failureCount ?? null,
            'infotext' => $infoText,
            'newsCounts' => $newsCounts,
            'ownFleets' => $ownFleets,
            'result' => $result,
            'technologyInProgress' => $technologyInProgress,
            'genTechnologyInProgress' => $genTechnologyInProgress,
            'allianceBuildingInProgress' => $allianceBuildingInProgress,
            'allianceSupportFleetCount' => $allianceSupportFleetCount,
            'allianceAttackFleetCount' => $allianceAttackFleetCount,
            'allianceTechnologyInProgress' => $allianceTechnologyInProgress,
            'absolute_width' => $absolute_width,
            'absolute_height' => $absolute_height,
            'd_planets' => $d_planets,
            'technologyNames' => $technologyNames,
            'resourceNames' => ResourceNames::NAMES,
            'planets' => $planets,
            'firstView' => $s->get('isFirstView')
        ]);
    }
}