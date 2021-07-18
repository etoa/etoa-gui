<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\User\UserPropertiesRepository;
use Exception;

class GameStatsGenerator
{
    private const GAME_STATS_FILE = CACHE_ROOT . "/out/gamestats.html";

    private PlanetTypeRepository $planetTypeRepository;
    private SolarTypeRepository $solarTypeRepository;
    private RaceDataRepository $raceDataRepository;
    private PlanetRepository $planetRepository;
    private BuildingRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;
    private UserPropertiesRepository $userPropertiesRepository;

    public function __construct(
        PlanetTypeRepository $planetTypeRepository,
        SolarTypeRepository $solarTypeRepository,
        RaceDataRepository $raceDataRepository,
        PlanetRepository $planetRepository,
        BuildingRepository $buildingRepository,
        TechnologyRepository $technologyRepository,
        ShipRepository $shipRepository,
        DefenseRepository $defenseRepository,
        UserPropertiesRepository $userPropertiesRepository
    ) {
        $this->planetTypeRepository = $planetTypeRepository;
        $this->solarTypeRepository = $solarTypeRepository;
        $this->raceDataRepository = $raceDataRepository;
        $this->planetRepository = $planetRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
        $this->userPropertiesRepository = $userPropertiesRepository;
    }

    public function readCached(): ?string
    {
        return is_file(self::GAME_STATS_FILE) ? file_get_contents(self::GAME_STATS_FILE) : null;
    }

    public function generateAndSave(): void
    {
        $file = self::GAME_STATS_FILE;

        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (file_put_contents($file, $this->generate()) === false) {
            throw new Exception("Error! Could not write game stats file $file!");
        }
    }

    public function generate(): string
    {
        $renderStartTime = timerStart();

        $out = '';
        $out .= $this->createUniverseStats();
        $out .= $this->createResourceStats();
        $out .= $this->createOverallConstructionStats();
        $out .= $this->createBestPlayerConstructionStats();
        $out .= $this->createMiscStats();

        $renderTime = timerStop($renderStartTime);
        $out .= "<br/>Erstellt am " . date("d.m.Y") . " um " . date("H:i") . " Uhr ";
        $out .= " in " . $renderTime . " Sekunden";

        return $out;
    }

    private function createUniverseStats(): string
    {
        $out = "<h2>Universum</h2>";
        $out .= "<table width=\"95%\">";
        $out .= "<tr>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->numberOfOwnedPlanetsByType();
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->numberOfOwnedSystemsByType();
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->numberOfRacesByType();
        $out .= "</td>";

        $out .= "</tr>";
        $out .= "</table>";

        return $out;
    }

    private function numberOfOwnedPlanetsByType(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"3\">Bewohnte Planetentypen</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->planetTypeRepository->getNumberOfOwnedPlanetsByType() as $arr) {
            $out .= "<tr>
                <td>" . $rank++ . "</td>
                <td>" . $arr['name'] . "</td>
                <td>" . $arr['cnt'] . "</td>
            </tr>";
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td ><b>" . $total . "</b></td>
        </tr>";
        $out .= "</table>";

        return $out;
    }

    private function numberOfOwnedSystemsByType(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th colspan=\"3\">Benannte Systeme</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->solarTypeRepository->getNumberOfNamedSystemsByType() as $arr) {
            $out .= "<tr>
                <td>" . $rank++ . "</td>
                <td>" . $arr['name'] . "</td>
                <td>" . $arr['cnt'] . "</td>
            </tr>";
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td ><b>" . $total . "</b></td>
        </tr>";
        $out .= "</table>";

        return $out;
    }

    private function numberOfRacesByType(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"3\">Rassen</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->raceDataRepository->getNumberOfRacesByType() as $arr) {
            $out .= "<tr>
                <td>" . $rank++ . "</td>
                <td>" . $arr['name'] . "</td>
                <td>" . $arr['cnt'] . "</td>
            </tr>";
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td><b>" . $total . "</b></td>
        </tr>";
        $out .= "</table>";

        return $out;
    }

