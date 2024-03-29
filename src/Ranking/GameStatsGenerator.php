<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\User\UserPropertiesRepository;
use Exception;

class GameStatsGenerator
{
    private const GAME_STATS_FILE = "/out/gamestats.html";
    public const USER_STATS_FILE = "/out/userstats.png";
    public const USER_STATS_FILE_PUBLIC_PATH = "/cache/out/userstats.png";
    public const XML_INFO_FILE = "/xml/info.xml";
    public const XML_INFO_FILE_PUBLIC_PATH = "/cache/xml/info.xml";

    private PlanetTypeRepository $planetTypeRepository;
    private SolarTypeRepository $solarTypeRepository;
    private RaceDataRepository $raceDataRepository;
    private PlanetRepository $planetRepository;
    private BuildingRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;
    private UserPropertiesRepository $userPropertiesRepository;
    private string $cacheDir;

    public function __construct(
        PlanetTypeRepository $planetTypeRepository,
        SolarTypeRepository $solarTypeRepository,
        RaceDataRepository $raceDataRepository,
        PlanetRepository $planetRepository,
        BuildingRepository $buildingRepository,
        TechnologyRepository $technologyRepository,
        ShipRepository $shipRepository,
        DefenseRepository $defenseRepository,
        UserPropertiesRepository $userPropertiesRepository,
        string $cacheDir
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
        $this->cacheDir = $cacheDir;
    }

    public function readCached(): ?string
    {
        return is_file($this->cacheDir . self::GAME_STATS_FILE) ? file_get_contents($this->cacheDir . self::GAME_STATS_FILE) : null;
    }

    public function generateAndSave(): void
    {
        $file = $this->cacheDir . self::GAME_STATS_FILE;

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
                <td >" . ResourceNames::METAL . "</td>
                <td >" . StringUtils::formatNumber((int) $metal['res']) . "</td>
                <td >" . $metal['type'] . "</td>
            </tr>";
        }

        $crystal = $this->planetRepository->getMaxCrystalOnAPlanet();
        if ($crystal !== null) {
            $out .= "<tr>
                    <td >" . ResourceNames::CRYSTAL . "</td>
                    <td >" . StringUtils::formatNumber((int) $crystal['res']) . "</td>
                    <td >" . $crystal['type'] . "</td>
                </tr>";
        }

        $plastic = $this->planetRepository->getMaxPlasticOnAPlanet();
        if ($plastic !== null) {
            $out .= "<tr>
                    <td >" . ResourceNames::PLASTIC . "</td>
                    <td >" . StringUtils::formatNumber((int) $plastic['res']) . "</td>
                    <td >" . $plastic['type'] . "</td>
                </tr>";
        }

        $fuel = $this->planetRepository->getMaxFuelOnAPlanet();
        if ($fuel !== null) {
            $out .= "<tr>
                    <td >" . ResourceNames::FUEL . "</td>
                    <td >" . StringUtils::formatNumber((int) $fuel['res']) . "</td>
                    <td >" . $fuel['type'] . "</td>
                </tr>";
        }

