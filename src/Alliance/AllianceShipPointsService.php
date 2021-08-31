<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;

class AllianceShipPointsService
{
    private ConfigurationService $config;
    private AllianceBuildingRepository $allianceBuildingRepository;
    private UserRepository $userRepository;

    public function __construct(ConfigurationService $config, AllianceBuildingRepository $allianceBuildingRepository, UserRepository $userRepository)
    {
        $this->config = $config;
        $this->allianceBuildingRepository = $allianceBuildingRepository;
        $this->userRepository = $userRepository;
    }

    public function update(): int
    {
        $shipyardLevels = $this->allianceBuildingRepository->getShipyardLevelsWhereNonNegativeResources();
        foreach ($shipyardLevels as $allianceId => $level) {
            // New exponential algorithm by river
            // NOTE: if changed, also change in content/alliance/base.inc.php
            $shipPointsAdd = (int) ceil($this->config->getInt("alliance_shippoints_per_hour") * $this->config->getFloat('alliance_shippoints_base') ** ($level - 1));

            $this->userRepository->addAllianceShipPoints($allianceId, $shipPointsAdd);
        }

        return count($shipyardLevels);
    }
}