    private function createResourceStats(): string
    {
        $out = "<h2>Rohstoffe</h2>";
        $out .= "<table width=\"95%\">";
        $out .= "<tr>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->maxResourcesOnAPlanet();
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->maxResourcesInUniverse();
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->maxResourcesOfAPlayer();
        $out .= "</td>";

        $out .= "</tr>";
        $out .= "</table>";

        return $out;
    }

    private function maxResourcesOnAPlanet(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"3\">Max Ressourcen auf einem Planeten</th></tr>";

        $metal = $this->planetRepository->getMaxMetalOnAPlanet();
        if ($metal !== null) {
            $out .= "<tr>
                <td >" . RES_METAL . "</td>
                <td >" . nf($metal['res']) . "</td>
                <td >" . $metal['type'] . "</td>
            </tr>";
        }

        $crystal = $this->planetRepository->getMaxCrystalOnAPlanet();
        if ($crystal !== null) {
            $out .= "<tr>
                    <td >" . RES_CRYSTAL . "</td>
                    <td >" . nf($crystal['res']) . "</td>
                    <td >" . $crystal['type'] . "</td>
                </tr>";
        }

        $plastic = $this->planetRepository->getMaxPlasticOnAPlanet();
        if ($plastic !== null) {
            $out .= "<tr>
                    <td >" . RES_PLASTIC . "</td>
                    <td >" . nf($plastic['res']) . "</td>
                    <td >" . $plastic['type'] . "</td>
                </tr>";
        }

        $fuel = $this->planetRepository->getMaxFuelOnAPlanet();
        if ($fuel !== null) {
            $out .= "<tr>
                    <td >" . RES_FUEL . "</td>
                    <td >" . nf($fuel['res']) . "</td>
                    <td >" . $fuel['type'] . "</td>
                </tr>";
        }

        $food = $this->planetRepository->getMaxFoodOnAPlanet();
        if ($food !== null) {
            $out .= "<tr>
                    <td >" . RES_FOOD . "</td>
                    <td >" . nf($food['res']) . "</td>
                    <td >" . $food['type'] . "</td>
                </tr>";
            $out .= "</table>";
        }

        return $out;
    }

    private function maxResourcesInUniverse(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"4\">Total Ressourcen im Universum</th></tr>";
        $out .= "<tr><th >Ressource</th><th >Total</th><th >Durchschnitt</th><th >Planeten</th></tr>";

        $metal = $this->planetRepository->getMaxMetal();
        $out .= "<tr>
                <td >" . RES_METAL . "</td>
                <td >" . nf($metal['sum']) . "</td>
                <td >" . nf($metal['avg']) . "</td>
                <td >" . nf($metal['cnt']) . "</td>
            </tr>";

        $crystal = $this->planetRepository->getMaxCrystal();
        $out .= "<tr>
                <td >" . RES_CRYSTAL . "</td>
                <td >" . nf($crystal['sum']) . "</td>
                <td >" . nf($crystal['avg']) . "</td>
                <td >" . nf($crystal['cnt']) . "</td>
            </tr>";

        $plastic = $this->planetRepository->getMaxPlastic();
        $out .= "<tr>
                <td >" . RES_PLASTIC . "</td>
                <td >" . nf($plastic['sum']) . "</td>
                <td >" . nf($plastic['avg']) . "</td>
                <td >" . nf($plastic['cnt']) . "</td>
            </tr>";

        $fuel = $this->planetRepository->getMaxFuel();
        $out .= "<tr>
                <td >" . RES_FUEL . "</td>
                <td >" . nf($fuel['sum']) . "</td>
                <td >" . nf($fuel['avg']) . "</td>
                <td >" . nf($fuel['cnt']) . "</td>
            </tr>";

        $food = $this->planetRepository->getMaxFood();
        $out .= "<tr>
                <td >" . RES_FOOD . "</td>
                <td >" . nf($food['sum']) . "</td>
                <td >" . nf($food['avg']) . "</td>
                <td >" . nf($food['cnt']) . "</td>
            </tr>";
        $out .= "</table>";

        return $out;
    }

