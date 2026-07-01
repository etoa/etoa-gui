<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingCostCalculator;
use EtoA\Building\BuildingCostContext;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingTypeDataRepository;
use EtoA\Building\BuildingTypeId;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseCategoryRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Entity\Building;
use EtoA\Entity\Defense;
use EtoA\Entity\Missile;
use EtoA\Entity\Planet;
use EtoA\Entity\Race;
use EtoA\Entity\Ship;
use EtoA\Entity\Technology;
use EtoA\Fleet\FleetAction;
use EtoA\Missile\MissileDataRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipCategoryRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\ExternalUrl;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyService;
use EtoA\Technology\TechnologyTypeRepository;
use EtoA\UI\Tooltip;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Universe\Star\SolarTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractGameController
{
    public function __construct(
        private readonly ShipDataRepository         $shipDataRepository,
        private readonly BuildingTypeDataRepository $buildingTypeDataRepository,
        private readonly BuildingDataRepository     $buildingDataRepository,
        private readonly ConfigurationService       $configurationService,
        private readonly BuildingCostCalculator     $buildingCostCalculator,
        private readonly BuildingCostContext        $buildingCostContext,
        private readonly DefenseCategoryRepository  $defenseCategoryRepository,
        private readonly DefenseDataRepository      $defenseDataRepository,
        private readonly RuntimeDataStore           $runtimeDataStore,
        private readonly PlanetTypeRepository       $planetTypeRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly ShipListRepository         $shipListRepository,
        private readonly RaceDataRepository         $raceDataRepository,
        private readonly TechnologyTypeRepository   $technologyTypeRepository,
        private readonly TechnologyDataRepository   $technologyDataRepository,
        private readonly ShipRequirementRepository  $shipRequirementRepository,
        private readonly TechnologyService          $technologyService,
        private readonly ShipCategoryRepository     $shipCategoryRepository,
        private readonly SpecialistDataRepository   $specialistDataRepository,
        private readonly SolarTypeRepository        $solarTypeRepository,
        private readonly MissileDataRepository      $missileDataRepository
    )
    {
    }

    #[Route('/game/help/overview', name: 'game.help.overview')]
    public function list(): Response
    {
        // Internal pages
        $links = [
            [
                'label' => 'Supportticket eröffnen',
                'url' => 'game.ticket.list'
            ],
            [
                'label' => 'Admin via Mail kontaktieren',
                'url' => 'game.contact.list'
            ],
            [
                'label' => 'Changelog',
                'url' => 'game.changelog'
            ],
            [
                'label' => 'Credits',
                'url' => 'game.credits'
            ]
        ];

        // External resources
        $externalLinks = [
            [
                'label' => 'Häufig gestellte Fragen',
                'onclick' => ExternalUrl::HELP_CENTER_ON_CLICK,
            ],
            [
                'label' => 'Regeln',
                'onclick' => ExternalUrl::RULES_ON_CLICK,
            ],
            [
                'label' => 'Forum',
                'url' => ExternalUrl::FORUM,
            ],
            [
                'label' => 'Fehler melden',
                'url' => ExternalUrl::DEV_CENTER,
            ]
        ];

        $helpNav = [
            "Datenbank" => [
                "Rassen" => array('races', 'Liste aller Rassen'),
                "Planeten" => array('planets', 'Liste aller Planeten'),
                "Sterne" => array('stars', 'Liste aller Sterne'),
                "Gebäude" => array('buildings', 'Liste aller Gebäude'),
                "Technologien" => array('research', 'Liste aller Technologien'),
                "Schiffe" => array('shipyard', 'Liste aller Schiffe'),
                "Raketen" => array('missiles', 'Liste aller Raketen'),
                "Verteidigung" => array('defense', 'Liste aller Verteidigungsanlagen'),
                "Spezialisten" => array('specialists', 'Was man mit Spezialisten machen kann'),
            ],
            "Spielmechanismen" => [
                "Einstellungen" => array('settings', 'Grundlegende Einstellungen dieser Runde'),
                "Ressourcen" => array('resources', 'Liste aller Ressourcen'),
                "Markt" => array('market', 'Wie der Marktplatz funktioniert?'),
                "Rohstoffkurse" => array('rates', 'Welche Werte die Rohstoffe aktuell haben'),
                "Bewohner" => array('population', 'Wie arbeite ich mit Bewohnern und was muss ich beachten?'),
                "Energie" => array('power', 'Alles über die Energieproduktion'),
                "Schiffsaktionen" => array('action', 'Die verschiedenen Aktionen in der Übersicht'),
                "Kryptocenter" => array('crypto', 'Wie man fremde Flottenbewegungen scannt?'),
                "Multis und Sitting" => array('multi_sitting', 'Wie wir Mehrfachaccounts handhaben und wie Sitting funktioniert?'),
                "Raketen" => array('missile_system', 'Wie das Raketensystem funktioniert?'),
                "Raumkarte" => array('space', 'Wie ist das Universum aufgebaut?'),
                "Spezialpunkte" => array('specialpoints', 'Wie man Spezialpunkte und Titel erwerben kann?'),
                "Spionage" => array('spy', 'Wie das Spionagesystem funktioniert?'),
                "Statistik" => array('stats', 'Was sind Statistiken und wie werden sie berechnet?'),
                "Technikbaum" => array('techtree', 'Wie lese ich daraus die Voraussetzungen ab?'),
                "Textformatierung" => array('textformat', 'Wie man Text formatieren kann (BBcode)?'),
                "Urlaubsmodus" => array('u_mod', 'Was das ist und wie es funktioniert?'),
                "Wärme- und Kältebonus" => array('tempbonus', 'Welche Auswirkungen hat die Planetentemperatur?')
            ]
        ];

        return $this->render('game/help/overview.html.twig', [
            'links' => $links,
            'externalLinks' => $externalLinks,
            'helpNav' => $helpNav
        ]);
    }

    #[Route('/game/help/action', name: 'game.help.action')]
    public function action(): Response
    {
        $attitudes = array();

        $actions = FleetAction::getAll();
        foreach ($actions as $data) {
            $attitudes[$data->attitude()][] = $data;
        }
        ksort($attitudes);

        return $this->render('game/help/info/action.html.twig', [
            'attitudes' => $attitudes,
            'attitudeColor' => FleetAction::$attitudeColor,
            'attitudeString' => FleetAction::$attitudeString
        ]);
    }

    #[Route('/game/help/action/{code}', name: 'game.help.action.detail')]
    public function actionDetail(?string $code): Response
    {

        $action = FleetAction::createFactory($code);
        $shipNames = $action ? $this->shipDataRepository->getShipNamesWithAction($action->code()) : [];

        return $this->render('game/help/info/actionDetail.html.twig', [
            'action' => $action,
            'shipNames' => $shipNames
        ]);
    }

    #[Route('/game/help/buildings', name: 'game.help.buildings')]
    public function buildings(): Response
    {
        $buildingsWithType = [];
        foreach ($this->buildingTypeDataRepository->getTypeNames() as $buildingTypeId => $buildingTypeName) {
            $buildingsWithType[$buildingTypeId]['name'] = $buildingTypeName;
            $buildingsWithType[$buildingTypeId]['buildings'] = $this->buildingDataRepository->getBuildingsByType($buildingTypeId);
        }

        return $this->render('game/help/info/buildings.html.twig', [
            'buildingsWithType' => $buildingsWithType
        ]);
    }

    #[Route('/game/help/buildings/{building}', name: 'game.help.buildings.detail')]
    public function buildingDetail(?Building $building): Response
    {
        $infos = [];
        $infos['building'] = $building;

        // Metallmine
        if ($building->getId() === 1) {
            $infos['title'] = "Produktion von " . ResourceNames::METAL . " (ohne Boni)";

            return $this->renderProductionBuilding($building, $infos);
        } // Siliziummine
        elseif ($building->getId() === 2) {
            $infos['title'] = "Produktion von " . ResourceNames::CRYSTAL . " (ohne Boni)";

            return $this->renderProductionBuilding($building, $infos);
        } // Chemiefabrik
        elseif ($building->getId() === 3) {
            $infos['title'] = "Produktion von " . ResourceNames::PLASTIC . " (ohne Boni)";

            return $this->renderProductionBuilding($building, $infos);
        } // Tritiumsynthetizer
        elseif ($building->getId() === 4) {
            $infos['title'] = "Produktion von " . ResourceNames::FUEL . " (ohne Boni)";

            return $this->renderProductionBuilding($building, $infos);
        } // Gewächshaus
        elseif ($building->getId() === 5) {
            $infos['title'] = "Produktion von " . ResourceNames::FOOD . " (ohne Boni)";

            return $this->renderProductionBuilding($building, $infos);
        } // Planetenbasis
        elseif ($building->getId() === 6) {
            $infos['title'] = "Produktion (ohne Boni)";
            $infos['costs'] = $this->generateBuildingCosts($building);


            return $this->render('game/help/info/buildings/base.html.twig', [
                'building' => $building,
                'infos' => $infos
            ]);
        } // Wohnmodul
        elseif ($building->getId() === 7) {
            $infos['title'] = "Platz für Bewohner";
            $basePeoplePlace = $this->buildingDataRepository->getBuilding(6)->getPeoplePlace();
            for ($level = 1; $level < 31; $level++) {
                $prod_item = round($building->getPeoplePlace() * pow($building->getStoreFactor(), $level - 1));
                $levels[$level] = ['prod_item' => $prod_item, 'place' => $prod_item + $basePeoplePlace + $this->configurationService->param1Int('user_start_people')];
            }
            $infos['levels'] = $levels;
            $infos['costs'] = $this->generateBuildingCosts($building);


            return $this->render('game/help/info/buildings/people_place.html.twig', [
                'building' => $building,
                'infos' => $infos
            ]);
        }
        // Windkraftwerk
        // Solarkaftwerk
        // Fusionskraftwerk
        // Gezeitenkraftwerk
        elseif (in_array($building->getId(), [12, 13, 14, 15], true)) {
            $infos['title'] = "Energieproduktion (ohne Boni)";
            $infos['levels'] = $this->generateProductionLevels($building);
            $infos['costs'] = $this->generateBuildingCosts($building);

            return $this->render('game/help/info/buildings/energy_production.html.twig', [
                'building' => $building,
                'infos' => $infos
            ]);
        } // Titanspeicher
        elseif ($building->getId() === 16) {
            $baseStoreMetal = $this->buildingDataRepository->getBuilding(6)->getStoreMetal();
            $infos['title'] = "Lagerkapazität (inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStoreMetal) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ") des Planeten)";

            return $this->renderStorageBuilding($building, $infos);
        } // Siliziumspeicher
        elseif ($building->getId() === 17) {
            $baseStoreCrystal = $this->buildingDataRepository->getBuilding(6)->getStoreCrystal();
            $infos['title'] = "Lagerkapazität (inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStoreCrystal) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ") des Planeten)";

            return $this->renderStorageBuilding($building, $infos);
        } // Lagerhalle
        elseif ($building->getId() === 18) {
            $baseStorePlastic = $this->buildingDataRepository->getBuilding(6)->getStorePlastic();
            $infos['title'] = "Kapazität inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStorePlastic) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ")";

            return $this->renderStorageBuilding($building, $infos);
        } // Nahrungssilos
        elseif ($building->getId() === 19) {
            $baseStoreFood = $this->buildingDataRepository->getBuilding(6)->getStoreFood();
            $infos['title'] = "Lagerkapazität (inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStoreFood) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ") des Planeten)";

            return $this->renderStorageBuilding($building, $infos);
        } // Tritiumsilo
        elseif ($building->getId() === 20) {
            $baseStoreFuel = $this->buildingDataRepository->getBuilding(6)->getStoreFuel();
            $infos['title'] = "Lagerkapazität (inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStoreFuel) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ") des Planeten)";

            return $this->renderStorageBuilding($building, $infos);
        } // Orbitalplatform
        elseif ($building->getId() === 22) {
            $infos['title'] = "Zusätzliche Felder";
            $infos['levels'] = $this->generateStorageLevels($building);
            $infos['costs'] = $this->generateBuildingCosts($building);

            return $this->render('game/help/info/buildings/orbital.html.twig', [
                'building' => $building,
                'infos' => $infos
            ]);
        } //Raketensilo
        elseif ($building->getId() === 25) {
            $infos['title'] = "Energieverbrauch (ohne Boni)";
            $infos['levels'] = $this->generateStorageLevels($building);
            $infos['costs'] = $this->generateBuildingCosts($building);

            return $this->render('game/help/info/buildings/silo.html.twig', [
                'building' => $building,
                'infos' => $infos
            ]);
        } // Rohstoffbunker
        elseif ($building->getId() === 26) {
            $infos['title'] = "Bunkern von Rohstoffen";
            $infos['levels'] = $this->generateBunkerLevels($building);
            $infos['costs'] = $this->generateBuildingCosts($building);

            return $this->render('game/help/info/buildings/bunker_ress.html.twig', [
                'building' => $building,
                'infos' => $infos
            ]);
        } // Flottenbunker
        elseif ($building->getId() === 27) {
            $infos['title'] = "Bunkern von Schiffen";
            $infos['levels'] = $this->generateBunkerLevels($building);
            $infos['costs'] = $this->generateBuildingCosts($building);

            return $this->render('game/help/info/buildings/bunker_fleet.html.twig', [
                'building' => $building,
                'infos' => $infos
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Gebäude nicht gefunden!',
            'path' => $this->generateUrl('game.help.buildings'),
            'headline' => 'Hilfe'
        ]);
    }

    #[Route('/game/help/crypto', name: 'game.help.crypto')]
    public function crypto(): Response
    {
        return $this->render('game/help/info/crypto.html.twig');
    }

    #[Route('/game/help/textformat', name: 'game.help.textformat')]
    public function textformat(): Response
    {
        $bb = [
            [
                'm' => "Text <b>fett</b> schreiben",
                'b' => "[b]EtoA[/b]"
            ],
            [
                'm' => "Text <b>unterstreichen</b>",
                'b' => "[u]EtoA[/u]"
            ],
            [
                'm' => "Text <b>kursiv</b> schreiben",
                'b' => "[i]EtoA[/i]"
            ],
            [
                'm' => '<b>Verschiedenfarbig</b> schreiben (<a href="#colors">Farbliste</a>)',
                'b' => "[color=red]EtoA[/color]"
            ],
            [
                'm' => "<b>Grösse</b> ändern",
                'b' => "[size=15]EtoA[/size]"
            ],
            [
                'm' => "<b>Schriftart</b> ändern",
                'b' => "[font=times]EtoA[/font]"
            ],
            [
                'm' => "<b>Textausrichtung</b> ändern: zentriert",
                'b' => "[center]EtoA[/center]"
            ],
            [
                'm' => "<b>Textausrichtung</b> ändern: rechtsbündig",
                'b' => "[right]EtoA[/right]"
            ],
            [
                'm' => "<b>E-Mail</b> Link erstellen <b>(Adresse sichtbar)</b>",
                'b' => "[email]mail@etoa.ch[/email]"
            ],
            [
                'm' => "<b>E-Mail</b> Link erstellen <b>(Adresse unsichtbar)</b>",
                'b' => "[email=mail@etoa.ch]EtoA[/email]"
            ],
            [
                'm' => "<b>Link</b> zu einer Homepage erstellen <b>(Adresse sichtbar)</b>",
                'b' => "[url]http://www.etoa.ch[/url]"
            ],
            [
                'm' => "<b>Link</b> zu einer Homepage erstellen <b>(Adresse unsichtbar)</b>",
                'b' => "[url=http://www.etoa.ch]EtoA[/url]"
            ],
            [
                'm' => "<b>Bild</b> einfügen auf einer Homepage <b>(Bild sichtbar)</b>",
                'b' => "[img]http://etoa.ch/images/logo_mini.gif[/img]"
            ],
            [
                'm' => "<b>Anklickbares</b> Bild einfügen",
                'b' => "[url=http://etoa.ch/images/logo_mini.gif][img]http://etoa.ch/images/logo_mini.gif[/img][/url]"
            ],
            [
                'm' => "<b>Interner Link</b>",
                'b' => "Erkunde das [page=cell&id=635]System 635[/page] mit dem [page=help&site=shipyard&id=71]AURIGA Explorer[/page] und schreib mir eine [page=messages&mode=new]Nachricht[/page] mit den Resultaten!"
            ],
            [
                'm' => "<b>Link</b> zu einem Bild im Internet einfügen <b>(Bild nicht sichtbar)</b>",
                'b' => "[url=http://etoa.ch/images/logo_mini.gif]EtoA Logo[/url]"
            ],
            [
                'm' => "Text <b>zitieren (ohne Autor)</b>",
                'b' => "[quote]EtoA[/quote]"
            ],
            [
                'm' => "Text <b>zitieren (mit Autor)</b>",
                'b' => "[quote=Hans Muster]EtoA[/quote]"
            ],
            [
                'm' => "Blockcode: <b>Zentriert</b> den Text und verwendet die Schriftart <b>Courier New</b> (praktisch für Programmcode)",
                'b' => "[bc]EtoA ist ein Onlinebrowsergame[/bc]"
            ],
            [
                'm' => "Liste erstellen: Aufzählung <b>ohne</b> Nummerierung; beliebig viele Elemente möglich",
                'b' => "[list][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]"
            ],
            [
                'm' => "Liste erstellen: Aufzählung <b>mit nummerischer</b> Nummerierung; beliebig viele Elemente möglich",
                'b' => "[nlist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/nlist]"
            ],
            [
                'm' => "Liste erstellen: Aufzählung <b>mit nummerischer</b> Nummerierung; beliebig viele Elemente möglich",
                'b' => "[list=1][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]"
            ],
            [
                'm' => "Liste erstellen: Aufzählung <b>mit alphabetischer</b> Nummerierung; beliebig viele Elemente möglich",
                'b' => "[alist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/alist]"
            ],
            [
                'm' => "Liste erstellen: Aufzählung <b>mit alphabetischer</b> Nummerierung; beliebig viele Elemente möglich",
                'b' => "[list=a][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]"
            ],
            [
                'm' => "Liste erstellen: Aufzählung <b>mit römischer</b> Nummerierung; beliebig viele Elemente möglich",
                'b' => "[rlist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/rlist]"
            ],
            [
                'm' => "Liste erstellen: Aufzählung <b>mit römischer</b> Nummerierung; beliebig viele Elemente möglich",
                'b' => "[list=I][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]"
            ],
            [
                'm' => '<b>Flagge</b> eines Landes einfügen oder Kantons einfügen (<a href="#flags">Flaggen</a>)',
                'b' => "[flag ch-be] liegt in der [flag ch]",
            ]
        ];

        $kt = [
            [
                'n' => "Argau",
                'f' => "[flag ch-ag]"
            ],
            [
                'n' => "Appenzell Innerrhode",
                'f' => "[flag ch-ai]"
            ],
            [
                'n' => "Appenzell Ausserrhoden",
                'f' => "[flag ch-ar]"
            ],
            [
                'n' => "Bern",
                'f' => "[flag ch-be]"
            ],
            [
                'n' => "Basel Land",
                'f' => "[flag ch-bl]"
            ],
            [
                'n' => "Basel Stadt",
                'f' => "[flag ch-bs]",
            ],
            [
                'n' => "Graubünden",
                'f' => "[flag ch-gr]"
            ],
            [
                'n' => "Jura",
                'f' => "[flag ch-ju]"
            ],
            [
                'n' => "Luzern",
                'f' => "[flag ch-lu]"
            ],
            [
                'n' => "Nidwalden",
                'f' => "[flag ch-nw]",
            ],
            [
                'n' => "Obwalden",
                'f' => "[flag ch-ow]"
            ],
            [
                'n' => "Schaffhausen",
                'f' => "[flag ch-sh]"
            ],
            [
                'n' => "Schwyz",
                'f' => "[flag ch-sz]"
            ],
            [
                'n' => "Solothurn",
                'f' => "[flag ch-so]"
            ],
            [
                'n' => "Thurgau",
                'f' => "[flag ch-tg]"
            ],
            [
                'n' => "Tessin",
                'f' => "[flag ch-ti]"
            ],
            [
                'n' => "Uri",
                'f' => "[flag ch-ur]"
            ],
            [
                'n' => "Waadt",
                'f' => "[flag ch-vd]"
            ],
            [
                'n' => "Wallis",
                'f' => "[flag ch-vs]"
            ],
            [
                'n' => "Zug",
                'f' => "[flag ch-zg]"
            ],
            [
                'n' => "Zürich",
                'f' => "[flag ch-zh]"
            ],
            [
                'n' => "Genf",
                'f' => "[flag ch-ge]"
            ]
        ];

        $fl = [
            [
                'n' => "Schweiz",
                'f' => "[flag ch]"
            ],
            [
                'n' => "Argentinien",
                'f' => "[flag ar]"
            ],
            [
                'n' => "Österreich",
                'f' => "[flag at]"
            ],
            [
                'n' => "Australien",
                'f' => "[flag au]"
            ],
            [
                'n' => "Beneluxstaaten",
                'f' => "[flag benelux]"
            ],
            [
                'n' => "Bulgarien",
                'f' => "[flag bg]"
            ],
            [
                'n' => "Brasilien",
                'f' => "[flag br]"
            ],
            [
                'n' => "Kanada",
                'f' => "[flag ca]"
            ],
            [
                'n' => "China",
                'f' => "[flag cn]"
            ],
            [
                'n' => "Tschechien",
                'f' => "[flag cz]"
            ],
            [
                'n' => "Deutschland",
                'f' => "[flag de]"
            ],
            [
                'n' => "Dänemark",
                'f' => "[flag dk]"
            ],
            [
                'n' => "Estland",
                'f' => "[flag ee]"
            ],
            [
                'n' => "Europa",
                'f' => "[flag eu]"
            ],
            [
                'n' => "Finnland",
                'f' => "[flag fi]"
            ],
            [
                'n' => "Frankreich",
                'f' => "[flag fr]"
            ],
            [
                'n' => "Grossbritannien",
                'f' => "[flag gb]"
            ],
            [
                'n' => "Griechenland",
                'f' => "[flag gr]"
            ],
            [
                'n' => "Kroatien",
                'f' => "[flag hr]"
            ],
            [
                'n' => "Israel",
                'f' => "[flag il]"
            ],
            [
                'n' => "Indien",
                'f' => "[flag in]"
            ],
            [
                'n' => "Japan",
                'f' => "[flag jp]"
            ],
            [
                'n' => "Südkorea",
                'f' => "[flag kp]"
            ],
            [
                'n' => "Luxemburg",
                'f' => "[flag lu]"
            ],
            [
                'n' => "Lettland",
                'f' => "[flag lv]"
            ],
            [
                'n' => "Niederlande",
                'f' => "[flag nl]"
            ],
            [
                'n' => "Norwegen",
                'f' => "[flag no]"
            ],
            [
                'n' => "Polen",
                'f' => "[flag pl]"
            ],
            [
                'n' => "Russland",
                'f' => "[flag ru]"
            ],
            [
                'n' => "Schweden",
                'f' => "[flag se]"
            ],
            [
                'n' => "Slowakei",
                'f' => "[flag sk]"
            ],
            [
                'n' => "Spanien",
                'f' => "[flag sp]"
            ],
            [
                'n' => "Türkei",
                'f' => "[flag ty]"
            ],
            [
                'n' => "USA",
                'f' => "[flag us]"
            ],
            [
                'n' => "Vatikan",
                'f' => "[flag vn]"
            ],
            [
                'n' => "Welt",
                'f' => "[flag world]"
            ]
        ];

        return $this->render('game/help/info/textformat.html.twig', [
            'bb' => $bb,
            'kt' => $kt,
            'fl' => $fl
        ]);
    }

    #[Route('/game/help/defense', name: 'game.help.defense')]
    public function defense(Request $request): Response
    {
        $sortBy = $request->get('order') ?? 'order';

        /** @var DefenseCategoryRepository $defenseCategoryRepository */
        $defenseCategories = $this->defenseCategoryRepository->getAllCategories();
        $categoryWithDefenses = [];

        foreach ($defenseCategories as $defenseCategory) {
            $defenses = $this->defenseDataRepository->getDefenseByCategory($defenseCategory, $sortBy);
            $categoryWithDefenses[$defenseCategory->getId()]['category'] = $defenseCategory->getName();
            $categoryWithDefenses[$defenseCategory->getId()]['defenses'] = $defenses;
        }

        return $this->render('game/help/info/defense.html.twig', [
            'categoryWithDefenses' => $categoryWithDefenses
        ]);
    }

    #[Route('/game/help/defense/{id}', name: 'game.help.defense.detail')]
    public function defenseDetail(?Defense $defense): Response
    {
        return $this->render('game/help/info/defenseDetail.html.twig', [
            'defense' => $defense,
            'ship' => $this->shipDataRepository->getTransformedShipForDefense($defense)
        ]);
    }

    #[Route('/game/help/market', name: 'game.help.market')]
    public function market(): Response
    {
        return $this->render('game/help/info/market.html.twig');
    }

    #[Route('/game/help/rates', name: 'game.help.rates')]
    public function rates(): Response
    {
        $currentRates = [];
        foreach (array_keys(ResourceNames::NAMES) as $i) {
            $currentRates[$i] = $this->runtimeDataStore->get('market_rate_' . $i, "1");
        }

        return $this->render('game/help/info/rates.html.twig', [
            'currentRates' => $currentRates,
            'resourceNames' => ResourceNames::NAMES
        ]);
    }

    #[Route('/game/help/missiles', name: 'game.help.missiles')]
    public function missiles(): Response
    {
        return $this->render('game/help/info/missiles.html.twig', [
            'missiles' => $this->missileDataRepository->getMissiles()
        ]);
    }

    #[Route('/game/help/missiles/{id}', name: 'game.help.missiles.detail')]
    public function missilesDetail(Missile $missile): Response
    {
        return $this->render('game/help/info/missilesDetail.html.twig',
            [
                'missile' => $missile
            ]
        );
    }

    #[Route('/game/help/missile_system', name: 'game.help.missile_system')]
    public function missileSystem(): Response
    {
        return $this->render('game/help/info/missile_system.html.twig');
    }

    #[Route('/game/help/multi', name: 'game.help.multi_sitting')]
    public function multi(): Response
    {
        return $this->render('game/help/info/multi_sitting.html.twig');
    }

    #[Route('/game/help/planets', name: 'game.help.planets')]
    public function planets(Request $request): Response
    {
        $planetTypes = [];
        foreach ($this->planetTypeRepository->getPlanetTypes($request->get('order') ?? 'name') as $planetType) {
            $ttPlanet = new Tooltip();
            $x = mt_rand(1, 5);
            $ttPlanet->addIcon($planetType->getImagePath('small', $x));
            $ttPlanet->addTitle($planetType->getName());
            if ($planetType->isHabitable())
                $ttPlanet->addGoodCond("Bewohnbar");
            else
                $ttPlanet->addBadCond("Unbewohnbar");
            if ($planetType->isCollectGas())
                $ttPlanet->addGoodCond("Ermöglicht " . ResourceNames::FUEL . "abbau");
            $ttPlanet->addComment($planetType->getComment());

            $ttImage = new Tooltip();
            $ttImage->addImage($planetType->getImagePath('other', $x));

            $planetTypes[] = [
                'name' => $planetType->getName(),
                'metal' => $planetType->getMetal(),
                'crystal' => $planetType->getCrystal(),
                'plastic' => $planetType->getPlastic(),
                'fuel' => $planetType->getFuel(),
                'food' => $planetType->getFood(),
                'power' => $planetType->getPower(),
                'people' => $planetType->getPeople(),
                'researchTime' => $planetType->getResearchTime(),
                'buildTime' => $planetType->getBuildTime(),
                'ttPlanet' => $ttPlanet,
                'ttImage' => $ttImage,
                'imagePath' => $planetType->getImagePath('small', $x)
            ];
        }

        return $this->render('game/help/info/planets.html.twig', [
            'planetTypes' => $planetTypes
        ]);
    }

    #[Route('/game/help/population', name: 'game.help.population')]
    public function population(Request $request): Response
    {
        return $this->render('game/help/info/population.html.twig', [
            'buildingNames' => $this->buildingDataRepository->getBuildingNamesHavingPlaceForPeople(),
            'cpid' => $request->getSession()->get('cpid')
        ]);
    }

    #[Route('/game/help/power', name: 'game.help.power')]
    public function power(): Response
    {
        $buildings = [];

        foreach ($this->buildingDataRepository->getBuildingsByType(BuildingTypeId::POWER) as $building) {
            $sum = $this->buildingListItemRepository->getNumberOfBuildings($building);
            $buildings[] = [
                'name' => $building->getName(),
                'prodPower' => $building->getProdPower(),
                'buildCostsFactor' => $building->getBuildCostsFactor(),
                'productionFactor' => $building->getProductionFactor(),
                'fields' => $building->getFields(),
                'sum' => StringUtils::formatNumber($sum)
            ];
        }

        $ships = [];

        foreach ($this->shipDataRepository->getShipWithPowerProduction() as $ship) {
            $sum = $this->shipListRepository->getNumberOfShips($ship);

            $tpb1 = Planet::getSolarPowerBonus($this->configurationService->param1Int('planet_temp'), $this->configurationService->param1Int('planet_temp') + $this->configurationService->getInt('planet_temp'));
            $tpb2 = Planet::getSolarPowerBonus($this->configurationService->param2Int('planet_temp') - $this->configurationService->getInt('planet_temp'), $this->configurationService->param2Int('planet_temp'));

            $ships[] = [
                'name' => $ship->getName(),
                'powerProduction' => $ship->getPowerProduction() . " ($tpb1 bis $tpb2)",
                'sum' => StringUtils::formatNumber($sum)
            ];
        }

        return $this->render('game/help/info/power.html.twig', [
            'buildings' => $buildings,
            'ships' => $ships
        ]);
    }

    #[Route('/game/help/races', name: 'game.help.races')]
    public function races(Request $request): Response
    {
        $sortBy = $request->get('order') ?? 'name';

        return $this->render('game/help/info/races.html.twig', [
            'races' => $this->raceDataRepository->getActiveRaces($sortBy)
        ]);
    }

    #[Route('/game/help/races/{id}', name: 'game.help.races.detail')]
    public function raceDetail(?Race $race): Response
    {
        return $this->render('game/help/info/racesDetail.html.twig', [
            'race' => $race,
            'ships' => $this->shipDataRepository->getShipsByRace($race),
            'defenses' => $this->defenseDataRepository->getDefenseByRace($race)
        ]);
    }

    #[Route('/game/help/research', name: 'game.help.research')]
    public function research(): Response
    {
        $technologyTypes = $this->technologyTypeRepository->getTypes();
        $typesWithTechs = [];

        foreach ($technologyTypes as $technologyType) {
            $techs = $this->technologyDataRepository->getTechnologiesByType($technologyType);
            $typesWithTechs[$technologyType->getId()]['category'] = $technologyType->getName();
            $typesWithTechs[$technologyType->getId()]['techs'] = $techs;
        }

        return $this->render('game/help/info/research.html.twig', [
            'typesWithTechs' => $typesWithTechs
        ]);
    }

    #[Route('/game/help/research/{id}', name: 'game.help.research.detail')]
    public function researchDetail(?Technology $technology): Response
    {
        return $this->render('game/help/info/researchDetail.html.twig', [
            'technology' => $technology,
            'ships' => $this->shipRequirementRepository->findBy(['tech' => $technology]),
            'costs' => $this->generateTechnologyCosts($technology)
        ]);
    }

    #[Route('/game/help/resources', name: 'game.help.resources')]
    public function resources(): Response
    {
        return $this->render('game/help/info/resources.html.twig');
    }

    #[Route('/game/help/settings', name: 'game.help.settings')]
    public function settings(): Response
    {
        $items = [
            "Max. Spieler" => $this->configurationService->param2Int('enable_register'),
            "Urlaubsmodus Mindestdauer" => $this->configurationService->getInt('hmode_days'),
            "Einheiten/Userpunkte" => $this->configurationService->param1Int('points_update'),
            "Userpunkte/Allianzpunkte" => $this->configurationService->param2Int('points_update'),
            "Tage bis zur endgültigen Löschung eines Accounts" => $this->configurationService->getInt('user_delete_days'),
            "Spieler werden inaktiv nach (in Tagen)" => $this->configurationService->getInt('user_inactive_days'),
            "Löschung wegen Inaktivität nach (in Tagen)" => $this->configurationService->param1Int('user_inactive_days'),
            "Timeout in Sekunden" => $this->configurationService->getInt('user_timeout'),
            "Globaler Bauzeitfaktor" => $this->configurationService->getInt('global_time'),
            "Startzeitfaktor" => $this->configurationService->getFloat('flight_start_time'),
            "Landezeitfaktor" => $this->configurationService->getFloat('flight_land_time'),
            "Flugzeitfaktor" => $this->configurationService->getFloat('flight_flight_time'),
            "Verteidigungsbauzeitfaktor" => $this->configurationService->getFloat('def_build_time'),
            "Gebäudebauzeitfaktor" => $this->configurationService->getFloat('build_build_time'),
            "Forschungszeitfaktor" => $this->configurationService->getFloat('res_build_time'),
            "Schiffbauzeitfaktor" => $this->configurationService->getFloat('ship_build_time'),
            "Minimale Planetentemperatur" => $this->configurationService->param1Int('planet_temp'),
            "Maximale Planetentemperatur" => $this->configurationService->param2Int('planet_temp'),
            "Minimale Feldanzahl" => $this->configurationService->param1Int('planet_fields'),
            "Maximale Feldanzahl" => $this->configurationService->param2Int('planet_fields'),
            "Minimale Planetenanzahl" => $this->configurationService->param1Int('num_planets'),
            "Maximale Planetenanzahl" => $this->configurationService->param2Int('num_planets'),
            "Maximale Planeten/User" => $this->configurationService->getInt('user_max_planets'),
            "Verteidigungswiederherstellung" => $this->configurationService->getFloat('def_restore_percent'),
            "Verteidigung ins Trümmerfeld" => $this->configurationService->getFloat('def_wf_percent'),
            "Schiffe ins Trümmerfeld" => $this->configurationService->getFloat('ship_wf_percent'),
            "Noobschutz: Minimale Punkte" => $this->configurationService->getInt('user_attack_min_points'),
            "Noobschutz: Verhältnis %" => $this->configurationService->getInt('user_attack_percentage'),
            "Dauer eines Krieges (in Stunden)" => $this->configurationService->getInt('alliance_war_time'),
            "Nahrungsverbrauch pro Arbeiter" => $this->configurationService->getInt('people_food_require'),
            "Bevölkerungswachstum" => $this->configurationService->getFloat('people_multiply'),
        ];

        return $this->render('game/help/info/settings.html.twig', ['items' => $items]);
    }

    #[Route('/game/help/shipyard', name: 'game.help.shipyard')]
    public function shipyard(Request $request): Response
    {
        $sortBy = $request->get('order') ?? 'order';

        $shipCategories = $this->shipCategoryRepository->getAllCategories();
        $categoryWithShips = [];
        foreach ($shipCategories as $shipCategory) {
            $ships = [];
            $shipData = $this->shipDataRepository->getShipsByCategory($shipCategory, $sortBy);
            $categoryWithShips[$shipCategory->getId()]['category'] = $shipCategory->getName();
            foreach ($shipData as $ship) {
                $tooltip = new Tooltip();
                $tooltip->addTitle($ship->getName());
                $tooltipText = BBCodeUtils::toHTML($ship->getShortComment()) . "<br/><br/>" . $this->shipRanking($ship);
                $tooltip->addText($tooltipText);

                $ships[] = [
                    'id' => $ship->getId(),
                    'name' => $ship->getName(),
                    'race' => $ship->getRace(),
                    'capacity' => $ship->getCapacity(),
                    'speed' => $ship->getSpeed(),
                    'fuelUse' => $ship->getFuelUse(),
                    'weapon' => $ship->getWeapon(),
                    'structure' => $ship->getStructure(),
                    'shield' => $ship->getShield(),
                    'pilots' => $ship->getPilots(),
                    'heal' => $ship->getHeal(),
                    'points' => $ship->getPoints(),
                    'tooltip' => $tooltip,
                    'image' => $ship->getImagePath()
                ];
            }

            $categoryWithShips[$shipCategory->getId()]['ships'] = $ships;
        }

        return $this->render('game/help/info/shipyard.html.twig',
            [
                'categoryWithShips' => $categoryWithShips
            ]
        );
    }

    #[Route('/game/help/shipyard/{id}', name: 'game.help.shipyard.detail')]
    public function shipyardDetail(Ship $ship): Response
    {
        $actions = array_filter(explode(",", $ship->getActions()));
        $factoredActions = [];

        foreach ($actions as $i) {
            $factoredActions[] = FleetAction::createFactory($i);
        }

        return $this->render('game/help/info/shipyardDetail.html.twig',
            [
                'ship' => $ship,
                'shipRanking' => $this->shipRanking($ship),
                'actions' => $factoredActions,
                'technologies' => $this->shipRequirementRepository->getRequiredSpeedTechnologies($ship)
            ]
        );
    }

    #[Route('/game/help/space', name: 'game.help.space')]
    public function space(): Response
    {
        return $this->render('game/help/info/space.html.twig');
    }

    #[Route('/game/help/specialists', name: 'game.help.specialists')]
    public function specialists(): Response
    {
        $specialists = $this->specialistDataRepository->getActiveSpecialists();

        return $this->render('game/help/info/specialists.html.twig', [
            'specialists' => $specialists
        ]);
    }

    #[Route('/game/help/specialpoints', name: 'game.help.specialpoints')]
    public function specialpoints(): Response
    {
        return $this->render('game/help/info/specialpoints.html.twig');
    }

    #[Route('/game/help/spy', name: 'game.help.spy')]
    public function spy(): Response
    {
        $ships = $this->shipDataRepository->getShipsWithAction('spy');

        return $this->render('game/help/info/spy.html.twig', [
            'ships' => $ships
        ]);
    }

    #[Route('/game/help/stars', name: 'game.help.stars')]
    public function stars(Request $request): Response
    {
        $sortBy = $request->get('order') ?? 'name';
        $stars = $this->solarTypeRepository->getSolarTypes($sortBy);

        return $this->render('game/help/info/stars.html.twig', [
            'solarTypes' => $stars
        ]);
    }

    #[Route('/game/help/stats', name: 'game.help.stats')]
    public function stats(): Response
    {
        return $this->render('game/help/info/stats.html.twig');
    }

    #[Route('/game/help/techtree', name: 'game.help.techtree')]
    public function techtree(): Response
    {
        return $this->render('game/help/info/techtree.html.twig');
    }

    #[Route('/game/help/tempbonus', name: 'game.help.tempbonus')]
    public function tempbonus(): Response
    {
        return $this->render('game/help/info/tempbonus.html.twig');
    }

    #[Route('/game/help/u_mod', name: 'game.help.u_mod')]
    public function u_mod(): Response
    {
        return $this->render('game/help/info/u_mod.html.twig');
    }

    private function generateProductionLevels(Building $building): array
    {
        $levels = [];
        $resources = ['Metal', 'Crystal', 'Plastic', 'Fuel', 'Food', 'Power'];
        $maxLevel = min(31, $building->getLastLevel() + 1);

        for ($level = 1; $level < $maxLevel; $level++) {
            foreach ($resources as $resource) {
                $getProd = "getProd$resource";
                $prod_item = round($building->{$getProd}() * pow($building->getProductionFactor(), $level - 1));
                $power_use = round($building->getPowerUse() * pow($building->getProductionFactor(), $level - 1));
                $prod_items[strtolower($resource)] = $prod_item;
                $levels[$level] = ['prod_items' => $prod_items, 'power_use' => $power_use];
            }
        }

        return $levels;
    }

    private function generateStorageLevels(Building $building): array
    {
        $levels = [];
        $resources = ['Metal', 'Crystal', 'Plastic', 'Fuel', 'Food'];
        $maxLevel = min(31, $building->getLastLevel() + 1);

        for ($level = 1; $level < $maxLevel; $level++) {
            foreach ($resources as $resource) {
                $getRessource = "getStore$resource";
                $baseStore = $this->buildingDataRepository->getBuilding(6)->{$getRessource}();
                $prod_item = round($building->{$getRessource}() * pow($building->getStoreFactor(), $level - 1));
                $power_use = round($building->getPowerUse() * pow($building->getProductionFactor(), $level - 1));
                $fields_provide = round($building->getFieldsProvide() * pow($building->getProductionFactor(), $level - 1));
                $prod_items[strtolower($resource)] = $prod_item;
                $levels[$level] = ['prod_items' => $prod_items, 'base_store' => $baseStore, 'power_use' => $power_use, 'fields_provide' => $fields_provide];
            }
        }

        return $levels;
    }

    private function generateBunkerLevels(Building $building): array
    {
        $levels = [];
        $resources = ['Res', 'FleetSpace'];

        for ($level = 1; $level < $building->getLastLevel() + 1; $level++) {
            foreach ($resources as $resource) {
                $getRessource = "getBunker$resource";
                $prod_item = round($building->{$getRessource}() * pow($building->getStoreFactor(), $level - 1));
                $prod_items[strtolower($resource)] = $prod_item;
                $levels[$level] = ['prod_items' => $prod_items, 'fleet_count' => round($building->getBunkerFleetCount() * pow($building->getStoreFactor(), $level - 1))];
            }
        }

        return $levels;
    }

    private function generateBuildingCosts(Building $building): array
    {
        $costs = [];
        for ($x = 0; $x < min(30, $building->getLastLevel()); $x++) {
            $costs[] = $this->buildingCostCalculator->calculate($building, $x, $this->buildingCostContext);
        }

        return $costs;
    }

    private function generateTechnologyCosts(Technology $technology): array
    {
        $costs = [];
        for ($x = 0; $x < min(30, $technology->getLastLevel()); $x++) {
            $costs[] = $this->technologyService->calcTechCosts($technology, $x);
        }

        return $costs;
    }

    private function renderProductionBuilding(Building $building, array $infos): Response
    {
        $infos['levels'] = $this->generateProductionLevels($building);
        $infos['costs'] = $this->generateBuildingCosts($building);


        return $this->render('game/help/info/buildings/production.html.twig', [
            'building' => $building,
            'infos' => $infos
        ]);
    }

    private function renderStorageBuilding(Building $building, array $infos): Response
    {
        $infos['levels'] = $this->generateStorageLevels($building);
        $infos['costs'] = $this->generateBuildingCosts($building);

        return $this->render('game/help/info/buildings/storage.html.twig', [
            'building' => $building,
            'infos' => $infos
        ]);
    }

    private function shipRanking(Ship $ship): string
    {
        ob_start();
        echo "<table class=\"tb\">";
        echo "<tr><th>Struktur:</th><td>" . $this->rankingStars($ship->getStructure(), 20000) . "</td></tr>";
        echo "<tr><th>Schilder:</th><td>" . $this->rankingStars($ship->getShield(), 25000) . "</td></tr>";
        echo "<tr><th>Waffen:</th><td>" . $this->rankingStars($ship->getWeapon(), 50000) . "</td></tr>";
        echo "<tr><th>Speed:</th><td>" . $this->rankingStars($ship->getSpeed(), 5000) . "</td></tr>";
        echo "<tr><th>Kapazität:</th><td>" . $this->rankingStars($ship->getCapacity(), 100000) . "</td></tr>";
        echo "<tr><th>Reisekosten:</th><td>" . $this->rankingStars($ship->getFuelUse(), 60) . "</td></tr>";
        echo "</table>";
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    private function rankingStars($val, $max2): string
    {
        $max = $max2;
        $img = "star_r";

        $t = $max / 5;
        $s = "";
        for ($x = 0; $x < 5; $x++) {
            if ($val == 0)
                $s .= "<img src=\"/build/images/star_g.gif\" />";
            elseif ($val > 3 * $max)
                $s .= "<img src=\"/build/images/star_y.gif\" />";
            elseif ($val > $t * $x)
                $s .= "<img src=\"/build/images/" . $img . ".gif\" />";
            else
                $s .= "<img src=\"/build/images/star_g.gif\" />";
        }
        return $s;
    }
}