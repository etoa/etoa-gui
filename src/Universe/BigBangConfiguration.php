<?php declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\Configuration\ConfigurationService;

class BigBangConfiguration
{
    public int $numberOfSectors1;
    public int $numberOfSectors2;
    public int $numberOfCells1;
    public int $numberOfCells2;
    public int $starPercent;
    public int $asteroidPercent;
    public int $nebulaPercent;
    public int $wormholePercent;
    public int $wormholePersistentPercent;
    public int $emptySpacePercent;
    public int $numberOfPlanets1;
    public int $numberOfPlanets2;
    public int $solarSystemPlanetPercent;
    public int $solarSystemAsteroidPercent;
    public int $solarSystemEmptySpacePercent;
    public int $planetFields1;
    public int $planetFields2;

    public static function createFromConfig(ConfigurationService $config): BigBangConfiguration
    {
        $request = new BigBangConfiguration();
        $request->numberOfSectors1 = $config->param1Int('num_of_sectors');
        $request->numberOfSectors2 = $config->param2Int('num_of_sectors');
        $request->numberOfCells1 = $config->param1Int('num_of_cells');
        $request->numberOfCells2 = $config->param2Int('num_of_cells');
        $request->starPercent = $config->getInt('space_percent_solsys');
        $request->asteroidPercent = $config->getInt('space_percent_asteroids');
        $request->nebulaPercent = $config->getInt('space_percent_nebulas');
        $request->wormholePercent = $config->getInt('space_percent_wormholes');
        $request->wormholePersistentPercent = $config->getInt('persistent_wormholes_ratio');
        $request->emptySpacePercent = 0;
        $request->numberOfPlanets1 = $config->param1Int('num_planets');
        $request->numberOfPlanets2 = $config->param2Int('num_planets');
        $request->solarSystemPlanetPercent = $config->getInt('solsys_percent_planet');
        $request->solarSystemAsteroidPercent = $config->getInt('solsys_percent_asteroids');
        $request->solarSystemEmptySpacePercent = 0;
        $request->planetFields1 = $config->param1Int('planet_fields');
        $request->planetFields2 = $config->param2Int('planet_fields');

        return $request;
    }

    public function updateConfig(ConfigurationService $config): void
    {
        $config->set("num_of_sectors", "", $this->numberOfSectors1, $this->numberOfSectors2);
        $config->set("num_of_cells", "", $this->numberOfCells1, $this->numberOfCells2);
        $config->set("space_percent_solsys", $this->starPercent);
        $config->set("space_percent_asteroids", $this->asteroidPercent);
        $config->set("space_percent_nebulas", $this->nebulaPercent);
        $config->set("space_percent_wormholes", $this->wormholePercent);
        $config->set("persistent_wormholes_ratio", $this->wormholePersistentPercent);
        $config->set("num_planets", "", $this->numberOfPlanets1, $this->numberOfPlanets2);
        $config->set("solsys_percent_planet", $this->solarSystemPlanetPercent);
        $config->set("solsys_percent_asteroids", $this->solarSystemAsteroidPercent);
        $config->set("planet_fields", "", $this->planetFields1, $this->planetFields2);
    }
}