    private function maxResourcesOfAPlayer(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"3\">Max Ressourcen eines Spielers</th></tr>";
        $out .= "<tr>
                <td >" . RES_METAL . "</td>
                <td >" . nf($this->planetRepository->getMaxMetalOfAPlayer()) . "</td>
            </tr>";
        $out .= "<tr>
                <td >" . RES_CRYSTAL . "</td>
                <td >" . nf($this->planetRepository->getMaxCrystalOfAPlayer()) . "</td>
            </tr>";
        $out .= "<tr>
                <td >" . RES_PLASTIC . "</td>
                <td >" . nf($this->planetRepository->getMaxPlasticOfAPlayer()) . "</td>
            </tr>";
        $out .= "<tr>
                <td >" . RES_FUEL . "</td>
                <td >" . nf($this->planetRepository->getMaxFuelOfAPlayer()) . "</td>
            </tr>";
        $out .= "<tr>
                <td >" . RES_FOOD . "</td>
                <td >" . nf($this->planetRepository->getMaxFoodOfAPlayer()) . "</td>
            </tr>";
        $out .= "</table>";

        return $out;
    }

    private function createOverallConstructionStats(): string
    {
        $out = "<h2>Konstruktionen (Gesamt Anzahl von allen Spielern)</h2>";
        $out .= "<table width=\"95%\">";
        $out .= "<tr>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->overallShipsInUniverse();
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->overallDefenseInUniverse();
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->overallBuildingsInUniverse();
        $out .= "</td>";

        $out .= "</tr>";
        $out .= "</table>";

        return $out;
    }

    private function overallShipsInUniverse(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"4\">Schiffe ohne Flotten (Beste Leistung, Gesamt)</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->shipRepository->getOverallCount() as $arr) {
            $out .= "<tr>
                <td>" . $rank . "</td>
                <td>" . $arr['name'] . "</td>
                <td>" . nf($arr['max']) . "</td>
                <td>" . nf($arr['cnt']) . "</td>
            </tr>";
            $rank++;
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td>&nbsp;</td>
            <td><b>" . nf($total) . "</b></td>
        </tr>";
        $out .= "</table>";

        return $out;
    }

    private function overallDefenseInUniverse(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th colspan=\"4\">Verteidigung</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->defenseRepository->getOverallCount() as $arr) {
            $out .= "<tr>
                <td>" . $rank . "</td>
                <td>" . $arr['name'] . "</td>
                <td>" . nf($arr['max']) . "</td>
                <td>" . nf($arr['cnt']) . "</td>
            </tr>";
            $rank++;
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td>&nbsp;</td>
            <td><b>" . nf($total) . "</b></td>
        </tr>";
        $out .= "</table>";

        return $out;
    }

    private function overallBuildingsInUniverse(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"3\">Geb&auml;ude</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->buildingRepository->getOverallCount() as $arr) {
            $out .= "<tr>
                <td>" . $rank . "</td>
                <td>" . $arr['name'] . "</td>
                <td>" . nf($arr['cnt']) . "</td>
            </tr>";
            $rank++;
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td ><b>" . nf($total) . "</b></td>
        </tr>";
        $out .= "</table>";

        return $out;
    }

    private function createBestPlayerConstructionStats(): string
    {
        $out = "<h2>Konstruktionen (Die beste Leistung eines Einzelnen)</h2>";
        $out .= "<table width=\"95%\">";
        $out .= "<tr>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->bestPlayerResearchStats();
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->bestPlayerBuildingStats();
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->specialShipStats();
        $out .= "</td>";

        $out .= "</tr>";
        $out .= "</table>";

        return $out;
    }