        $food = $this->planetRepository->getMaxFoodOnAPlanet();
        if ($food !== null) {
            $out .= "<tr>
                    <td >" . ResourceNames::FOOD . "</td>
                    <td >" . StringUtils::formatNumber((int) $food['res']) . "</td>
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
                <td >" . ResourceNames::METAL . "</td>
                <td >" . StringUtils::formatNumber((int) $metal['sum']) . "</td>
                <td >" . StringUtils::formatNumber((int) $metal['avg']) . "</td>
                <td >" . StringUtils::formatNumber((int) $metal['cnt']) . "</td>
            </tr>";

        $crystal = $this->planetRepository->getMaxCrystal();
        $out .= "<tr>
                <td >" . ResourceNames::CRYSTAL . "</td>
                <td >" . StringUtils::formatNumber((int) $crystal['sum']) . "</td>
                <td >" . StringUtils::formatNumber((int) $crystal['avg']) . "</td>
                <td >" . StringUtils::formatNumber((int) $crystal['cnt']) . "</td>
            </tr>";

        $plastic = $this->planetRepository->getMaxPlastic();
        $out .= "<tr>
                <td >" . ResourceNames::PLASTIC . "</td>
                <td >" . StringUtils::formatNumber((int) $plastic['sum']) . "</td>
                <td >" . StringUtils::formatNumber((int) $plastic['avg']) . "</td>
                <td >" . StringUtils::formatNumber((int) $plastic['cnt']) . "</td>
            </tr>";

        $fuel = $this->planetRepository->getMaxFuel();
        $out .= "<tr>
                <td >" . ResourceNames::FUEL . "</td>
                <td >" . StringUtils::formatNumber((int) $fuel['sum']) . "</td>
                <td >" . StringUtils::formatNumber((int) $fuel['avg']) . "</td>
                <td >" . StringUtils::formatNumber((int) $fuel['cnt']) . "</td>
            </tr>";

        $food = $this->planetRepository->getMaxFood();
        $out .= "<tr>
                <td >" . ResourceNames::FOOD . "</td>
                <td >" . StringUtils::formatNumber((int) $food['sum']) . "</td>
                <td >" . StringUtils::formatNumber((int) $food['avg']) . "</td>
                <td >" . StringUtils::formatNumber((int) $food['cnt']) . "</td>
            </tr>";
        $out .= "</table>";

        return $out;
    }

    private function maxResourcesOfAPlayer(): string
    {
        $out = "<table width=\"100%\" class=\"tb\">";
        $out .= "<tr><th  colspan=\"3\">Max Ressourcen eines Spielers</th></tr>";
        $out .= "<tr>
                <td >" . ResourceNames::METAL . "</td>
                <td >" . StringUtils::formatNumber($this->planetRepository->getMaxMetalOfAPlayer()) . "</td>
            </tr>";
        $out .= "<tr>
                <td >" . ResourceNames::CRYSTAL . "</td>
                <td >" . StringUtils::formatNumber($this->planetRepository->getMaxCrystalOfAPlayer()) . "</td>
            </tr>";
        $out .= "<tr>
                <td >" . ResourceNames::PLASTIC . "</td>
                <td >" . StringUtils::formatNumber($this->planetRepository->getMaxPlasticOfAPlayer()) . "</td>
            </tr>";
        $out .= "<tr>
                <td >" . ResourceNames::FUEL . "</td>
                <td >" . StringUtils::formatNumber($this->planetRepository->getMaxFuelOfAPlayer()) . "</td>
            </tr>";
        $out .= "<tr>
                <td >" . ResourceNames::FOOD . "</td>
                <td >" . StringUtils::formatNumber($this->planetRepository->getMaxFoodOfAPlayer()) . "</td>
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
                <td>" . StringUtils::formatNumber($arr['max']) . "</td>
                <td>" . StringUtils::formatNumber($arr['cnt']) . "</td>
            </tr>";
            $rank++;
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td>&nbsp;</td>
            <td><b>" . StringUtils::formatNumber($total) . "</b></td>
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
                <td>" . StringUtils::formatNumber($arr['max']) . "</td>
                <td>" . StringUtils::formatNumber($arr['cnt']) . "</td>
            </tr>";
            $rank++;
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td>&nbsp;</td>
            <td><b>" . StringUtils::formatNumber($total) . "</b></td>
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
                <td>" . StringUtils::formatNumber($arr['cnt']) . "</td>
            </tr>";
            $rank++;
            $total += $arr['cnt'];
        }
        $out .= "<tr>
            <td colspan=\"2\"><b>Total</b></td>
            <td ><b>" . StringUtils::formatNumber($total) . "</b></td>
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
                <td>" . StringUtils::formatNumber($arr['max']) . "</td>
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
                <td >" . StringUtils::formatNumber($arr['max']) . "</td>
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
                <td>" . StringUtils::formatNumber($arr['level']) . "</td>
                <td>" . StringUtils::formatNumber($arr['exp']) . "</td>
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

        $out .= "<td style=\"vertical-align:top;\">";
        $out .= $this->designStats($limit);
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
            $out .= "<td>" . StringUtils::formatNumber($count) . "</td>";
            $out .= "<td>" . round(100 / $total * $count, 2) . "%</td></tr>";
        }
        $out .= "</table>";

        return $out;
    }
}
