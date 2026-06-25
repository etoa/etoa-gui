<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingCostCalculator;
use EtoA\Building\BuildingCostContext;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingTypeDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Building;
use EtoA\Fleet\FleetAction;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\ExternalUrl;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResourceNames;
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
        private readonly BuildingCostContext        $buildingCostContext
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
                "Spionage" => array('spy_info', 'Wie das Spionagesystem funktioniert?'),
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
    public function buildingDetail(?Building $building, Request $request): Response
    {
        $infos = [];
        $infos['building'] = $building;

        // Metallmine
        if ($building->getId() === 1) {
            $infos['title'] = "Produktion von " . ResourceNames::METAL . " (ohne Boni)";
            $infos['levels'] = $this->generateProductionLevels($building);
            $infos['costs'] = $this->generateBuildingCosts($building);

            return $this->render('game/help/info/buildings/production.html.twig', [
                'building' => $building,
                'infos' => $infos
            ]);
        } // Siliziummine
        elseif ($building->getId() === 2) {
            $infos['title'] = "Produktion von " . ResourceNames::CRYSTAL . " (ohne Boni)";
            $infos['levels'] = $this->generateProductionLevels($building);
        } // Chemiefabrik
        elseif ($building->getId() === 3) {
            $infos['title'] = "Produktion von " . ResourceNames::PLASTIC . " (ohne Boni)";
            $infos['levels'] = $this->generateProductionLevels($building);
        } // Tritiumsynthetizer
        elseif ($building->getId() === 4) {
            $infos['title'] = "Produktion von " . ResourceNames::FUEL . " (ohne Boni)";
            $infos['levels'] = $this->generateProductionLevels($building);
        } // Gewächshaus
        elseif ($building->getId() === 5) {
            $infos['title'] = "Produktion von " . ResourceNames::FOOD . " (ohne Boni)";
            $infos['levels'] = $this->generateProductionLevels($building);
        } // Planetenbasis
        elseif ($building->getId() === 6) {
            $infos['title'] = "Produktion (ohne Boni)";
        } // Wohnmodul
        elseif ($building->getId() === 7) {
            $infos['title'] = "Platz für Bewohner";
            $basePeoplePlace = $this->buildingDataRepository->getBuilding(6)->getPeoplePlace();
            for ($level = 1; $level < 31; $level++) {
                $prod_item = round($building->getPeoplePlace() * pow($building->getStoreFactor(), $level - 1));
                $levels[$level] = ['prod_item' => $prod_item, 'place' => $prod_item + $basePeoplePlace + $this->configurationService->param1Int('user_start_people')];
            }
            $infos['levels'] = $levels;
        }
        // Windkraftwerk
        // Solarkaftwerk
        // Fusionskraftwerk
        // Gezeitenkraftwerk
        elseif (in_array($building->getId(), [12, 13, 14, 15], true)) {
            $infos['title'] = "Energieproduktion (ohne Boni)";
            $infos['levels'] = $this->generateProductionLevels($building);
        } // Titanspeicher
        elseif ($building->getId() === 16) {
            $baseStoreMetal = $this->buildingDataRepository->getBuilding(6)->getStoreMetal();
            $infos['title'] = "Lagerkapazität (inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStoreMetal) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ") des Planeten)";
            $infos['levels'] = $this->generateStorageLevels($building);
        } // Siliziumspeicher
        elseif ($building->getId() === 17) {
            $baseStoreCrystal = $this->buildingDataRepository->getBuilding(6)->getStoreCrystal();
            $infos['title'] = "Lagerkapazität (inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStoreCrystal) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ") des Planeten)";
            $infos['levels'] = $this->generateStorageLevels($building);
        } // Lagerhalle
        elseif ($building->getId() === 18) {
            $baseStorePlastic = $this->buildingDataRepository->getBuilding(6)->getStorePlastic();
            $infos['title'] = "Kapazität inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStorePlastic) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ")";
            $infos['levels'] = $this->generateStorageLevels($building);
        } // Nahrungssilos
        elseif ($building->getId() === 19) {
            $baseStoreFood = $this->buildingDataRepository->getBuilding(6)->getStoreFood();
            $infos['title'] = "Lagerkapazität (inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStoreFood) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ") des Planeten)";
            $infos['levels'] = $this->generateStorageLevels($building);
        } // Tritiumsilo
        elseif ($building->getId() === 20) {
            $baseStoreFuel = $this->buildingDataRepository->getBuilding(6)->getStoreFuel();
            $infos['title'] = "Lagerkapazität (inklusive Planetenbasiskapazität (" . StringUtils::formatNumber($baseStoreFuel) . ") und Standardkapazität (" . StringUtils::formatNumber($this->configurationService->getInt("def_store_capacity")) . ") des Planeten)";
            $infos['levels'] = $this->generateStorageLevels($building);
        } // Orbitalplatform
        elseif ($building->getId() === 22) {
            $infos['title'] = "Zusätzliche Felder";
            $infos['levels'] = $this->generateStorageLevels($building);
        } //Raketensilo
        elseif ($building->getId() === 25) {
            $infos['title'] = "Energieverbrauch (ohne Boni)";
            $infos['levels'] = $this->generateStorageLevels($building);
        } // Rohstoffbunker
        elseif ($building->getId() === 26) {
            $infos['title'] = "Bunkern von Rohstoffen";
            $infos['levels'] = $this->generateBunkerLevels($building);
        } // Flottenbunker
        elseif ($building->getId() === 27) {
            $infos['title'] = "Bunkern von Schiffen";
            $infos['levels'] = $this->generateBunkerLevels($building);
        }

        return $this->render('game/help/info/buildingsDetail.html.twig', [
            'building' => $building,
            'infos' => $infos
        ]);
    }

    private function generateProductionLevels(Building $building): array
    {
        $levels = [];
        $resources = ['Metal', 'Crystal', 'Plastic', 'Fuel', 'Food', 'Power'];

        for ($level = 1; $level < 31; $level++) {
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

        for ($level = 1; $level < 31; $level++) {
            foreach ($resources as $resource) {
                $getRessource = "getStore$resource";
                $baseStore = $this->buildingDataRepository->getBuilding(6)->{$getRessource}();
                $prod_item = $this->configurationService->getInt("def_store_capacity") + $baseStore + round($building->{$getRessource}() * pow($building->getStoreFactor(), $level - 1));
                $power_use = round($building->getPowerUse() * pow($building->getProductionFactor(), $level - 1));
                $fields_provide = round($building->getFieldsProvide() * pow($building->getProductionFactor(), $level - 1));
                $prod_items[strtolower($resource)] = $prod_item;
                $levels[$level] = ['prod_items' => $prod_items, 'power_use' => $power_use, 'fields_provide' => $fields_provide];
            }
        }

        return $levels;
    }

    private function generateBunkerLevels(Building $building): array
    {
        $levels = [];
        $resources = ['Res', 'FleetSpace'];

        for ($level = 1; $level < $building->getLastLevel(); $level++) {
            foreach ($resources as $resource) {
                $getRessource = "bunker$resource";
                $prod_item = round($building->{$getRessource} * pow($building->getStoreFactor(), $level - 1));
                $prod_items[strtolower($resource)] = $prod_item;
                $levels[$level] = ['prod_items' => $prod_items];
            }
        }

        return $levels;
    }

    private function generateBuildingCosts(Building $building): array
    {
        $costs = [];
        for ($x = 0; $x < min(30, $building->getLastLevel()); $x++) {
            $costs[] = $this->buildingCostCalculator->calculate($building,$x, $this->buildingCostContext);
        }

        return $costs;
    }
}