    private function bestPlayerResearchStats(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"3\">Forschungen</th></tr>";
        $rank = 1;
        foreach ($this->technologyRepository->getBestLevels() as $arr) {
            $out .= "<tr>
                <td>" . $rank . "</td>
                <td>" . $arr['name'] . "</td>
                <td>" . nf($arr['max']) . "</td>
            </tr>";
            $rank++;
        }
        $out .= "</table>";

        return $out;
    }

    private function bestPlayerBuildingStats(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"3\">Geb&auml;ude</th></tr>";
        $rank = 1;
        foreach ($this->buildingRepository->getBestLevels() as $arr) {
            $out .= "<tr>
                <td >" . $rank . "</td>
                <td >" . $arr['name'] . "</td>
                <td >" . nf($arr['max']) . "</td>
            </tr>";
            $rank++;
        }
        $out .= "</table>";

        return $out;
    }

    private function specialShipStats(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"4\">Spezialschiffe (Level, EXP)</th></tr>";
        $rank = 1;
        foreach ($this->shipRepository->getSpecialShipStats() as $arr) {
            $out .= "<tr>
                <td>" . $rank . "</td>
                <td>" . $arr['name'] . "</td>
                <td>" . nf($arr['level']) . "</td>
                <td>" . nf($arr['exp']) . "</td>
            </tr>";
            $rank++;
        }
        $out .= "</table>";

        return $out;
    }

    private function createMiscStats(): string
    {
        $limit = 5;

        $out = "<h2>Sonstiges</h2>";
        $out .= "<table width=\"95%\"><tr>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->designStats($limit);
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->imagePackStats($limit);
        $out .= "</td>";

        $out .= "<td style=\"width:33%;vertical-align:top;\">";
        $out .= $this->imageExtensionStats($limit);
        $out .= "</td>";

        $out .= "</tr>";

        $out .= "</table>";

        return $out;
    }

    private function designStats(int $limit): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th colspan=\"4\">Design</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->userPropertiesRepository->getDesignStats($limit) as $design => $count) {
            $total += $count;
            $out .= "<tr>
                <td>" . $rank++ . "</td>";
            $out .= $design != ""
                ? "<td>" . strtr($design, ["css_style/" => ""]) . "</td>"
                : "<td><i>Standard</i></td>";
            $out .= "<td>" . nf($arr['cnt']) . "</td>";
            $out .= "<td>" . round(100 / $total * $count, 2) . "%</td></tr>";
        }
        $out .= "</table>";

        return $out;
    }

    private function imagePackStats(int $limit): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th colspan=\"4\">Bildpaket</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->userPropertiesRepository->getImagePackStats($limit) as $arr) {
            $total += $arr['cnt'];
            $out .= "<tr>
                <td>" . $rank++ . "</td>";
            $out .= $arr['name'] != ""
                ? "<td>" . strtr($arr['name'], ["images/themes/" => ""]) . "</td>"
                : "<td><i>Standard</i></td>";
            $out .= "<td>" . nf($arr['cnt']) . "</td>";
            $out .= "<td>" . round(100 / $total * $arr['cnt'], 2) . "%</td></tr>";
        }
        $out .= "</table>";

        return $out;
    }

    private function imageExtensionStats(int $limit): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th colspan=\"4\">Bild-Erweiterung</th></tr>";
        $rank = 1;
        $total = 0;
        foreach ($this->userPropertiesRepository->getImageExtensionStats($limit) as $arr) {
            $total += $arr['cnt'];
            $out .= "<tr>
                <td>" . $rank++ . "</td>";
            $out .= $arr['name'] != ""
                ? "<td>" . $arr['name'] . "</td>"
                : "<td><i>Standard</i></td>";
            $out .= "<td>" . nf($arr['cnt']) . "</td>";
            $out .= "<td>" . round(100 / $total * $arr['cnt'], 2) . "%</td></tr>";
        }

        $out .= "</table>";

        return $out;
    }
}
